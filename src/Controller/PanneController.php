<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Panne;
use App\Repository\PanneRepository;
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

class PanneController extends AbstractController
{
    #[Route('/listPanne', name: 'listPanne')]
    public function listPanne(PanneRepository $panneRepository, RoleListRepository $roleListRepository, RoleModuleRepository $roleModuleRepository): Response
    {
        $this->verifierSession("PAN", $roleListRepository, $roleModuleRepository);
        $pannes = $panneRepository->findAll();
        return $this->render('back/panne/listPanne.html.twig', [
            'pannes' => $pannes,
        ]);
    }

    #[Route('/ajouterPanne', name: 'ajouterPanne', methods: ['GET','POST'])]
    public function ajouterPanne(Request $request, RoleListRepository $roleListRepository, RoleModuleRepository $roleModuleRepository,PanneRepository $panneRepository, VehiculeRepository $VehiculeRepository, ManagerRegistry $doctrine): Response
    {
        $this->verifierSession("PAN", $roleListRepository, $roleModuleRepository);
        $statut = "En panne";
        $vehicules= $VehiculeRepository->VehiculeFonctionne($statut);
        if ($request->getMethod() == "POST") {
            $entityManager = $doctrine->getManager();
            $Id_Vehicule = $request->get("Id_Vehicule");
            $id_v= $VehiculeRepository->find($Id_Vehicule);
            $StatusPanne = $request->get("StatusPanne");
            $TypePanne = $request->get("TypePanne");
            $Remarque = $request->get("Remarque");
            $panne = new Panne();
            $panne->setIdVehicule($id_v);
            $panne->setStatusPanne($StatusPanne);
            $panne->setTypePanne($TypePanne);
            $panne->setRemarque($Remarque);
            $entityManager->persist($panne);
            $entityManager->flush();
            return $this->redirectToRoute('listPanne');
        }
        return $this->render('back/panne/ajouterPanne.html.twig',['vehicules'=> $vehicules]);
    }

    #[Route('/editPanne/{idp}/{idv}', name: 'editPanne', methods: ['GET', 'POST'])]
    public function editPanne(Request $request, $idp, $idv,RoleListRepository $roleListRepository, RoleModuleRepository $roleModuleRepository,PanneRepository $panneRepository, VehiculeRepository $VehiculeRepository, ManagerRegistry $doctrine): Response
    {
        $this->verifierSession("PAN", $roleListRepository, $roleModuleRepository);
        $panne = $panneRepository->find($idp);
        $entityManager = $doctrine->getManager();
        if ($request->getMethod() == "POST") {
            $StatusPanne = $request->get("StatusPanne");
            $TypePanne = $request->get("TypePanne");
            $Remarque = $request->get("Remarque");
            $id_v = $VehiculeRepository->find($idv);
            $panne->setIdVehicule($id_v);
            $panne->setStatusPanne($StatusPanne);
            $panne->setTypePanne($TypePanne);
            $panne->setRemarque($Remarque);
            $entityManager->flush();
            return $this->redirectToRoute('listPanne');
        }
        return $this->render('back/panne/editPanne.html.twig', ['panne' => $panne]);
    }

    #[Route('/deletePanne/{idp}/{idv}', methods: ['GET', 'DELETE'], name: 'deletePanne')]
    public function deletePanne(ManagerRegistry $doctrine, RoleListRepository $roleListRepository, RoleModuleRepository $roleModuleRepository,PanneRepository $panneRepository, VehiculeRepository $VehiculeRepository, $idp, $idv): Response
    {
        $this->verifierSession("PAN", $roleListRepository, $roleModuleRepository);
        $panne = $panneRepository->find($idp);
        $vehicule = $VehiculeRepository->find($idv);
        $statusV = "Disponible";
        $vehicule->setStatusVehicule($statusV);
        $entityManager = $doctrine->getManager();
        $entityManager->remove($panne);
        $entityManager->flush();
        return $this->redirectToRoute('listPanne');
    }


    #[Route('/imprimerPanne', name: 'imprimerPanne', methods: ['GET'])]
    public function indexP(PanneRepository $PanneRepository, RoleListRepository $roleListRepository, RoleModuleRepository $roleModuleRepository): Response
    {
        $this->verifierSession("PAN", $roleListRepository, $roleModuleRepository);
        $pannes = $PanneRepository->findAll();
        $html = $this->renderView('back/panne/imprimerPanne.html.twig', [
            'pannes' => $pannes
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

        $dompdf->stream("listPanne.pdf", ["Attachement" => true]);

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
