<?php

namespace App\Controller;

use App\Entity\Location;
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

class ReservationController extends AbstractController
{
    #[Route('/listReservation', name: 'listReservation')]
    public function listReservation(LocationRepository $locationRepository, VehiculeRepository $vehiculeRepository,ClientRepository $clientRepository): Response
    {
        $value =0 ; $note = "REC";
        $vehicules = $vehiculeRepository->findAll();
        $clients = $clientRepository->findAll();
        $reservations = $locationRepository->findByReservation($value,$note);
        return $this->render('back/reservation/listReservation.html.twig', [
            'reservations' => $reservations, 'vehicules' => $vehicules, 'clients' => $clients
        ]);
    }

    #[Route('/listReservationRefus', name: 'listReservationRefus')]
    public function listReservationRefus(LocationRepository $locationRepository, VehiculeRepository $vehiculeRepository, ClientRepository $clientRepository): Response
    {
        $val =2;
        $vehicules = $vehiculeRepository->findAll();
        $clients = $clientRepository->findAll();
        $reservations = $locationRepository->findByRefus($val);
        return $this->render('back/reservation/listReservationRefus.html.twig', [
            'reservations' => $reservations, 'vehicules' => $vehicules, 'clients' => $clients
        ]);
    }

    #[Route('/acceptReservation/{id}', name: 'acceptReservation')]
    public function acceptReservation($id, LocationRepository $locationRepository,ManagerRegistry $doctrine){
        $reservation = $locationRepository->find($id);
        $valide =1;
        $reservation->setValide($valide);
        $entityManager = $doctrine->getManager();
        $entityManager->flush($reservation);
        return $this->redirectToRoute('listReservation');
    }

    #[Route('/annulerReservation/{id}', name: 'annulerReservation')]
    public function annulerReservation(LocationRepository $repository, ManagerRegistry $doctrine,Request $request, ClientRepository $clientRepository,$id, VehiculeRepository $vehiculeRepository)
    {
        $em = $doctrine->getManager();
        $valide= 2;
        $reservation = $repository->find($id);
        $Note =$request->get("Note");
        if($request->getMethod() == "POST"){
            $reservation->setNote($Note);
            $reservation->setValide($valide);
            $em->flush();
            return $this->redirectToRoute('listReservation');
        }
        return $this->render('back/reservation/annulerReservation.html.twig',['reservation'=>$reservation]);
    }
    #[Route('/addReservation', name: 'addReservation')]
    public function addReservation(ManagerRegistry $doctrine,RoleListRepository $roleListRepository, RoleModuleRepository $roleModuleRepository, Request $request, UserRepository $userRepository, ClientRepository $clientRepository, VehiculeRepository $vehiculeRepository): Response
    {
        $this->verifierSession("RSV", $roleListRepository, $roleModuleRepository);
        $session = new Session();
        if ($request->getMethod() == "POST") {
            $entityManager = $doctrine->getManager();
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
            $statutV = "En location";
            $Valide = 1;
            $note = "V";
            $vehicule->setStatusVehicule($statutV);
            $reservation = new Location();
            $reservation->setFromDate($DateD);
            $reservation->setToDate($DateF);
            $reservation->setNote($note);
            $reservation->setIdVehicule($vehicule);
            $reservation->setRemise($totalPrix);
            $reservation->setIdUser($user);
            $reservation->setDateRegistration($curentDateTime);
            $reservation->setIdClient($client);
            $reservation->setValide($Valide);
            $entityManager->persist($reservation);
            $entityManager->flush();
            return $this->redirectToRoute('listReservation');
        } else {
            $this->addFlash('error', "erreur");
        }

        return $this->render('back/reservation/addReservation.html.twig');
    }

    #[Route('/deleteReservation/{id}', name: 'deleteReservation')]
    public function deleteReservation(LocationRepository $repository, RoleListRepository $roleListRepository, RoleModuleRepository $roleModuleRepository, ManagerRegistry $doctrine, $id)
    {
        $this->verifierSession("RSV", $roleListRepository, $roleModuleRepository);
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
