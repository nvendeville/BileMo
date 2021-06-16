<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as SWG;

class ApiUser extends AbstractController
{
    #[Route('/users', name: 'api_get_users', methods: ['get'])]
    #[SWG\Get(['tagsddd' => ['Users'], 'path' => '/users', 'description' => 'Voici la liste de tous les utilisateurs'])]
    #[SWG\Response([
        'response'=> 200,
        'description' => 'Voici la liste de tous les utilisateurs']
    )]
    public function getUsers(UserRepository $userRepository): Response
    {
        return $this->json($userRepository->findAll(), 200, [], ['groups' => 'api_get']);
    }
}