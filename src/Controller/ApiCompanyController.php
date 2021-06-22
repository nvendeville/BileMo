<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\User;
use App\Repository\CompanyRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use FOS\RestBundle\Controller\Annotations as FOS;
use FOS\RestBundle\Controller\Annotations\View;

class ApiCompanyController extends AbstractFOSRestController
{
    #[Route('/api/companies/{id}/users', name: 'api_add_user_in_company', methods: ['post'])]
    #[ParamConverter('company', class: 'App:Company')]
    public function createUser(
        Request $request,
        Company $company,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $hasher
    ): Response
    {
        $json = $request->getContent();
        $user = $serializer->deserialize($json, User::class, 'json');
        $user->setPassword($hasher->hashPassword($user, $user->getPassword()));
        $company->addUser($user);
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json($user, 201, [], ['groups' => 'api_get']);
    }

    //#[Route('/api/companies', name: 'api_add_company', methods: ['post'])]
    /**
     * @Route("/api/companies", name="api_get_companies", methods={"POST"})
     * @OA\Post (
     *     tags={"Companie"}
     *)
     */
    public function addCompany(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager
    ): Response
    {
        $json = $request->getContent();
        $company = $serializer->deserialize($json, Company::class, 'json');
        $entityManager->persist($company);
        $entityManager->flush();

        return $this->json($company, 201, [], []);
    }

     /**
     * @FOS\Get("/api/companies/{id}/users", name="api_get_users_in_company", requirements = {"id"="\d+"})
     * @View
     * @OA\Get(
     *     path="/api/companies/{id}/users",
     *     tags={"Utilisateurs"},
     *     summary="Liste les utilisateurs d'une companie.",
     *     description="Cette route retourne l'ensemble des utilisateurs de l'application BileMo d'une companie en particuliers et sans leur mot de passe",
     *     operationId="getUsersPerCompany",
     *      @OA\Response(
     *          response=200,
     *          description="Voici la liste des utilisateurs de l'entreprise",
     *           ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid Request"
     *           ),
     *      @OA\Response(
     *          response=404,
     *          description="No Users found"
     *           ),
     *      @OA\Response(
     *          response=500,
     *          description="Unknown Error"
     *           ),
     *)
     */
    public function getUsersPerCompany(
        UserRepository $userRepository,
        Company $company
    ): Response
    {
        return $this->handleview($this->view($userRepository->find($company->getId()), 200));
    }

    #[Route('/api/companies', name: 'api_get_companies', methods: ['get'])]
    public function getCompanies(CompanyRepository $companyRepository): Response
    {
        return $this->json($companyRepository->findAll(), 200, [], ['groups' => 'api_get']);
    }

    #[Route('/api/companies/{id}/users/{user_id}', name: 'api_update_user_in_company', methods: ['put'])]
    #[Entity('user', expr: 'repository.find(user_id)')]
    public function updateUserInCompany(
        Company $company,
        User $user,
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager
    ): Response
    {
        $json = $request->getContent();
        $userDeserialized = $serializer->deserialize($json, User::class, 'json');
        $userDeserialized->setId($user->getId());
        $userDeserialized->setCompany($company);
        $entityManager->merge($userDeserialized);
        $entityManager->flush();

        return $this->json($userDeserialized, 200, [], ['groups' => 'api_get']);
    }

    #[Route('/api/companies/{id}', name: 'api_update_company', methods: ['put'])]
    #[ParamConverter ('company', class: 'App:Company')]
    public function updateCompany(
        Company $company,
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager
    ): Response
    {
        $json = $request->getContent();
        $companyDeserialized = $serializer->deserialize($json, Company::class, 'json');
        $companyDeserialized->setId($company->getId());
        $entityManager->flush();

        return $this->json($companyDeserialized, 200, [], []);
    }

    #[Route('/api/companies/{company_id}/users/{id}', name: 'api_delete_user_in_company', methods: ['delete'])]
    #[ParamConverter('user', class: 'App:User')]
    public function deleteUserInCompany(
        User $user,
        EntityManagerInterface $entityManager
    ): Response
    {
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->json($user, 200, [], ['groups' => 'api_get']);
    }

    #[Route('/api/companies/{id}', name: 'api_delete_company', methods: ['delete'])]
    #[ParamConverter('company', class: 'App:Company')]
    public function deleteCompany(
        Company $company,
        EntityManagerInterface $entityManager
    ): Response
    {
        $entityManager->remove($company);
        $entityManager->flush();

        return $this->json($company, 200, [], ['groups' => 'api_get']);
    }
}