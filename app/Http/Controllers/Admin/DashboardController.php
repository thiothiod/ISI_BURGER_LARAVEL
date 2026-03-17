<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        // Commandes en cours du jour
        $ordersEnCours = Order::whereDate('created_at', $today)
            ->whereIn('status', [Order::STATUS_EN_ATTENTE, Order::STATUS_EN_PREPARATION])
            ->count();

        // Commandes validées du jour
        $ordersValidees = Order::whereDate('created_at', $today)
            ->whereIn('status', [Order::STATUS_PRETE, Order::STATUS_PAYEE])
            ->count();

        // Recettes journalières
        $recettesJournalieres = Order::whereDate('paid_at', $today)
            ->where('status', Order::STATUS_PAYEE)
            ->sum('paid_amount');

        // Total produits
        $totalProducts = Product::notArchived()->count();

        // Commandes par mois (12 derniers mois) pour Chart.js
        $ordersParMois = Order::select(
                DB::raw('EXTRACT(YEAR FROM created_at) as year'),
                DB::raw('EXTRACT(MONTH FROM created_at) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                $months = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
                return [
                    'label' => $months[$item->month - 1] . ' ' . $item->year,
                    'total' => $item->total,
                ];
            });

        // Produits par catégorie pour Chart.js
        $produitsParCategorie = Category::withCount(['products' => function ($q) {
            $q->notArchived();
        }])->get()->map(fn($c) => [
            'label' => $c->name,
            'count' => $c->products_count,
        ]);

        // Dernières commandes
        $recentOrders = Order::with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'ordersEnCours',
            'ordersValidees',
            'recettesJournalieres',
            'totalProducts',
            'ordersParMois',
            'produitsParCategorie',
            'recentOrders'
        ));
    }
}