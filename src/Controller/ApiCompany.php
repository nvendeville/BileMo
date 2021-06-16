<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\User;
use App\Repository\CompanyRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ApiCompany extends AbstractController
{
    #[Route('/companies/{id}/users', name: 'api_add_user_in_company', methods: ['post'])]
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

    #[Route('/companies', name: 'api_add_company', methods: ['post'])]
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

    #[Route('/companies/{id}/users', name: 'api_get_users_in_company', methods: ['get'])]
    #[ParamConverter('company', class: 'App:Company')]
    public function getUsersPerCompany(
        UserRepository $userRepository,
        Company $company
    ): Response
    {
        return $this->json($userRepository->find($company->getId()), 200, [], ['groups' => 'api_get']);
    }

    #[Route('/companies', name: 'api_get_companies', methods: ['get'])]
    public function getCompanies(CompanyRepository $companyRepository): Response
    {
        return $this->json($companyRepository->findAll(), 200, [], ['groups' => 'api_get']);
    }

    #[Route('/companies/{id}/users/{user_id}', name: 'api_update_user_in_company', methods: ['put'])]
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
        $entityManager->flush();

        return $this->json($userDeserialized, 200, [], ['groups' => 'api_get']);
    }

    #[Route('/companies/{id}', name: 'api_update_company', methods: ['put'])]
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

    #[Route('/companies/{company_id}/users/{id}', name: 'api_delete_user_in_company', methods: ['delete'])]
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

    #[Route('/companies/{id}', name: 'api_delete_company', methods: ['delete'])]
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