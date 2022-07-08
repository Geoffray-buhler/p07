<?php

namespace App\Controller;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use DateTimeImmutable;
use App\Entity\UserClient;
use App\Repository\UserRepository;
use App\Repository\ProduitRepository;
use App\Repository\UserClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
{
    // Authorization
    public function __construct(UserRepository $userRepository, ProduitRepository $produitRepository, UserClientRepository $userClientRepository,SerializerInterface $serializer)
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
        return $this->redirectToRoute('app_apidocs');
    }

    #[Route('/apidocs', name: 'app_apidocs')]
    public function main()
    {
        $routes = [
            ["Definition"=>'Permet de ce connecté','path'=>'/api/connect','method'=>'POST','argument'=>'{"username":Votre nom,"password":Votre mot de passe}'],
            ["Definition"=>"Permet de voir les infos d'un utilisateur",'path'=>'/api/users','method'=>'GET','argument'=>''],
            ["Definition"=>"Permet de voir les details d'un client",'path'=>'/api/user/client/{idclient}','method'=>'GET','argument'=>''],
            ["Definition"=>"Permet de voir les infos des produits",'path'=>'/api/produits/','method'=>'GET','argument'=>''], 
            ["Definition"=>"Permet de voir les details d'un produits",'path'=>'/api/produit/{id}','method'=>'GET','argument'=>''],
            ["Definition"=>'Permet de supprimée un client','path'=>'/api/delete/user/{id}','method'=>'DELETE','argument'=>''],
            ["Definition"=>"Permet d'ajoutée un client",'path'=>'/api/add/user','method'=>'POST','argument'=>''],
        ];
        return $this->render('main/index.html.twig',['routes'=>$routes]);
    }

    #[Route('/api/connect', name: 'app_api_connect', methods: ['POST'])]
    public function connect(Request $request): Response
    {
        $data = $request->request->all();
        if ($this->getUser() !== null) {
            return new JsonResponse(
                ['message'=>'Vous etes deja connecté !'],
                301
            );
        }else{
            $user = $this->userRepository->findOneBy([ 'username' => $data['username'] ]);
            if (password_verify($data['password'],$user->getPassword())) {
                $issuedAt   = new DateTimeImmutable();
                $dataAuthorization = [
                    'iat'  => $issuedAt->getTimestamp(), // Issued at:  : heure à laquelle le jeton a été généré
                    'iss'  => $this->serverName,         // Émetteur
                    'nbf'  => $issuedAt->getTimestamp(), // Pas avant..
                    'userId' => $user->getId(),          // Id d'utilisateur
                ];
                return new JsonResponse(
                    'ok'
                    ,200
                    ,['Authorization'=>'Bearer '.JWT::encode(
                        $dataAuthorization,
                        $this->secretKey,
                        'HS512'
                    )]
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

    // Route pour afficher les utilisateur de l'api plus tous leur clients -- consulter la liste des utilisateurs inscrits liés à un client sur le site web ;
    #[Route('/api/user/me/clients', name: 'app_api_user_allclients', methods: ['GET'])]
    public function user(Request $request)
    {
        $res = $this->isLogged($request);
        if ($res) {
            $user = $res['user'];
            $offset = $request->query->get('offset');
            $page = $request->query->get('page');
            $clients = $this->userClientRepository->findOneBy(['user'=>$user],[$offset],[$page]);
            return new JsonResponse(
                ['user' => $this->Serializer->serialize($clients,JsonEncoder::FORMAT)],200,['Authorization'=>$res['jwt']]);
        }else{
            return new JsonResponse(
                ['message'=>$this->errormsg],
                403
            );
        }
    }

    // Route pour afficher un client en particulier d'un utilisateur de l'api -- consulter le détail d’un utilisateur inscrit lié à un client ;
    #[Route('/api/user/client/{idclient}', name: 'app_api_user_client', methods: ['GET'])]
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

    // Route pour afficher tous les produits -- consulter la liste des produits BileMo 
    #[Route('/api/produits/', name: 'app_api_produits', methods: ['GET'])]
    public function produitspagin(Request $request)
    {
        $res = $this->isLogged($request);
        if ($res) {
            $offset = $request->query->get('offset');
            $limit = $request->query->get('limit');
            $user = $res['user'];
            $produit = $this->produitRepository->findBy(['client'=>$user],null,$limit,$offset);
            return new JsonResponse([
                'users' => $this->Serializer->serialize($produit,JsonEncoder::FORMAT)],
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

    // Route pour afficher un produit -- consulter les détails d’un produit BileMo ;
    #[Route('/api/produit/{idproduct}', name: 'app_api_produit', methods: ['GET'])]
    public function produit($idproduct,Request $request)
    {
        $res = $this->isLogged($request);
        if ($res) {
            return new JsonResponse(
                ['users' => $this->Serializer->serialize($this->produitRepository->findOneBy(['id'=>$idproduct]),JsonEncoder::FORMAT)],
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
    #[Route('/api/delete/user/{iduser}', name: 'app_api_user', methods: ['DELETE'])]
    public function deleteUser(int $iduser,Request $request,EntityManagerInterface $entityManager )
    {
        $res = $this->isLogged($request);
        if ($res) {
            $user = $res['user'];
            $client = $this->userClientRepository->findOneBy(['User'=>$user,'id'=>$iduser]);
            if($client != null){
                $entityManager->remove($client);
                $entityManager->flush();
            }
            $this->isLogged($request,new JsonResponse(
                ['message' => "Client supprimée"],
                200
            ));
        }else{
            return new JsonResponse(
                ['message'=>$this->errormsg],
                403
            );
        }
    }

    #[Route('/api/add/user', name: 'app_api_user', methods: ['POST'])]
    public function addUser(Request $request,EntityManagerInterface $entityManager )
    {
        $responce = $this->isLogged($request);
        if ($responce) {
            $user = $responce['user'];
            $client = new UserClient;
            $data = $request->request->all();
            $client->setFirstname($data['firstname']);
            $client->setLastname($data['lastname']);
            $client->setUser($user);
            $entityManager->remove($client);
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
        // SWAGGER / openAPI C'est de la merde 
        if ($request->headers->has('Authorization')) {
            $jwt = str_replace('Bearer ','',$request->headers->get('Authorization'));
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