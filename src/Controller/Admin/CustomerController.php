<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/customers')]
final class CustomerController extends AbstractController
{
    // Placeholder page. Once a Customer (or User) entity exists,
    // inject its repository here and pass the real customers to the template.
    #[Route('', name: 'admin_customer_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('customer/index.html.twig', [
            'customers' => [],
        ]);
    }
}
