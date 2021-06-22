<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Annotations as OA;

class ApiProduct extends AbstractController
{
    //#[Route('/api/products', name: 'api_add_product', methods: ['post'])]
    /**
     * @Route("/api/products", name="api_add_products", methods={"POST"})
     * @OA\Post(
     *     tags={"Produits"}
     *)
     */
    public function addProduct(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager
    ): Response
    {
        $json = $request->getContent();
        $product = $serializer->deserialize($json, Product::class, 'json');
        $product->setAdded(new \DateTime);
        $product->setUpdated(new \DateTime);
        $entityManager->persist($product);
        $entityManager->flush();

        return $this->json($product, 201, [], []);
    }

    //#[Route('/api/products', name: 'api_get_products', methods: ['get'])]
    /**
     * @Route("/api/products", name="api_get_products", methods={"GET"})
     * @OA\Get (
     *     tags={"Produits"}
     *)
     */
    public function getProducts(ProductRepository $productRepository): Response
    {
        return $this->json($productRepository->findAll(), 200, [], []);
    }

    //#[Route('/api/products/{id}', name: 'api_update_product', methods: ['put'])]
    //#[ParamConverter ('product', class: 'App:Product')]
    /**
     * @Route("/api/products/{id}", name="api_update_products", methods={"PUT"})
     * @ParamConverter("product", class="App:Product")
     * @OA\Put(
     *     tags={"Produits"}
     *)
     */
    public function updateProduct(
        Product $product,
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager
    ): Response
    {
        $json = $request->getContent();
        $productDeserialized = $serializer->deserialize($json, Product::class, 'json');
        $productDeserialized->setId($product->getId());
        $productDeserialized->setUpdated(new \DateTime);
        $entityManager->flush();

        return $this->json($productDeserialized, 200, [], []);
    }

    //#[Route('/api/products/{id}', name: 'api_delete_product', methods: ['delete'])]
    //#[ParamConverter ('product', class: 'App:Product')]
    /**
     * @Route("/api/products/{id}", name="api_delete_products", methods={"delete"})
     * @ParamConverter("product", class="App:Product")
     * @OA\Delete(
     *     tags={"Produits"}
     *)
     */
    public function deleteProduct(
        Product $product,
        EntityManagerInterface $entityManager
    ): Response
    {
        $entityManager->remove($product);
        $entityManager->flush();

        return $this->json($product, 200, [], []);
    }
}