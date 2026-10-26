<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/orders')]
final class OrderController extends AbstractController
{
    // Placeholder page. Once the Order / OrderItem entities exist,
    // inject OrderRepository here and pass the real orders to the template.
    #[Route('', name: 'admin_order_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('order/index.html.twig', [
            'orders' => [],
        ]);
    }
}
