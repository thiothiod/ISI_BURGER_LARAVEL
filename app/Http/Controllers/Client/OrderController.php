<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Mail\NewOrderNotificationMail;
use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['items.product'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        $statusLabels = Order::statusLabels();

        return view('client.orders.index', compact('orders', 'statusLabels'));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
        $order->load(['items.product', 'user']);
        $statusLabels = Order::statusLabels();
        return view('client.orders.show', compact('order', 'statusLabels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items'       => 'required|array|min:1',
            'items.*.id'  => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1|max:50',
            'notes'       => 'nullable|string|max:500',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $totalAmount = 0;
                $orderItems  = [];

                foreach ($request->items as $item) {
                    $product = Product::lockForUpdate()->findOrFail($item['id']);

                    if (!$product->isAvailable()) {
                        throw new \Exception("Le produit '{$product->name}' n'est plus disponible.");
                    }

                    if ($product->stock < $item['qty']) {
                        throw new \Exception("Stock insuffisant pour '{$product->name}'. Stock : {$product->stock}");
                    }

                    $subtotal     = $product->price * $item['qty'];
                    $totalAmount += $subtotal;

                    $orderItems[] = [
                        'product_id' => $product->id,
                        'quantity'   => $item['qty'],
                        'unit_price' => $product->price,
                        'subtotal'   => $subtotal,
                    ];

                    $product->decrement('stock', $item['qty']);
                    if ($product->fresh()->stock === 0) {
                        $product->update(['status' => 'rupture']);
                    }
                }

                $order = Order::create([
                    'reference'    => Order::generateReference(),
                    'user_id'      => Auth::id(),
                    'status'       => Order::STATUS_EN_ATTENTE,
                    'total_amount' => $totalAmount,
                    'notes'        => $request->notes,
                ]);

                foreach ($orderItems as &$oi) {
                    $oi['order_id']   = $order->id;
                    $oi['created_at'] = now();
                    $oi['updated_at'] = now();
                }

                OrderItem::insert($orderItems);

                // Email confirmation au client
                try {
                    Mail::to(Auth::user()->email)
                        ->send(new OrderConfirmationMail($order->load('items.product')));
                    sleep(1);

                    // Notification au gestionnaire
                    $gestionnaires = User::role('gestionnaire')->get();
                    foreach ($gestionnaires as $gestionnaire) {
                        Mail::to($gestionnaire->email)
                            ->send(new NewOrderNotificationMail($order));
                        sleep(1);
                    }
                } catch (\Exception $e) {
                    // Email échoué mais commande créée quand même
                }
            });

            return redirect()->route('client.orders.index')
                ->with('success', 'Commande passée avec succès !');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}