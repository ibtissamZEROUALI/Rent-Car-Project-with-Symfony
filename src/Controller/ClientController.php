<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Client;
use App\Repository\ClientRepository;
use App\Repository\RoleListRepository;
use App\Repository\RoleModuleRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\String\Slugger\SluggerInterface;

class ClientController extends AbstractController
{
    #[Route('/listClient', name: 'listClient')]
    public function listClient(ClientRepository $clientRepository,RoleListRepository $roleListRepository, RoleModuleRepository $roleModuleRepository): Response
    {
        $this->verifierSession("CLT", $roleListRepository, $roleModuleRepository);
        $clients = $clientRepository->findAll();
        return $this->render('back/client/listClient.html.twig', [
            'clients' => $clients,
        ]);
    }
    
    #[Route('/createClient', name: 'createClient', methods: ['GET', 'POST'])]
    public function createClient(ManagerRegistry $doctrine, Request $request, RoleListRepository $roleListRepository,SluggerInterface $slugger, RoleModuleRepository $roleModuleRepository, ClientRepository $repository,): Response
    {
        $this->verifierSession("CLT", $roleListRepository, $roleModuleRepository);
        if ($request->getMethod() == "POST") {
            $entityManager = $doctrine->getManager();
            $Nom = $request->get("Nom");
            $Prenom = $request->get("Prenom");
            $Phone = $request->get("telephone");
            $Email = $request->get("Email");
            $Password = $request->get("Password");
            $Cin = $request->get("Cin");
            $Adresse = $request->get("Adresse");
            $Sexe = $request->get("Sexe");
            $DateNaissance = $request->get("DateNaissance");
            $Permition = $request->get("permition");
            $date = new \DateTime($DateNaissance);
            $dateCurrent = new \DateTime('now');
            /*************************** */
            if(is_numeric($Phone)){
                if (filter_var($Email, FILTER_VALIDATE_EMAIL)) {
                    if (strlen($Password) >= 8 && strlen($Password) <= 16) {
                        if (!empty($Password)) {
                            $mem = $repository->findOneByEmail($Email);
                            $mec = $repository->findOneByCin($Cin);
                            $mep = $repository->findOneByPhone($Phone);

                            if ($mem || $mec || $mep) {
                                return $this->render('back/User/ajouter.html.twig', ['erreur' => "Client existe deja, veuillez changer d'adresse mail ou cin ou bien numero de telephone"]);
                            } else {
                                if ($date > $dateCurrent) {
                                    $client = new Client();
                                    $client->setNom($Nom);
                                    $client->setPrenom($Prenom);
                                    $client->setPhone($Phone);
                                    $client->setEmail($Email);
                                    $client->setPassword($Password);
                                    $client->setCin($Cin);
                                    $client->setAdresse($Adresse);
                                    $client->setSexe($Sexe);
                                    $client->setDateNaissance($date);
                                    $client->setPermition(intval($Permition));

                                    $entityManager->persist($client);
                                    $entityManager->flush();
                                    return $this->redirectToRoute('listClient');
                                } else {
                                    return $this->render('back/client/createClient.html.twig', ['erreur' => "Veuillez definir une date de naissance valide !!"]);
                                }
                            }
                        } else {
                            return $this->render('back/client/createClient.html.twig', ['erreur' => "Veuillez definir un password !!"]);
                        }
                    } else {
                        return $this->render('back/client/createClient.html.twig', ['erreur' => "Le mot de passe doit comprendre entre 8 et 16 caractéres"]);
                    }
                } else {
                    return $this->render('back/client/createClient.html.twig', ['erreur' => "Email incorrect !!"]);
                }
            }else{
                return $this->render('back/client/createClient.html.twig', ['erreur' => "Veuiller entrez un numero de telephone valide !!"]);
            }
            
            /************************ */
        }
        return $this->render('back/client/createClient.html.twig');
    }

    #[Route('/editClient/{id}', name: 'editClient', methods: ['GET', 'POST'])]
    public function editClient(ClientRepository $clientRepository, RoleListRepository $roleListRepository, RoleModuleRepository $roleModuleRepository, Request $request, $id, ManagerRegistry $doctrine): Response
    {
        $this->verifierSession("CLT", $roleListRepository, $roleModuleRepository);
        $entityManager = $doctrine->getManager();
        $client = $clientRepository->find($id);
        if ($request->getMethod() == "POST") {
            if ($client) {
                $Nom = $request->get("Nom");
                $Prenom = $request->get("Prenom");
                $Phone = $request->get("telephone");
                $Email = $request->get("Email");
                $Password = $request->get("Password");
                $Cin = $request->get("Cin");
                $Adresse = $request->get("Adresse");
                $Sexe = $request->get("Sexe");
                $DateNaissance = $request->get("DateNaissance");
                $Permition = $request->get("permition");
                $date = new \DateTime($DateNaissance);
                if (is_numeric($Phone)) {
                    if (filter_var($Email, FILTER_VALIDATE_EMAIL)) {
                        if (strlen($Password) >= 8 && strlen($Password) <= 16) {
                            $client->setNom($Nom);
                            $client->setPrenom($Prenom);
                            $client->setPhone($Phone);
                            $client->setEmail($Email);
                            $client->setPassword($Password);
                            $client->setCin($Cin);
                            $client->setAdresse($Adresse);
                            $client->setSexe($Sexe);
                            $client->setDateNaissance($date);
                            $client->setPermition(intval($Permition));
                            $entityManager->persist($client);
                            $entityManager->flush();
                            $this->addFlash('success', "client a été mis à jour avec succès");
                            return $this->redirectToRoute('listClient');
                        }else{
                            return $this->render('back/client/editClient.html.twig', [
                                'client' => $client, 'erreur'=> 'Veuiller enytrer un numero valide'
                            ]);
                        }  
                    }else{
                        return $this->render('back/client/editClient.html.twig', [
                            'client' => $client, 'erreur'=> 'Veuiller verifier l\'adreese email'
                        ]);
                    }
                    } else {
                        return $this->render('back/client/editClient.html.twig', [
                            'client' => $client, 'erreur' => 'Le numero de telephone n\'est pas valide'
                        ]);
                    }
               
            }else{
                    return $this->render('back/client/editClient.html.twig', [
                        'client' => $client, 'erreur' => "Le client existe deja "
                    ]);
            }
        }
        return $this->render('back/client/editClient.html.twig', [
            'client' => $client
        ]);
    }

    #[Route('/listNoir', name: 'listNoir')]
    public function AddListNoir(RoleListRepository $roleListRepository, RoleModuleRepository $roleModuleRepository,ClientRepository $clientRepository) : Response
    {
        $this->verifierSession("CLT", $roleListRepository, $roleModuleRepository);
        $clients = $clientRepository->findAll();
        return $this->render('back/client/listNoir.html.twig', [
            'clients' => $clients,
        ]);
    }

    #[Route('/deleteClient/{id}', name: 'deleteClient', methods: ['GET', 'DELETE'])]
    public function  deleteClient(RoleListRepository $roleListRepository, RoleModuleRepository $roleModuleRepository,ManagerRegistry $doctrine, ClientRepository $clientRepository, $id): Response
    {
        $this->verifierSession("CLT", $roleListRepository, $roleModuleRepository);
        $client = $clientRepository->find($id);
        $entityManager = $doctrine->getManager();
        $entityManager->remove($client);
        $entityManager->flush();
        return $this->redirectToRoute('listClient');
    }

    #[Route('/deleteListNoir/{id}', name: 'deleteListNoir', methods: ['GET', 'DELETE'])]
    public function  deleteListNoir(ManagerRegistry $doctrine, RoleListRepository $roleListRepository, RoleModuleRepository $roleModuleRepository, ClientRepository $clientRepository, $id): Response
    {
        $this->verifierSession("CLT", $roleListRepository, $roleModuleRepository);
        $client = $clientRepository->find($id);
        $entityManager = $doctrine->getManager();
        $entityManager->remove($client);
        $entityManager->flush();
        return $this->redirectToRoute('listNoir');
    }

    #[Route('/editListNoirPermition/{id}',name: 'editListNoirPermition')]
    public function editListNoirPermition(ClientRepository $clientRepository, Request $request, RoleListRepository $roleListRepository, RoleModuleRepository $roleModuleRepository, $id, ManagerRegistry $doctrine): Response
    {
        $this->verifierSession("CLT", $roleListRepository, $roleModuleRepository);
        $entityManager = $doctrine->getManager();
        $client = $clientRepository->find($id);
        if ($request->getMethod() == "POST" &&  $client) {
            $Permition = $request->get("Permition");
            $client->setPermition($Permition);
            $entityManager->persist($client);
            $entityManager->flush($client);
            return $this->rendiretToRoute('listNoir');
        }
        return $this->render("/back/client/editListNoirPermition.html.twig",['client'=>$client]);

    }

    #[Route('/imprimerClient', name: 'imprimerClient', methods: ['GET'])]
    public function imprimerClient(ClientRepository $ClientRepository, RoleListRepository $roleListRepository, RoleModuleRepository $roleModuleRepository): Response
    {
        $this->verifierSession("CLT", $roleListRepository, $roleModuleRepository);
        $clients = $ClientRepository->findAll();
        $html = $this->renderView('back/client/imprimerClient.html.twig', [
            'clients' => $clients
        ]);
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($pdfOptions);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $output = $dompdf->output();
        $dompdf->stream("listClient.pdf", ["Attachement" => true]);
        return new Response("The PDF file has been succesfully generated !");
    }

    #[Route('/imprimerNoir', name: 'imprimerNoir', methods: ['GET'])]
    public function imprimerNoir(ClientRepository $ClientRepository, RoleListRepository $roleListRepository, RoleModuleRepository $roleModuleRepository): Response
    {
        $this->verifierSession("CLT", $roleListRepository, $roleModuleRepository);
        $clients = $ClientRepository->findAll();
        $html = $this->renderView('back/client/imprimerNoir.html.twig', [
            'clients' => $clients
        ]);
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($pdfOptions);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $output = $dompdf->output();
        $dompdf->stream("listNoir.pdf", ["Attachement" => true]);
        return new Response("The PDF file has been succesfully generated !");
    }

    #[Route('/delete2/{id}')]
    public function deleteadminAction(RoleListRepository $roleListRepository, RoleModuleRepository $roleModuleRepository,ClientRepository $repository, ManagerRegistry $doctrine,$id)
    {
        $this->verifierSession("CLT", $roleListRepository, $roleModuleRepository);
        $em = $doctrine->getManager();
        $response = "no";
        $evenement = $repository->find($id);
        $em->remove($evenement);
        $em->flush();
        $response = "ok";
        return new Response($response);
    }

    function verifierSession($Code, RoleListRepository $roleListRepository, RoleModuleRepository $roleModuleRepository)
    {
        $response = false;
        $session = new Session();
        if ($session->get('connect') != 'OK' && $session->get('user') == null) {
            $this->routeToControllerName('login');
        } else {
            $id = $session->get('user')->getId();
            $listeRole = $roleListRepository->findOneBy(array("Code" => $Code));
            if ($listeRole) {
                $roleModule = $roleModuleRepository->findOneBy(array("Id_User" => $id, "Id_RoleList" => $listeRole->getId(), "Permition" => 1));
                if ($roleModule) {
                    $response = true;
                } else {
                    $this->routeToControllerName('backHome');
                }
            } else {
                $this->routeToControllerName('backHome');
            }
        }
        return $response;
    }

    public function routeToControllerName($routename)
    {
        $hasAccess = false;
        if (!$hasAccess) {
            (new RedirectResponse($routename))->send();
            exit;
        }
    }
}
