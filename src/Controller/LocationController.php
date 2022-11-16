<?php

namespace App\Controller;

use DateTime;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\User;
use App\Entity\Location;
use App\Entity\Vehicule;
use PhpParser\Builder\Method;
use App\Repository\UserRepository;
use App\Repository\ClientRepository;
use App\Repository\LocationRepository;
use App\Repository\RoleListRepository;
use App\Repository\VehiculeRepository;
use App\Repository\RoleModuleRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LocationController extends AbstractController
{

    #[Route('/listLocation1', name: 'listLocation1')]
    public function listLocation1(ManagerRegistry $doctrine, Request $request,LocationRepository $locationRepository, UserRepository $userRepository, ClientRepository $clientRepository, VehiculeRepository$vehiculeRepository, RoleListRepository $roleListRepository, RoleModuleRepository $roleModuleRepository): Response
    {
        $this->verifierSession("LOC", $roleListRepository, $roleModuleRepository);
        $entityManager = $doctrine->getManager();
        $locations = $entityManager->createQuery("SELECT l, v, u FROM App\Entity\Location l JOIN l.Id_Vehicule v JOIN l.Id_User u ORDER BY l.DateRegistration ")->getResult();
        return $this->render('back/location/listLocation1.html.twig', [
            'locations' => $locations,
        ]);
    }

    #[Route('/listLocation', name: 'listLocation')]
    public function listLocation(ManagerRegistry $doctrine, Request $request,ClientRepository $clientRepository, LocationRepository $locationRepository, UserRepository $userRepository, VehiculeRepository$vehiculeRepository, RoleListRepository $roleListRepository, RoleModuleRepository $roleModuleRepository): Response
    {
        $this->verifierSession("LOC", $roleListRepository, $roleModuleRepository);
        $value = "NULL";
        $locations = $locationRepository->findByLocation($value); 
        $users = $userRepository->findAll();
        $vehicules = $vehiculeRepository->findAll();
        $clients = $clientRepository->findAll();
       
        return $this->render('back/location/listLocation.html.twig', [
            'locations' => $locations, 'vehicules'=> $vehicules , 'users'=> $users,'clients' => $clients
        ]);
    }

    #[Route('/ajouterLocation', name: 'ajouterLocation')]
    public function ajouterLocation(ManagerRegistry $doctrine, Request $request,UserRepository $userRepository, ClientRepository $clientRepository, VehiculeRepository$vehiculeRepository, RoleListRepository $roleListRepository, RoleModuleRepository $roleModuleRepository): Response
    {
        $session = new Session();
        $loc = '';
        $this->verifierSession("LOC", $roleListRepository, $roleModuleRepository);

        if($request->getMethod() == "POST"){
            $entityManager = $doctrine->getManager();
            $CIN =$request->get("cin");
            $client = $clientRepository->find($CIN);
            $MATRICUL =$request->get("matricul");
            $vehicule = $vehiculeRepository->find($MATRICUL);
            $prixDay = $vehiculeRepository->SelectPrixDay($MATRICUL);
            $DATEDEBUT =$request->get("datedebut");
            $DATEFIN = $request->get("datefin");
            $idUSER = $session->get("user");
            $user = $userRepository->find($idUSER);
            $DateD= new \DateTime($DATEDEBUT);
            $DateF= new \DateTime($DATEFIN);
            $curentDateTime = new \DateTime('now');
            $diff = date_diff($DateD, $DateF);
            $NbDays= $diff->days;
            $totalPrix = $NbDays * $prixDay;
            $statutV = "En location";
            $Valide=1;
            $note = "V";
            $vehicule->setStatusVehicule($statutV);
            $location = new Location();
            $location->setFromDate($DateD);
            $location->setToDate($DateF);
            $location->setNote($note);
            $location->setIdVehicule($vehicule);
            $location->setRemise($totalPrix);
            $location->setIdUser($user);
            $location->setDateRegistration($curentDateTime);
            $location->setIdClient($client);
            $location->setValide($Valide);
            $entityManager->persist($location);
            $entityManager->flush();
            return $this->redirectToRoute('listLocation');
        } else {
            $this->addFlash('error', "erreur");
        }

        return $this->render('back/location/ajouterLocation.html.twig');
    }

    #[Route('/editLocation/{id}', name: 'editLocation')]
    public function editLocation(ManagerRegistry $doctrine,$id, Request $request,LocationRepository $locationRepository, UserRepository $userRepository, ClientRepository $clientRepository, VehiculeRepository$vehiculeRepository, RoleListRepository $roleListRepository, RoleModuleRepository $roleModuleRepository): Response
    {
        $this->verifierSession("LOC", $roleListRepository, $roleModuleRepository);
        $session = new Session();
            $entityManager = $doctrine->getManager();
            $location = $locationRepository->find($id);
        if ($request->getMethod() == "POST") {
            $CIN = $request->get("cin");
            $client = $clientRepository->find($CIN);
            $MATRICUL = $request->get("matricul");
            $vehicule = $vehiculeRepository->find($MATRICUL);
            $prixDay = $vehiculeRepository->SelectPrixDay($MATRICUL);
            $DATEDEBUT = $request->get("datedebut");
            $DATEFIN = $request->get("datefin");
            $idUSER = $session->get("user");
            $user = $userRepository->find($idUSER);
            $DateD = new \DateTime($DATEDEBUT);
            $DateF = new \DateTime($DATEFIN);
            $curentDateTime = new \DateTime('now');
            $diff = date_diff($DateD, $DateF);
            $NbDays = $diff->days;
            $totalPrix = $NbDays * $prixDay;
            
            $location->setFromDate($DateD);
            $location->setToDate($DateF);
            $location->setIdVehicule($vehicule);
            $location->setRemise($totalPrix);
            $location->setIdUser($user);
            $location->setDateRegistration($curentDateTime);
            $entityManager->flush();
            return $this->redirectToRoute('listLocation');
        } 
        return $this->render('back/location/editLocation.html.twig',['location'=> $location]);
    }

    #[Route('/ajouterLocation/show1Location/{id}',name: 'showLocation')]
    public function showLocation(locationRepository $locationRepository, $id, ManagerRegistry $doctrine, RoleListRepository $roleListRepository, RoleModuleRepository $roleModuleRepository){
        $this->verifierSession("LOC", $roleListRepository, $roleModuleRepository);
        $entityManager = $doctrine->getManager();
        $location = $locationRepository->find($id);
        return $this->render('back/location/showLocation.html.twig', [
            'location' => $location
    
        ]);
    }
    
    #[Route('/deleteLocation/{id}', name :'deleteLocation')]
    public function deleteLocation(LocationRepository $repository, ManagerRegistry $doctrine, $id, RoleListRepository $roleListRepository, RoleModuleRepository $roleModuleRepository)
    {
        $this->verifierSession("LOC", $roleListRepository, $roleModuleRepository);
        $em = $doctrine->getManager();
        $response = "no";
        $evenement = $repository->find($id);
        $em->remove($evenement);
        $em->flush();
        $response = "ok";
        return new Response($response);
    }

    #[Route('/imprimerTotalite', name: 'imprimerTotalite', methods: ['GET'])]
    public function imprimerTotalite(ClientRepository $clientRepository, LocationRepository $locationRepository, UserRepository $userRepository, VehiculeRepository $vehiculeRepository, RoleListRepository $roleListRepository, RoleModuleRepository $roleModuleRepository): Response
    {
        $this->verifierSession("LOC", $roleListRepository, $roleModuleRepository);
        $locations = $locationRepository->findAll();
        $users = $userRepository->findAll();
        $vehicules = $vehiculeRepository->findAll();
        $clients = $clientRepository->findAll();
        $html = $this->renderView('back/location/imprimerTotalite.html.twig', [
            'locations' => $locations, 'vehicules' => $vehicules, 'users' => $users, 'clients' => $clients
        ]);
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');
        // Render the HTML as PDF
        $dompdf->render();
        // Store PDF Binary Data
        $output = $dompdf->output();

        $dompdf->stream("listLocation.pdf", ["Attachement" => true]);

        // Send some text response
        return new Response("The PDF file has been succesfully generated !");
    }

    #[Route('/imprimerPrecis/{id}', name: 'imprimerPrecis', methods: ['GET'])]
    public function imprimerPrecis($id,ClientRepository $clientRepository, LocationRepository $locationRepository, UserRepository $userRepository, VehiculeRepository $vehiculeRepository, RoleListRepository $roleListRepository, RoleModuleRepository $roleModuleRepository): Response
    {
        $this->verifierSession("LOC", $roleListRepository, $roleModuleRepository);
        $location = $locationRepository->find($id);
        $html = $this->renderView('back/location/imprimerPrecis.html.twig', [
            'location' => $location
        ]);
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');
        // Render the HTML as PDF
        $dompdf->render();
        // Store PDF Binary Data
        $output = $dompdf->output();

        $dompdf->stream("Location.pdf", ["Attachement" => true]);

        // Send some text response
        return new Response("The PDF file has been succesfully generated !");
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
