<?php

namespace App\Controller\Store;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'store_home', methods: ['GET'])]
    public function index(
        Request $request,
        CategoryRepository $categoryRepository,
        ProductRepository $productRepository,
    ): Response {
        $categories = $categoryRepository->findBy([], ['name' => 'ASC']);
        $requestedCategoryId = $request->query->getInt('category');
        $selectedCategory = $requestedCategoryId > 0
            ? $categoryRepository->find($requestedCategoryId)
            : null;

        $productsQuery = $productRepository->createQueryBuilder('product')
            ->innerJoin('product.category', 'category')
            ->addSelect('category')
            ->andWhere('product.stock > 0')
            ->orderBy('product.id', 'DESC')
            ->setMaxResults(8);

        if ($selectedCategory instanceof Category) {
            $productsQuery
                ->andWhere('product.category = :selectedCategory')
                ->setParameter('selectedCategory', $selectedCategory);
        }

        return $this->render('store/home.html.twig', [
            'categories' => $categories,
            'products' => $productsQuery->getQuery()->getResult(),
            'selected_category' => $selectedCategory,
        ]);
    }
}
