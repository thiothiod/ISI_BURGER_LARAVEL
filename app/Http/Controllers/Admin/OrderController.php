<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OrderReadyMail;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items.product'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('reference', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', fn($u) =>
                        $u->where('name', 'like', '%' . $request->search . '%')
                  );
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $orders       = $query->paginate(15);
        $statusLabels = Order::statusLabels();

        return view('admin.orders.index', compact('orders', 'statusLabels'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product']);
        $statusLabels = Order::statusLabels();
        return view('admin.orders.show', compact('order', 'statusLabels'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:en_attente,en_preparation,prete,payee,annulee',
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        if (in_array($order->status, [Order::STATUS_ANNULEE, Order::STATUS_PAYEE])) {
            return back()->with('error', 'Cette commande ne peut plus être modifiée.');
        }

        if ($newStatus === Order::STATUS_PAYEE && $order->status !== Order::STATUS_PRETE) {
            return back()->with('error', 'La commande doit être "Prête" avant d\'être payée.');
        }

        $data = ['status' => $newStatus];

        if ($newStatus === Order::STATUS_PAYEE) {
            $data['paid_at']     = now();
            $data['paid_amount'] = $order->total_amount;
        }

        $order->update($data);

        // Envoyer email + facture PDF quand commande est prête
        if ($newStatus === Order::STATUS_PRETE && $oldStatus !== Order::STATUS_PRETE) {
            Mail::to($order->user->email)
                ->send(new OrderReadyMail($order->load('items.product')));
        }

        return back()->with('success', 'Statut mis à jour avec succès !');
    }

    public function cancel(Order $order)
    {
        if (!$order->canBeCancelled()) {
            return back()->with('error', 'Cette commande ne peut pas être annulée.');
        }

        foreach ($order->items as $item) {
            $item->product->increment('stock', $item->quantity);
            if ($item->product->status === 'rupture' && $item->product->fresh()->stock > 0) {
                $item->product->update(['status' => 'disponible']);
            }
        }

        $order->update(['status' => Order::STATUS_ANNULEE]);

        return back()->with('success', 'Commande annulée et stocks restaurés.');
    }
}