<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Client;
use App\Entity\Location;
use App\Entity\Remarque;
use App\Entity\TypeVehicule;
use App\Repository\UserRepository;
use App\Repository\ClientRepository;
use App\Repository\LocationRepository;
use App\Repository\MarqueRepository;
use App\Repository\RemarqueRepository;
use App\Repository\VehiculeRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\TypeVehiculeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'home')]
    public function index(): Response
    {
        return $this->render('front/home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/about', name: 'about')]
    public function about(): Response
    {
        return $this->render('front/home/about.html.twig');
    }

    #[Route('/contact', name: 'contact')]
    public function contact(): Response
    {
        return $this->render('front/home/contact.html.twig');
    }

    #[Route('/ShowVehiculeFront/{id}', name: 'ShowVehiculeFront', methods: ['GET'])]
    public function ShowVehiculeFront(ManagerRegistry $doctrine, VehiculeRepository $vehiculeRepository,LocationRepository $locationRepository, MarqueRepository $marqueRepository, $id, TypeVehiculeRepository $typeVehiculeRepository): Response
    {
        $v = $vehiculeRepository->find($id);
        $list= [];
            $location = $locationRepository->find($v->getId());
            $marqueId = $v->getIdMarque();
            $marque = $marqueRepository->find($marqueId);
            $typeId = $v->getIdTypeVehicule();
            $type = $typeVehiculeRepository->find($typeId);
            if ($v->getStatusVehicule() != "En panne") {
                if ($location && $location->getValide() == 1) {
                    $list = [
                        "id" => $v->getId(),
                        "Marque" => $marque->getMarque(),
                        "Type" => $type->getTypeVehicule(),
                        "Description" => $v->getDescription(),
                        "Titre" => $v->getTitre(),
                        "PrixDay" => $v->getPrixDay(),
                        "Capacity" => $v->getCapacity(),
                        "Model" => $v->getModel(),
                        "ModelYear" => $v->getModelYear(),
                        "Diponibilite" => $location->getToDate()->format('Y-m-d'),
                    ];
                } else {
                    $list = [
                        "id" => $v->getId(),
                        "Marque" => $marque->getMarque(),
                        "Type" => $type->getTypeVehicule(),
                        "Description" => $v->getDescription(),
                        "Titre" => $v->getTitre(),
                        "PrixDay" => $v->getPrixDay(),
                        "Capacity" => $v->getCapacity(),
                        "Model" => $v->getModel(),
                        "ModelYear" => $v->getModelYear(),
                        "Diponibilite" => "Availble",
                    ];
                }
            } else {
                $list = [
                    "id" => $v->getId(),
                    "Marque" => $marque->getMarque(),
                    "Type" => $type->getTypeVehicule(),
                    "Description" => $v->getDescription(),
                    "Titre" => $v->getTitre(),
                    "PrixDay" => $v->getPrixDay(),
                    "Model" => $v->getModel(),
                    "ModelYear" => $v->getModelYear(),
                    "Capacity" => $v->getCapacity(),
                    "Diponibilite" => "Not Availble",
                ];
            }
        return $this->render('front/home/showVehiculeFront.html.twig', ['vehicule'=> $list]);
    }

    #[Route('/profil/{id}', name: 'profil')]
    public function profil($id, ClientRepository $ClientRepository, ManagerRegistry $doctrine, LocationRepository $repository , Request $request)
    {
        $session = new Session();
        $entityManager = $doctrine->getManager();
        if ($session->get("connect_client") == "OK" && $session->get("client") != null) {
            $client = $ClientRepository->find($id);
            $reservations = $repository->findByReservationClient($id);
            return $this->render('front/home/profil.html.twig',["client" => $client, "reservations" =>$reservations]);
        }else{
            return $this->redirectToRoute('home');
        }
    }


    #[Route('/editProfil/{id}', name: 'editProfil')]
    public function editProfil(Request $request, $id, ClientRepository $ClientRepository, ManagerRegistry $doctrine)
    {
        $session = new Session();
        $entityManager = $doctrine->getManager();
        $client = $ClientRepository->find($id);
        if ($session->get("connect_client") == "OK" && $session->get("client") != null) {
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
                    $Permition = 0;
                    $date = new \DateTime($DateNaissance);
                    $client->setNom($Nom);
                    $client->setPrenom($Prenom);
                    $client->setPhone($Phone);
                    $client->setEmail($Email);
                    $client->setPassword($Password);
                    $client->setCin($Cin);
                    $client->setAdresse($Adresse);
                    $client->setSexe($Sexe);
                    $client->setDateNaissance($date);
                    $client->setPermition($Permition);
                    $entityManager->persist($client);
                    $entityManager->flush();
                    return $this->redirectToRoute('home',['success'=>'Profil successfuly updated :)']);
            
                } else {
                    $this->addFlash('erreur', "client innexistante");
                }
            }
        } else {
            return $this->redirectToRoute('home');
        }
        return $this->render('front/home/profilEdit.html.twig', ["client" => $client]);
    }


    #[Route('/findVehicle', name: 'findVehicle', methods: ['GET', 'POST'])]
    public function findVehicle(MarqueRepository $marqueRepository,VehiculeRepository $vehiculeRepository, TypeVehiculeRepository $typeVehiculeRepository, ManagerRegistry $doctrine, Request $request){
       
        $entityManager = $doctrine->getManager();
        $marques = $marqueRepository->findAll();
        $types = $typeVehiculeRepository->findAll();
        $session = new Session();
        if ($request->getMethod() == "POST") {
            if ($session->get("connect_client") == "OK" && $session->get("client") != null)  {
                $ToDate = $request->get("ToDate");
                $FromDate = $request->get("FromDate");
                $Type = $request->get("Type");
                $Marque = $request->get("Marque");
                $DateT = new \DateTime($ToDate);
                $DateF = new \DateTime($FromDate);
                $date = new \DateTime('now');
                if(!empty($ToDate) && !empty($FromDate) && !empty($Type) && !empty($Marque)){
                    if ($DateF <= $date || $DateT <= $DateF) {
                        return $this->render('front/home/findVehicle.html.twig', array(
                            'erreur' => "Nither DateFrom or DateTo could be equal to curent date or less!!",'marques' => $marques, 'types' => $types
                        ));
                    } else {
                        if($Type == "AllType" && $Marque == "AllBrand"){
                            return $this->redirectToRoute('listVehicleDetail');
                        }

                        if($Type != "AllType" && $Marque == "AllBrand"){
                            $vehicules = $vehiculeRepository->findByType($Type);
                            return $this->render('front/home/checkAvailable.html.twig',['vehicules' => $vehicules]);
                        }

                        if ($Type == "AllType" && $Marque != "AllBrand") {
                            $vehicules = $vehiculeRepository->findByMarque($Marque);
                            return $this->render('front/home/checkAvailable.html.twig', ['vehicules' => $vehicules]);
                        }
                    }
                }
                }else{
                    return $this->render('front/home/findVehicle.html.twig', array('erreur' => "You need to login befor reserve a vehicle!!", 'marques' => $marques, 'types' => $types));
                }
            } else {
            return $this->render('front/home/findVehicle.html.twig', array( 'marques' => $marques, 'types' => $types));

            }
        return $this->render('front/home/findVehicle.html.twig',['marques' => $marques, 'types'=> $types]);
    }

    #[Route('/loginClient', name: 'loginClient')]
    public function loginClient(Request $request, ClientRepository $ClientRepository, ManagerRegistry $doctrine)
    {
        $session = new Session();
        if ($session->get("connect_client") == "OK" && $session->get("client") != null) {
            return $this->redirectToRoute('home');
        } else {
            $response = "";
            if ($request->getMethod() == "POST") {
                $email = $request->get("login");
                $pass = $request->get("pass");
                $entityManager = $doctrine->getManager();
                if ($email == "" || $pass == "") {
                    $response = "Veuillez renseigner  l'email et le mot de passe";
                } else {
                    $ent = $ClientRepository->findOneby(array("Email" => $email, "Password" => $pass));
                    if ($ent) {
                        $c = clone $ent;
                        $session->set("connect_client", "OK");
                        $session->set("client", $ent);
                        $entityManager->flush();
                        return $this->redirectToRoute('home');
                    } else {
                        $response = "Nom ou mot de passe incorrect";
                    }
                }
            }
            return $this->render('front/home/loginClient.html.twig', array("response" => $response));
        }
    }

    #[Route('/logoutClient', name: 'logoutClient')]
    public function logoutClient()
    {
        $session = new Session();
        $session->set("connect", "NO");
        $session->set("client", null);
        return $this->redirectToRoute('home');
    }

    #[Route('/registerClient', name: 'registerClient')]
    public function registerClient(ClientRepository $ClientRepository,ManagerRegistry $doctrine, Request $request) : Response
    {
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
            $date = new \DateTime($DateNaissance);
            if (
                !empty($Nom) && !empty($Prenom) && !empty($Adresse) && !empty($Phone) && !empty($Sexe)
                && !empty($Email) && !empty($Password) && !empty($Cin) && !empty($DateNaissance)
            ) {
                $Clt = $ClientRepository->findOneByEmail($Email);
                $checkPhone = $ClientRepository->findOneByPhone($Phone);
                if(!$Clt && !$checkPhone){
                    $client = new Client();
                    $Permition=0;
                    $client->setNom($Nom);
                    $client->setPrenom($Prenom);
                    $client->setPhone($Phone);
                    $client->setEmail($Email);
                    $client->setPassword($Password);
                    $client->setCin($Cin);
                    $client->setAdresse($Adresse);
                    $client->setSexe($Sexe);
                    $client->setDateNaissance($date);
                    $client->setPermition($Permition);
                    $entityManager->persist($client);
                    $entityManager->flush();
                    $session = new Session();
                    $c = clone $client;
                    $session->set("connect_client", "OK");
                    $session->set("client", $c);
                    return $this->redirectToRoute('home');
                }else{
                    return $this->render('front/home/registerClient.html.twig', array(
                    'erreur' => "Client Already Exist!!",
                        'erreur' => "Remplir tous les champs SVP !!", 'Nom' => $Nom, 'Cin' => $Cin,
                        'Sexe' => $Sexe, 'Prenom' => $Prenom, 'Prenom' => $Prenom, 'Adresse' => $Adresse,
                        'Email' => $Email, 'DateNaissance' => $DateNaissance, 'Phone' => $Phone
                    ));
                }
            }
            else{
                return $this->render('front/home/registerClient.html.twig', array(
                    'erreur' => "Fill all the blanks fields please!!", 'Nom' => $Nom, 'Cin' => $Cin,
                    'Sexe' => $Sexe, 'Prenom' => $Prenom, 'Prenom' => $Prenom, 'Adresse' => $Adresse,
                    'Email' => $Email, 'DateNaissance' => $DateNaissance, 'Phone' => $Phone 
                ));
            }
        }
        return $this->render('front/home/registerClient.html.twig');
       
    }

    #[Route('/BookVehicule/{id}', name: 'BookVehicule')]
    public function BookVehicule($id,ClientRepository $clientRepository ,VehiculeRepository $vehiculeRepository, ManagerRegistry $doctrine, Request $request): Response
    {
        $session = new Session();
        $vehicule  = $vehiculeRepository->find($id);
        if ($session->get("connect_client") == "OK" && $session->get("client") != null) {
            if ($request->getMethod() == "POST") {
                $entityManager = $doctrine->getManager();
                $idclient = $session->get("client");
                $client = $clientRepository->find($idclient);
                $vehicule = $vehiculeRepository->find($id);
                $prixDay = $vehiculeRepository->SelectPrixDay($id);
                $DATEDEBUT = $request->get("datedebut");
                $DATEFIN = $request->get("datefin");
                $DateD = new \DateTime($DATEDEBUT);
                $DateF = new \DateTime($DATEFIN);
                $date = new \DateTime('now');
                $diff = date_diff($DateD, $DateF);
                $NbDays = $diff->days;
                $totalPrix = $NbDays * $prixDay;
                $statutV = "Réservé";
                $Valide = 0;
                $note = "REC";
                if($DateD <= $date || $DateF <= $DateD ){
                    return $this->render('front/home/bookVehicle.html.twig', array(
                        'erreur' => "Nither DateFrom or DateTo could be equal to curent date or less!!", 'vehicule' => $vehicule
                    ));
                }else{
                    $vehicule->setStatusVehicule($statutV);
                    $reservation = new Location();
                    $reservation->setFromDate($DateD);
                    $reservation->setToDate($DateF);
                    $reservation->setIdVehicule($vehicule);
                    $reservation->setRemise($totalPrix);
                    $reservation->setDateRegistration($date);
                    $reservation->setIdClient($client);
                    $reservation->setValide($Valide);
                    $reservation->setNote($note);
                    $entityManager->persist($reservation);
                    $entityManager->flush();
                    return $this->render('front/home/SuccessReservation.html.twig');
                }
            }
        } else {
            return $this->redirectToRoute('loginClient',array('erreur' => "You need to login befor reserve a vehicle!!"));
        }
        return $this->render('front/home/bookVehicle.html.twig', ['vehicule' => $vehicule]);
    }

    #[Route('/listVehicleDetail', name: 'listVehicleDetail')]
    public function listVehicleDetail(VehiculeRepository $vehiculeRepository, LocationRepository $locationRepository ,MarqueRepository $marqueRepository, TypeVehiculeRepository $typeVehiculeRepository): Response
    {
        $vehicules = $vehiculeRepository->findAll();
        $list= [];
        if (!$vehicules) {
            $this->addFlash('info', "la liste est vide");
        }
        for( $i=0; $i<sizeof($vehicules); $i++ ){
            $v = $vehicules[$i];
            $location = $locationRepository->find($vehicules[$i]->getId());
            $marqueId = $v->getIdMarque();
            $marque = $marqueRepository->find($marqueId);
            $typeId = $v->getIdTypeVehicule();
            $type = $typeVehiculeRepository->find($typeId);
            if($v->getStatusVehicule() != "En panne"){
                if($location && $location->getValide()==1){
                    $list[$i] = [
                        "id" => $v->getId(),
                        "Marque"=> $marque->getMarque(), 
                        "Type" => $type->getTypeVehicule(),
                        "Description" =>$v->getDescription(),
                        "Titre" => $v->getTitre(),
                        "PrixDay" => $v->getPrixDay(),
                        "Capacity" => $v->getCapacity(),
                        "Diponibilite"=> $location->getToDate()->format('Y-m-d'), 
                    ];
                }else{
                    $list[$i] = [
                        "id" => $v->getId(),
                        "Marque" => $marque->getMarque(),
                        "Type" => $type->getTypeVehicule(),
                        "Description" => $v->getDescription(),
                        "Titre" => $v->getTitre(),
                        "PrixDay" => $v->getPrixDay(),
                        "Capacity" => $v->getCapacity(),
                        "Diponibilite" => "Availble",
                    ];
                }
            }else{
                $list[$i] = [
                    "id" => $v->getId(),
                    "Marque" => $marque->getMarque(),
                    "Type" => $type->getTypeVehicule(),
                    "Description" => $v->getDescription(),
                    "Titre" => $v->getTitre(),
                    "PrixDay" => $v->getPrixDay(),
                    "Capacity" => $v->getCapacity(),
                    "Diponibilite" => "Not Availble",
                ];
            }
        }
        return $this->render('front/home/listVehicleDetail.html.twig', ["list" =>$list]);
    }

    #[Route('/addRemarque', name:'addRemarque')]
    public function addRemarque(ManagerRegistry $doctrine, Request $request, ClientRepository $clientRepository){
        $session = new Session();
            if ($request->getMethod() == "POST") {
                $remarque = $request->get("Remarque");
                $Client = $session->get("client");
                $idclient = $clientRepository->find($Client);
                $remark = new Remarque();
                $remark->setRemarque($remarque);
                $remark->setIdClient($idclient);
                $em = $doctrine->getManager();
                $em->persist($remark);
                $em->flush();
            }
        return $this->render("front/home/index.html.twig");
    }
   
}
