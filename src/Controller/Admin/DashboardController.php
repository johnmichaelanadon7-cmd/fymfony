<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    #[Route('/admin', name: 'admin_home', methods: ['GET'])]
    public function home(): RedirectResponse
    {
        return $this->redirectToRoute('admin_dashboard');
    }

    #[Route('/admin/dashboard', name: 'admin_dashboard', methods: ['GET'])]
    public function index(): Response
    {
        // Demo figures only. Replace these arrays with database queries later.
        $summary = [
            [
                'title' => 'Sales Today',
                'value' => '₱8,450.00',
                'note' => '12.5% higher than yesterday',
                'symbol' => '₱',
                'class' => 'orange',
            ],
            [
                'title' => 'Orders Today',
                'value' => '24',
                'note' => '4 more than yesterday',
                'symbol' => '≡',
                'class' => 'blue',
            ],
            [
                'title' => 'Products Listed',
                'value' => '86',
                'note' => 'Across all categories',
                'symbol' => '◇',
                'class' => 'green',
            ],
            [
                'title' => 'Low Stock Alerts',
                'value' => '5',
                'note' => 'Items need attention',
                'symbol' => '!',
                'class' => 'red',
            ],
        ];

        $weeklySales = [
            ['day' => 'Mon', 'amount' => '₱4.2k', 'height' => 40],
            ['day' => 'Tue', 'amount' => '₱5.6k', 'height' => 53],
            ['day' => 'Wed', 'amount' => '₱3.9k', 'height' => 37],
            ['day' => 'Thu', 'amount' => '₱6.8k', 'height' => 65],
            ['day' => 'Fri', 'amount' => '₱5.1k', 'height' => 49],
            ['day' => 'Sat', 'amount' => '₱8.4k', 'height' => 86],
            ['day' => 'Sun', 'amount' => '₱6.2k', 'height' => 59],
        ];

        $recentOrders = [
            ['id' => 'HB-1008', 'customer' => 'Maria Santos', 'date' => 'Oct 15, 2026', 'items' => 3, 'total' => '₱560.00', 'status' => 'Completed'],
            ['id' => 'HB-1007', 'customer' => 'Joshua Reyes', 'date' => 'Oct 15, 2026', 'items' => 2, 'total' => '₱320.00', 'status' => 'Processing'],
            ['id' => 'HB-1006', 'customer' => 'Angela Cruz', 'date' => 'Oct 15, 2026', 'items' => 5, 'total' => '₱845.00', 'status' => 'Pending'],
            ['id' => 'HB-1005', 'customer' => 'Daniel Garcia', 'date' => 'Oct 14, 2026', 'items' => 4, 'total' => '₱690.00', 'status' => 'Completed'],
            ['id' => 'HB-1004', 'customer' => 'Sofia Mendoza', 'date' => 'Oct 14, 2026', 'items' => 1, 'total' => '₱120.00', 'status' => 'Cancelled'],
        ];

        $lowStockItems = [
            ['name' => 'Butter Croissant', 'category' => 'Pastries', 'stock' => 3, 'threshold' => 10],
            ['name' => 'Sourdough Loaf', 'category' => 'Bread', 'stock' => 4, 'threshold' => 12],
            ['name' => 'Chocolate Muffin', 'category' => 'Pastries', 'stock' => 6, 'threshold' => 15],
        ];

        return $this->render('dashboard/index.html.twig', [
            'summary' => $summary,
            'weekly_sales' => $weeklySales,
            'recent_orders' => $recentOrders,
            'low_stock_items' => $lowStockItems,
        ]);
    }
}
