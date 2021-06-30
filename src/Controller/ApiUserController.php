<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\OffsetRepresentation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use OpenApi\Annotations as OA;
use FOS\RestBundle\Controller\Annotations as FOS;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class ApiUserController extends AbstractFOSRestController
{
    /**
     * @FOS\Post("/api/companies/{id}/users", name = "api_create_user_in_company", requirements = {"id"="\d+"})
     * @FOS\View(StatusCode = 201)
     * @ParamConverter("user", converter="fos_rest.request_body")
     * @OA\Post(
     *     path="/api/companies/{id}/users",
     *     tags={"Utilisateurs"},
     *     summary="Crée un nouvel utilisateur dans une companie donnée",
     *     description="Cette route crée un nouvel utilisateur dans une companie donnée",
     *     operationId="createUser",
     * @OA\Response(
     *     response=201,
     *     description="Voici la fiche de l'utilisateur créé",
     *     @OA\JsonContent(ref="#/components/schemas/User")
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
    public function createUser(
        Company $company,
        User $user,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $hasher,
        ConstraintViolationList $violations
    ): User|Response
    {
        if(count($violations))  {
            return $this->handleView($this->view($violations, Response::HTTP_BAD_REQUEST));
        }
        $user->setPassword($hasher->hashPassword($user, $user->getPassword()));
        $company->addUser($user);
        $entityManager->persist($user);
        $entityManager->flush();

        return $user;

    }

    /**
     * @FOS\Get("/api/users", name = "api_get_users")
     * @FOS\View(statusCode = 200)
     * @FOS\QueryParam(
     *     name="limit",
     *     requirements="\d+",
     *     default="3",
     *     description="Nombre maximum de user par page"
     * )
     * @FOS\QueryParam(
     *     name="offset",
     *     requirements="\d+",
     *     default="0",
     *     description="Pagination offset"
     * )
     * @OA\Get(
     *     path="/api/users",
     *     tags={"Utilisateurs"},
     *     summary="Liste tous les utilisateurs de l'application.",
     *     description="Cette route retourne l'ensemble des utilisateurs de l'application BileMo sans leur mot de passe",
     *     operationId="getUsers",
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
     *     description="No Route found"
     *      ),
     * @OA\Response(
     *     response=500,
     *     description="Server Error"
     *      ),
     * )
     */
    public function getUsers(CacheInterface $cache, UserRepository $userRepository, ParamFetcher $paramFetcher): mixed
    {
        return $cache->get('result-paginated-users', function(ItemInterface $item) use ($paramFetcher, $userRepository) {
            $item->expiresAfter(3600);
            $paginatedUsers = new OffsetRepresentation(
                new CollectionRepresentation(
                    $userRepository->findBy(
                        [],
                        [],
                        $paramFetcher->get('limit'),
                        $paramFetcher->get('offset')
                    )
                ),
                'api_get_users',
                array(),
                $paramFetcher->get('offset'),
                $paramFetcher->get('limit'),
                count($userRepository->findAll()),
                null,
                null,
                true
            );
            return $this->handleView($this->view($paginatedUsers, 200));
        });

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

    /**
     * @FOS\Get("/api/users/{id}", name = "api_get_user", requirements = {"id"="\d+"})
     * @OA\Get(
     *     path="/api/users/{id}",
     *     tags={"Utilisateurs"},
     *     summary="Donne la fiche détaillée d'un utilisateur",
     *     description="Cette route retourne le profil complet d'un utilisateur sans son mot de passe",
     *     operationId="getUserPerId",
     * @OA\Response(
     *     response=200,
     *     description="Voici la fiche de l'utilisateur demandé",
     *     @OA\JsonContent(ref="#/components/schemas/User")
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
    public function getUserPerId(UserRepository $userRepository, int $id): Response
    {
        return $this->handleView($this->view($userRepository->find($id), 200));
    }

    /**
     * @FOS\Put("/api/companies/{company_id}/users/{user_id}", name="api_update_user_in_company")
     * @FOS\View(StatusCode = 200)
     * @ParamConverter("user", converter="fos_rest.request_body", options={"id"= "user_id"})
     * @ParamConverter("company", class="App:Company", options={"id"= "company_id"})
     * @OA\Put(
     *     path="/api/companies/{company_id}/users/{user_id}",
     *     tags={"Utilisateurs"},
     *     summary="Met à jour la fiche d'un utilisateur",
     *     description="Cette route met à jour la fiche d'un utilisateur",
     *     operationId="updateUserInCompany",
     * @OA\Response(
     *     response=200,
     *     description="Voici la fiche mise à jour de l'utilisateur donné",
     *     @OA\JsonContent(ref="#/components/schemas/User")
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
    public function updateUserInCompany(
        Company $company,
        User $user,
        UserRepository $userRepository,
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $hasher
    ): Response
    {
        /*foreach ($data as $key => $value){
            if($key && !empty($value)) {
                $method = 'set'.ucfirst($key);
                $user->$method($value);
            }
        }*/

        $userToFlush = $userRepository->find($request->get('user_id'));

        if(!$userToFlush){
            $user->setPassword($hasher->hashPassword($user, $user->getPassword()));
            $company->addUser($user);
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->handleView($this->view($user, 200));


        }
        $userToFlush->setId($request->get('user_id'));
        $userToFlush->setEmail($user->getEmail());
        $userToFlush->setPassword($hasher->hashPassword($user, $user->getPassword()));
        $userToFlush->setCompany($company);
        $entityManager->flush();

        return $this->handleView($this->view($userToFlush, 200));

    }

    /**
     * @FOS\Put("/api/users/{id}", name="api_update_user")
     * @FOS\View(StatusCode = 200)
     * @ParamConverter("user", converter="fos_rest.request_body")
     * @OA\Put(
     *     path="/api/users/{id}",
     *     tags={"Utilisateurs"},
     *     summary="Met à jour la fiche d'un utilisateur",
     *     description="Cette route met à jour la fiche d'un utilisateur",
     *     operationId="updateUser",
     * @OA\Response(
     *     response=200,
     *     description="Voici la fiche mise à jour de l'utilisateur donné",
     *     @OA\JsonContent(ref="#/components/schemas/User")
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
    public function updateUser(
        User $user,
        UserRepository $userRepository,
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $hasher
    ): Response
    {
        /*foreach ($data as $key => $value){
            if($key && !empty($value)) {
                $method = 'set'.ucfirst($key);
                $user->$method($value);
            }
        }*/

        $userToFlush = $userRepository->find($request->get('id'));

        if(!$userToFlush){
            $user->setPassword($hasher->hashPassword($user, $user->getPassword()));
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->handleView($this->view($user, 200));


        }
        $userToFlush->setId($request->get('user_id'));
        $userToFlush->setEmail($user->getEmail());
        $userToFlush->setPassword($hasher->hashPassword($user, $user->getPassword()));
        $entityManager->flush();

        return $this->handleView($this->view($userToFlush, 200));

    }

    /**
     * @FOS\Delete ("/api/users/{id}", name = "api_delete_user", requirements = {"id"="\d+"})
     * @ParamConverter ("user", class="App:User")
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     tags={"Utilisateurs"},
     *     summary="Supprime un utilisateur",
     *     description="Cette route supprime le profil complet d'un utilisateur",
     *     operationId="deleteUserPerId",
     * @OA\Response(
     *     response=204,
     *     description="Utilisateur supprimé",
     *     @OA\JsonContent(ref="#/components/schemas/User")
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
    public function deleteUserPerId(
        User $user,
        EntityManagerInterface $entityManager
    ): Response
    {
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->handleView($this->view($user, 204));


    }

    /**
     * @FOS\Delete("/api/companies/{company_id}/users/{user_id}", name="api_delete_user_in_company")
     * @FOS\View(StatusCode = 200)
     * @ParamConverter("user", class="App:User", options={"id"= "user_id"})
     * @ParamConverter("company", class="App:Company", options={"id"= "company_id"})
     * @OA\Delete(
     *     path="/api/companies/{company_id}/users/{user_id}",
     *     tags={"Utilisateurs"},
     *     summary="Supprime la fiche d'un utilisateur",
     *     description="Cette route supprime la fiche d'un utilisateur",
     *     operationId="deleteUserInCompany",
     * @OA\Response(
     *     response=204,
     *     description="Utilisateur supprimé",
     *     @OA\JsonContent(ref="#/components/schemas/User")
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
    public function deleteUserInCompany(
        User $user,
        EntityManagerInterface $entityManager
    ): Response
    {
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->handleView($this->view($user, 204));

    }
}
