<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\User;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use FOS\RestBundle\Controller\Annotations as FOS;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class ApiCompanyController extends AbstractFOSRestController
{
     /**
     * @FOS\Post("/api/companies", name="api_create_company")
     * @FOS\View(StatusCode = 201)
     * @ParamConverter("company", converter="fos_rest.request_body")
     * @OA\Post(
     *     path="/api/companies",
     *     tags={"Companie"},
     *     summary="Crée une nouvelle companie",
     *     description="Cette route crée une nouvelle companie",
     *     operationId="createCompany",
     * @OA\Response(
     *     response=201,
     *     description="Voici la fiche de la companie créée",
     *     @OA\JsonContent(ref="#/components/schemas/Company")
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
    public function addCompany(
        Company $company,
        EntityManagerInterface $entityManager,
        ConstraintViolationList $violations
    ): Company|Response
    {
        if(count($violations))  {
            return $this->handleView($this->view($violations, Response::HTTP_BAD_REQUEST));
        }
        $entityManager->persist($company);
        $entityManager->flush();

        return $company;
    }

    /**
     * @FOS\Get("/api/companies", name = "api_get_companies")
     * @OA\Get(
     *     path="/api/companies",
     *     tags={"Companie"},
     *     summary="Liste les companies utilisatrices de l'application BileMo",
     *     description="Cette route retourne l'ensemble des companies utilisatrices de l'application BileMo",
     *     operationId="getCompanies",
     * @OA\Response(
     *     response=200,
     *     description="Voici la liste des companies",
     *     @OA\JsonContent(ref="#/components/schemas/Company")
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
    public function getCompanies(CompanyRepository $companyRepository, CacheInterface $cache): Response|CompanyRepository
    {
        return $cache->get('result-companies', function (ItemInterface $item) use ($companyRepository) {
            $item->expiresAfter(3600);
            $this->handleView($this->view($companyRepository->findAll()));
            return $companyRepository;
        });
    }

    /**
     * @FOS\Get("/api/companies/{id}", name = "api_get_company", requirements = {"id"="\d+"})
     * @OA\Get(
     *     path="/api/companies/{id}",
     *     tags={"Companie"},
     *     summary="Donne la fiche d'une companie",
     *     description="Cette route retourne la fiche d'une companie",
     *     operationId="getCompany",
     * @OA\Response(
     *     response=200,
     *     description="Voici la la fiche de la companie demandée",
     *     @OA\JsonContent(ref="#/components/schemas/Company")
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
    public function getCompany(CompanyRepository $companyRepository, int $id): Response
    {
        return $this->handleView($this->view($companyRepository->find($id)));
    }

    /**
     * @FOS\Put ("/api/companies/{id}", name = "api_update_company", requirements = {"id"="\d+"})
     * @ParamConverter("company", converter="fos_rest.request_body")
     * @OA\Put(
     *     path="/api/companies/{id}",
     *     tags={"Companie"},
     *     summary="Met à jour la fiche d'une companie",
     *     description="Cette route met à jour le profil d'une companie",
     *     operationId="updateCompanyPerId",
     * @OA\Response(
     *     response=200,
     *     description="Voici la fiche de la companie demandée",
     *     @OA\JsonContent(ref="#/components/schemas/Company")
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
    public function updateCompany(
        Company $company,
        CompanyRepository $companyRepository,
        EntityManagerInterface $entityManager
    ): Company
    {
        $companyToFlush = $companyRepository->find($company->getId());

        if(!$companyToFlush){
            $entityManager->persist($company);
        }
        $companyToFlush->setId($company->getId());
        $companyToFlush->setName($company->getName());
        $companyToFlush->setAddress($company->getAddress());
        $companyToFlush->setSiret($company->getSiret());

        $entityManager->flush();

        return $companyToFlush;
    }

    /**
     * @FOS\Delete ("/api/companies/{id}", name = "api_delete_company", requirements = {"id"="\d+"})
     * @ParamConverter ("company", class="App:Company")
     * @OA\Delete(
     *     path="/api/companies/{id}",
     *     tags={"Companie"},
     *     summary="Supprime une companie",
     *     description="Cette route supprime le profil complet d'une companie",
     *     operationId="deleteCompanyPerId",
     * @OA\Response(
     *     response=200,
     *     description="Voici la fiche de la companie demandée",
     *     @OA\JsonContent(ref="#/components/schemas/Company")
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
    public function deleteCompany(
        Company $company,
        EntityManagerInterface $entityManager
    ): Response
    {
        $entityManager->remove($company);
        $entityManager->flush();

        return $this->handleView($this->view($company, 204));

    }
}