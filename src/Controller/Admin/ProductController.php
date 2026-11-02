<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

#[Route('/admin/products')]
final class ProductController extends AbstractController
{
    #[Route('', name: 'admin_product_index', methods: ['GET'])]
    public function index(
        Request $request,
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
    ): Response {
        // An empty ?category= (the "All categories" option) means no filter.
        $categoryId = $request->query->getInt('category') ?: null;

        // Filter by the related category's ID so selecting a category reliably
        // returns only products assigned to that category.
        $productsQuery = $productRepository->createQueryBuilder('product')
            ->innerJoin('product.category', 'category')
            ->addSelect('category')
            ->orderBy('product.name', 'ASC');

        if (null !== $categoryId) {
            $productsQuery
                ->andWhere('category.id = :categoryId')
                ->setParameter('categoryId', $categoryId);
        }

        return $this->render('product/index.html.twig', [
            'products' => $productsQuery->getQuery()->getResult(),
            'categories' => $categoryRepository->findBy([], ['name' => 'ASC']),
            'selected_category' => $categoryId,
        ]);
    }

    #[Route('/new', name: 'admin_product_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        KernelInterface $kernel,
        SluggerInterface $slugger,
    ): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile instanceof UploadedFile) {
                $product->setImageFilename($this->storeProductImage($imageFile, $kernel, $slugger));
            }

            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('success', sprintf('Product "%s" was created.', $product->getName()));

            return $this->redirectToRoute('admin_product_index');
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_product_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_product_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Product $product,
        EntityManagerInterface $entityManager,
        KernelInterface $kernel,
        SluggerInterface $slugger,
    ): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            $oldImage = null;
            if ($imageFile instanceof UploadedFile) {
                $oldImage = $product->getImageFilename();
                $product->setImageFilename($this->storeProductImage($imageFile, $kernel, $slugger));
            }

            $entityManager->flush();
            $this->removeProductImage($oldImage, $kernel);

            $this->addFlash('success', sprintf('Product "%s" was updated.', $product->getName()));

            return $this->redirectToRoute('admin_product_index');
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_product_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(
        Request $request,
        Product $product,
        EntityManagerInterface $entityManager,
        KernelInterface $kernel,
    ): Response
    {
        if (!$this->isCsrfTokenValid('delete_product_'.$product->getId(), $request->getPayload()->getString('_token'))) {
            $this->addFlash('error', 'Invalid security token. Please try again.');

            return $this->redirectToRoute('admin_product_index');
        }

        $imageFilename = $product->getImageFilename();
        $entityManager->remove($product);
        $entityManager->flush();
        $this->removeProductImage($imageFilename, $kernel);

        $this->addFlash('success', 'Product was deleted.');

        return $this->redirectToRoute('admin_product_index');
    }

    private function storeProductImage(UploadedFile $image, KernelInterface $kernel, SluggerInterface $slugger): string
    {
        $uploadDirectory = $kernel->getProjectDir().'/public/uploads/products';
        if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0775, true) && !is_dir($uploadDirectory)) {
            throw new \RuntimeException(sprintf('Unable to create the product image directory: %s', $uploadDirectory));
        }

        $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
        $safeName = (string) $slugger->slug($originalName);
        $extension = $image->guessExtension() ?: 'jpg';
        $newFilename = ($safeName ?: 'product').'-'.bin2hex(random_bytes(6)).'.'.$extension;

        $image->move($uploadDirectory, $newFilename);

        return $newFilename;
    }

    private function removeProductImage(?string $filename, KernelInterface $kernel): void
    {
        if (null === $filename || '' === $filename || basename($filename) !== $filename) {
            return;
        }

        $path = $kernel->getProjectDir().'/public/uploads/products/'.$filename;
        if (is_file($path)) {
            unlink($path);
        }
    }
}
