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
        //latest permet de trier les commandes par date de création, de la plus récente à la plus ancienne, pour que les commandes les plus récentes soient affichées en premier dans la liste.

        if ($request->filled('status')) {
            //filled vérifie si le champ "status" est présent dans la requête et n'est pas vide. Si c'est le cas, on ajoute une condition à la requête pour filtrer les commandes par statut.
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

    // la méthode show affiche les détails d'une commande spécifique, 
    //y compris les informations sur l'utilisateur et les produits commandés. 
    //La méthode updateStatus permet de mettre à jour le statut d'une commande, 
    //avec des validations pour éviter les changements de statut invalides. 
    //La méthode cancel annule une commande et restaure les stocks des produits associés.
    public function show(Order $order)
    {
        $order->load(['user', 'items.product']);
        $statusLabels = Order::statusLabels();
        return view('admin.orders.show', compact('order', 'statusLabels'));
    }

  public function updateStatus(Request $request, Order $order)
{
    $request->validate([
        'status'      => 'required|in:en_attente,en_preparation,prete,payee,annulee',
        'paid_amount' => 'nullable|numeric|min:0',
    ]);

    $oldStatus = $order->status;
    $newStatus = $request->status;

    //order permet de vérifier si la commande est déjà annulée ou payée. Si c'est le cas, on empêche toute modification du statut et on retourne un message d'erreur à l'utilisateur.
    //back permet de rediriger l'utilisateur vers la page précédente, généralement la page de détails de la commande, avec un message d'erreur indiquant que le statut ne peut pas être modifié.
    if (in_array($order->status, [Order::STATUS_ANNULEE, Order::STATUS_PAYEE])) {
        return back()->with('error', 'Cette commande ne peut plus être modifiée.');
    }

    if ($newStatus === Order::STATUS_PAYEE && $order->status !== Order::STATUS_PRETE) {
        return back()->with('error', 'La commande doit être "Prête" avant d\'être payée.');
    }

    // Vérification du montant saisi
    if ($newStatus === Order::STATUS_PAYEE) {
        $montantSaisi = floatval($request->paid_amount);
        $montantAttendu = floatval($order->total_amount);

        if ($montantSaisi != $montantAttendu) {
            return back()->with('error',
                "❌ Montant incorrect ! Montant saisi : " . number_format($montantSaisi, 0, ',', ' ') . " F. " .
                "Montant attendu : " . number_format($montantAttendu, 0, ',', ' ') . " F."
            );
        }
    }

    $data = ['status' => $newStatus];

    if ($newStatus === Order::STATUS_PAYEE) {
        $data['paid_at']     = now();
        $data['paid_amount'] = $request->paid_amount;
    }

    $order->update($data);

    // Envoyer email + facture PDF quand commande est prête
    if ($newStatus === Order::STATUS_PRETE && $oldStatus !== Order::STATUS_PRETE) {
        try {
            Mail::to($order->user->email)
                ->send(new OrderReadyMail($order->load('items.product')));
        } catch (\Exception $e) {
            // Email échoué mais statut mis à jour
        }
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