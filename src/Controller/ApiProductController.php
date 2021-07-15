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
use FOS\RestBundle\Controller\Annotations as FOS;
use FOS\RestBundle\Controller\Annotations\View;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\ConstraintViolationList;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\OffsetRepresentation;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class ApiProductController extends AbstractFOSRestController
{
    /**
     * @FOS\Post ("/api/products", name="api_create_product", methods={"POST"})
     * @FOS\View(StatusCode = 201)
     * @IsGranted ("ROLE_SUPER_ADMIN", message="Vous n'avez pas l'autorisation de créer un nouveau produit")
     * @ParamConverter("product", converter="fos_rest.request_body")
     * @OA\Post(
     *     path="/api/products",
     *     tags={"Produits"},
     *     summary="Crée un nouveau produit dans le catalogue",
     *     description="Cette route crée un nouveau produit dans le catalogue",
     *     operationId="createProduct",
     * @OA\Response(
     *     response=201,
     *     description="Voici la fiche du produit créé",
     *     @OA\JsonContent(ref="#/components/schemas/Product")
     *      ),
     * @OA\Response(
     *     response=400,
     *     description="Invalid Request"
     *      ),
     * @OA\Response(
     *     response=404,
     *     description="No Route found"
     *      ),
     * @OA\Response(
     *     response=500,
     *     description="Server Error"
     *      ),
     * )
     */
    public function createProduct(
        Product $product,
        EntityManagerInterface $entityManager,
        ConstraintViolationList $violations
    ): Response|Product {
        if (count($violations)) {
            return $this->handleView($this->view($violations, Response::HTTP_BAD_REQUEST));
        }
        $entityManager->persist($product);
        $entityManager->flush();

        return $product;
    }

    /**
     * @FOS\Get("/api/products", name = "api_get_products")
     * @FOS\View(statusCode = 200)
     * @FOS\QueryParam(
     *     name="limit",
     *     requirements="\d+",
     *     default="3",
     *     description="Nombre maximum de produits par page"
     * )
     * @FOS\QueryParam(
     *     name="offset",
     *     requirements="\d+",
     *     default="0",
     *     description="Pagination offset"
     * )
     * @OA\Get(
     *     path="/api/products",
     *     tags={"Produits"},
     *     summary="Liste tous les produits du catalogue",
     *     description="Cette route retourne l'ensemble des produits du catalogue BileMo",
     *     operationId="getProducts",
     * @OA\Response(
     *     response=200,
     *     description="Voici la liste de tous les produits",
     *     @OA\JsonContent(ref="#/components/schemas/Product")
     *      ),
     * @OA\Response(
     *     response=400,
     *     description="Invalid Request"
     *      ),
     * @OA\Response(
     *     response=404,
     *     description="No Route found"
     *      ),
     * @OA\Response(
     *     response=500,
     *     description="Server Error"
     *      ),
     * )
     */
    public function getProducts(
        CacheInterface $cache,
        ProductRepository $productRepository,
        ParamFetcher $paramFetcher
    ): Response {
        return $cache->get(
            'result-paginated-products' . '/' . $paramFetcher->get('limit') . '/' . $paramFetcher->get('offset'),
            function (ItemInterface $item) use ($paramFetcher, $productRepository) {
                $item->expiresAfter(3600);
                $paginatedProducts = new OffsetRepresentation(
                    new CollectionRepresentation(
                        $productRepository->findBy(
                            [],
                            [],
                            $paramFetcher->get('limit'),
                            $paramFetcher->get('offset')
                        )
                    ),
                    'api_get_products',
                    array(),
                    $paramFetcher->get('offset'),
                    $paramFetcher->get('limit'),
                    count($productRepository->findAll()),
                    null,
                    null,
                    true
                );
                return $this->handleView($this->view($paginatedProducts, 200));
            }
        );
    }

    /**
     * @FOS\Put("/api/products/{id}", name="api_update_product")
     * @FOS\View(StatusCode = 200)
     * @ParamConverter("product", converter="fos_rest.request_body")
     * @IsGranted ("ROLE_SUPER_ADMIN", message="Vous n'avez pas l'autorisation de modifier un produit")
     * @OA\Put(
     *     path="/api/products/{id}",
     *     tags={"Produits"},
     *     summary="Met à jour la fiche d'un produit",
     *     description="Cette route met à jour la fiche d'un produit",
     *     operationId="updateProduct",
     * @OA\Response(
     *     response=200,
     *     description="Voici la fiche mise à jour du produit donné",
     *     @OA\JsonContent(ref="#/components/schemas/Product")
     *      ),
     * @OA\Response(
     *     response=400,
     *     description="Invalid Request"
     *      ),
     * @OA\Response(
     *     response=404,
     *     description="No Route found"
     *      ),
     * @OA\Response(
     *     response=500,
     *     description="Server Error"
     *      ),
     * )
     */
    public function updateProduct(
        Product $product,
        Request $request,
        ProductRepository $productRepository,
        EntityManagerInterface $entityManager
    ): Product {
        $productToFlush = $productRepository->find($request->get('id'));

        if (!$productToFlush) {
            $entityManager->persist($product);
            $entityManager->flush();

            return $product;
        }
        $productToFlush->setId($request->get('id'));
        $productToFlush->setName($product->getName());
        $productToFlush->setDescription($product->getDescription());
        $productToFlush->setReference($product->getReference());
        $productToFlush->setUpdated(new \DateTime('now'));
        $productToFlush->setPrice($product->getPrice());
        $entityManager->flush();

        return $productToFlush;
    }

    /**
     * @FOS\Delete ("/api/products/{id}", name = "api_delete_product", requirements = {"id"="\d+"})
     * @ParamConverter ("product", class="App:Product")
     * @IsGranted ("ROLE_SUPER_ADMIN", message="Vous n'avez pas l'autorisation de supprimer un produit")
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     tags={"Produits"},
     *     summary="Supprime un produit",
     *     description="Cette route supprime un produit",
     *     operationId="deleteProduct",
     * @OA\Response(
     *     response=204,
     *     description="produit supprimé",
     *     @OA\JsonContent(ref="#/components/schemas/Product")
     *      ),
     * @OA\Response(
     *     response=400,
     *     description="Invalid Request"
     *      ),
     * @OA\Response(
     *     response=404,
     *     description="No Route found"
     *      ),
     * @OA\Response(
     *     response=500,
     *     description="Server Error"
     *      ),
     * )
     */
    public function deleteProduct(
        Product $product,
        EntityManagerInterface $entityManager
    ): Response {
        $entityManager->remove($product);
        $entityManager->flush();

        return $this->handleView($this->view($product, 204));
    }
}
