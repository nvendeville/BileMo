<?php

namespace App\Controller;

use App\Repository\UserRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Annotations as OA;
use FOS\RestBundle\Controller\Annotations as FOS;

class ApiUserController extends AbstractFOSRestController
{
    /**
     * @FOS\Get("/api/users", name = "api_get_users")
     * @FOS\View(statusCode = 200, serializerGroups = {""})
     * @OA\Get(
     *     path="/api/users",
     *     tags={"Utilisateurs"},
     *     summary="Liste tous les utilisateurs de l'application.",
     *     description="Cette route retourne l'ensemble des utilisateurs de l'application BileMo sans leur mot de passe",
     *     operationId="getUser",
     * @OA\Response(
     *     response=200,
     *     description="Voici la liste de tous les utilisateurs",
     *     @OA\JsonContent(ref="#/components/schemas/User")
     *      ),
     * @OA\Response(
     *     response=400,
     *     description="Invalid Request"
     *      ),
     * @OA\Response(
     *     response=404,
     *     description="No Users found"
     *      ),
     * @OA\Response(
     *     response=500,
     *     description="Unknown Error"
     *      ),
     * )
     */
    public function getUsers(UserRepository $userRepository): Response
    {
        return $this->handleView($this->view($userRepository->findAll(), 200));
    }
}