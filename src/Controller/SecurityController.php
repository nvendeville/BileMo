<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use OpenApi\Annotations as OA;


class SecurityController extends AbstractController
{

    /**
     * @Route(name="api_login", path="/api/login_check", methods={"POST"})
     * @OA\Post(
     *     path="/api/login_check",
     *     tags={"Authentification"},
     *     summary="Permet de s'authentifier en fournissant email et mot de passe",
     *     description="Cette route permet de s'authentifier pour utiliser les API",
     *     operationId="loginCheck"
     * )
     */
    public function apiLogin(): JsonResponse
    {
        $user = $this->getUser();

        return new JsonResponse([
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ]);
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     */
    public function logout(): \Exception
    {
        // controller can be blank: it will never be executed!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}
