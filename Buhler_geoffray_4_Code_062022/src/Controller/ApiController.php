<?php

namespace App\Controller;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Entity\Client;
use DateTimeImmutable;
use OpenApi\Attributes as OA;
use App\Repository\UserRepository;
use App\Repository\ClientRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
{

    // Authorization.
    public function __construct(UserRepository $userRepository, ProduitRepository $produitRepository, ClientRepository $userClientRepository,SerializerInterface $serializer)
    {
        $this->userRepository = $userRepository;
        $this->produitRepository = $produitRepository;
        $this->userClientRepository = $userClientRepository;
        $this->errormsg = 'Vous n\'etes pas connecte !';
        $this->secretKey  = 'bGS6lzFqvvSQ8ALbOxatm7/Vk7mLQyzqaS34Q4oR1ew=';
        $this->serverName = "Api";
        $this->LogedIn = false;
        $this->Serializer = $serializer;
    }

    #[Route('/', name: 'app')]
    public function redirecttomain()
    {
        return $this->redirect('/doc');
    }

    // consulté la liste des utilisateur inscrit sur un client. suppréssion, PSR a connaitre.
    #[Route('/api/connect', name: 'app_api_connect', methods: ['POST'])]
    #[OA\Tag(name: 'Connexion')]
    #[OA\Response(
        response: 201,
        description: 'Returns the user infomations and token',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items()
        )
    )]
    #[OA\RequestBody(
        content: new OA\JsonContent(title :"data",example:'{"password":"test","username":"test"}'),
        required: true,
        description:"Demande Password et username en json",
    )]
    #[OA\Response(response: 403,description: 'Wrong user information')]
    #[OA\Response(response: 404,description: 'User not found')]
    #[OA\Response(response: 500,description: 'ERROR')]
    #[Security(name: 'Bearer')]
        
    public function connect(Request $request): Response
    {
        $data = $request->getContent();
        $datadecoded = json_decode($data,true);

        if ($this->getUser() !== null) {
            return new JsonResponse(
                ['message'=>'Vous etes deja connecté !'],
                301
            );
        }else{
            $user = $this->userRepository->findOneBy(['username'=>$datadecoded['username']]);
            if (password_verify($datadecoded['password'],$user->getPassword())) {
                $issuedAt = new DateTimeImmutable();
                $dataAuthorization = [
                    'iat'  => $issuedAt->getTimestamp(), // Issued at:  : heure à laquelle le jeton a été généré
                    'iss'  => $this->serverName,         // Émetteur
                    'nbf'  => $issuedAt->getTimestamp(), // Pas avant..
                    'userId' => $user->getId(),          // Id d'utilisateur
                ];
                return new JsonResponse(
                    ['token' => 'Bearer '.JWT::encode(
                        $dataAuthorization,
                        $this->secretKey,
                        'HS512'
                    )]
                    ,201
                );
            }else{
                return new JsonResponse(
                    ['message' => 'Mauvais mot de passe !'],
                    403
                );
            }
            if (empty($user)) {
                return new JsonResponse(
                    ['message' => 'Cet utilisateur n\'existe pas !'],
                    404
                );
            }
        }
    }

    // Route pour afficher les utilisateur de l'api plus tous leur clients -- consulter la liste des utilisateurs inscrits liés à un client sur le site web.
    #[Route('/api/user/{iduser}/clients', name: 'app_api_user_allclients', methods: ['GET'])]
    #[OA\Tag(name: 'User')]
    public function user(Request $request)
    {
        $res = $this->isLogged($request);
        
        if ($res) {
            $user = $res['user'];
            try {
                $clients = $this->userClientRepository->findBy(['user'=>$user]);
            } catch (\Throwable $th) {
                return new JsonResponse([
                    'message' => 'il y a aucun client en BDD !'
                ],404,[
                    'Authorization'=>$res['jwt']
                ]);
            }
            return new JsonResponse([
                'user' => $this->Serializer->serialize($clients,JsonEncoder::FORMAT)
            ],200,[
                'Authorization'=>$res['jwt']
            ]);
        }else{
            return new JsonResponse(
                ['message'=>$this->errormsg],
                403
            );
        }
    }

    // Route pour afficher un client en particulier d'un utilisateur de l'api -- consulter le détail d’un utilisateur inscrit lié à un client.
    #[Route('/api/user/{iduser}/client/{idclient}', name: 'app_api_user_client', methods: ['GET'])]
    #[OA\Tag(name: 'User')]
    public function userclient(int $idclient,Request $request)
    {
        $res = $this->isLogged($request);
        if ($res) {
            $user = $res['user'];
            $client = $this->userClientRepository->findOneBy(['User'=>$user,'id'=>$idclient]);
            return new JsonResponse([
                'client' => $this->Serializer->serialize($client,JsonEncoder::FORMAT)],
                200,
                ['Authorization'=>$res['jwt']]
            );
        }else{
            return new JsonResponse(
                ['message'=>$this->errormsg],
                403
            );
        }
    }

    // Route pour afficher tous les produits -- consulter la liste des produits BileMo.
    #[Route('/api/produits/all', name: 'app_api_produits', methods: ['GET'])]
    #[OA\Tag(name: 'Product')]
    public function produitspagin(Request $request)
    {
        $res = $this->isLogged($request);
        if ($res) {
            $offset = $request->query->get('offset');
            $limit = $request->query->get('limit');
            $produit = $this->produitRepository->findBy([],null,$limit,$offset);
            return new JsonResponse([
                'Produits' => $this->Serializer->serialize($produit,JsonEncoder::FORMAT)],
                200,
                ['Authorization'=>$res['jwt']]
            );
        }else{
            return new JsonResponse(
                ['message'=>$this->errormsg],
                403
            );
        }
    }

    // Route pour afficher un produit -- consulter les détails d’un produit BileMo.
    #[Route('/api/produit/{idproduct}', name: 'app_api_produit', methods: ['GET'])]
    #[OA\Tag(name: 'Product')]
    public function produit($idproduct,Request $request)
    {
        $res = $this->isLogged($request);
        if ($res) {
            return new JsonResponse(
                ['Produits' => $this->Serializer->serialize($this->produitRepository->findOneBy(['id'=>$idproduct]),JsonEncoder::FORMAT)],
                200,
                ['Authorization'=>$res['jwt']]
            );
        }else{
            return new JsonResponse(
                ['message'=>$this->errormsg],
                403
            );
        }
    }

    // Route pour supprimer un utilisateur de l'api -- supprimer un utilisateur ajouté par un client.
    #[Route('/api/user/{iduser}/delete/client/{idclient}', name: 'app_api_delete_client', methods: ['DELETE'])]
    #[OA\Tag(name: 'User')]
    public function deleteUser(int $iduser,Request $request,EntityManagerInterface $entityManager )
    {
        $res = $this->isLogged($request);
        if ($res) {
            $user = $res['user'];
            $client = $this->userClientRepository->findOneBy(['User'=>$user,'id'=>$iduser]);
            if (in_array("ROLE_ADMIN",$user->getRoles())) {
                $client = $this->userClientRepository->findOneBy(['id'=>$iduser]);
            }
            if($client != null){
                $entityManager->remove($client);
                $entityManager->flush();
            }
            $this->isLogged($request,new JsonResponse(
                ['message' => "Client supprimée"],
                204
            ));
        }else{
            return new JsonResponse(
                ['message'=>$this->errormsg],
                403
            );
        }
    }

    // Route pour ajouter un utilisateur de l'api -- ajouter un utilisateur a un client.
    #[Route('/api/user/{iduser}/add/client', name: 'app_api_client', methods: ['POST'])]
    #[OA\Tag(name: 'User')]
    #[OA\RequestBody(
        content: new OA\JsonContent(title :"data",example:'{"firstname":"test","lastname":"test","Phone Number":"00.00.00.00.00"}'),
        required: true,
        description:"Demande du firstname et lastname en json",
    )]
    public function addUser(Request $request,EntityManagerInterface $entityManager )
    {
        $responce = $this->isLogged($request);
        if ($responce) {
            $user = $responce['user'];
            $client = new Client;
            $data = $request->getContent();
            $datadecoded = json_decode($data,true);
            $client->setFirstname($datadecoded['firstname']);
            $client->setLastname($datadecoded['lastname']);
            $client->setPhoneNumber($datadecoded['lastname']);
            $client->addUser($user);
            $entityManager->persist($client);
            $entityManager->flush();
            return new JsonResponse(
                ['message' => "client ajoutée"],
                200
            );
        }else{
            return new JsonResponse(
                ['message'=>$this->errormsg],
                403
            );
        }
    }

    private function isLogged(Request $request)
    {
        if ($request->headers->has('Authorization')) {
            $jwt = str_replace('Bearer ', '', $request->headers->get('Authorization'));
            $jwtdecoded = $this->decodeJWT($jwt);
            $iduser = $jwtdecoded->userId;
            $user = $this->userRepository->findOneBy(['id'=>$iduser]);
            return ['user'=>$user,'jwt'=>$request->headers->get('Authorization')];
        }else{
            return false;
        }
    }

    private function decodeJWT($jwt)
    {
        return JWT::decode($jwt,new Key($this->secretKey,'HS512'));
    }
}