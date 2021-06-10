<?php

namespace App\Controller;

use App\Entity\Company;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class UserApi extends AbstractController
{
    #[Route('/api/user', name: 'api_user', methods: ['get'])]
    public function getUsers(UserRepository $userRepository): Response
    {
        //normalizer puis json_encode
        //$json = $serializer->serialize($users, 'json', ['groups' => 'api_get']);
        //return new JsonResponse($json, 200, [], true);

        return $this->json($userRepository->findAll(), 200, [], ['groups' => 'api_get']);
    }

    #[Route('/api/{id}/user', name: 'api_user_create', methods: ['post'])]
    #[ParamConverter('company', class: 'App:Company')]
    public function createUser(
        Request $request,
        Company $company,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager
    ): Response
    {
        $json = $request->getContent();
        $user = $serializer->deserialize($json, \App\Entity\User::class, 'json');
        $company->addUser($user);
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json($user, 201, [], ['groups' => 'api_get']);
    }
}