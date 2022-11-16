<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Marque;
use App\Entity\Vehicule;
use App\Entity\FileVehicule;
use App\Entity\TypeVehicule;
use App\Entity\StatusVehicule;
use App\Form\VehiculeFormType;
use App\Form\FileVehiculeFormType;
use App\Form\VehiculeTypeFormType;
use App\Form\VehiculeMarqueFormType;
use App\Repository\MarqueRepository;
use App\Repository\RoleListRepository;
use App\Repository\VehiculeRepository;
use App\Repository\RoleModuleRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\FileVehiculeRepository;
use App\Repository\TypeVehiculeRepository;
use App\Repository\StatusVehiculeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\TypeValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/backHome', name: '')]
class VehiculeController extends AbstractController 
{
    ///// listes////
    #[Route('/ListeVehicule', name: 'ListeVehicule')]
    public function indexVehicule(VehiculeRepository $vehiculeRepository,RoleListRepository $roleListRepository, RoleModuleRepository $roleModuleRepository, MarqueRepository $marqueRepository, TypeVehiculeRepository $typeVehiculeRepository): Response
    {
        $this->verifierSession("STK", $roleListRepository, $roleModuleRepository);
        $vehicules = $vehiculeRepository->findAll();
        $marques = $marqueRepository->findAll();
        $types = $typeVehiculeRepository->findAll();
        if(!$vehicules){
            $this->addFlash('info', "la liste est vide");
        }
              return $this->render('back/vehicule/listeVehicule.html.twig', [
            'vehicules' => $vehicules, 'marques' => $marques, 'types' => $types,
        ]);
    }

    #[Route('/ListeMarque', name: 'ListeMarque')]
    public function indexMarque(MarqueRepository $marqueRepository, RoleListRepository $roleListRepository, RoleModuleRepository $roleModuleRepository): Response
    {
        $this->verifierSession("STK", $roleListRepository, $roleModuleRepository);
        $marques = $marqueRepository->findAll();
        if (!$marques) {
            $this->addFlash('info', "la liste est vide");
        } else {
            return $this->render('back/vehicule/listeMarque.html.twig', [
                'marques' => $marques,
            ]);
        }
       
    }

    #[Route('/ListeType', name: 'ListeType')]
    public function indexType(TypeVehiculeRepository $typeVehiculeRepository, RoleListRepository $roleListRepository, RoleModuleRepository $roleModuleRepository): Response
    {
        $this->verifierSession("STK", $roleListRepository, $roleModuleRepository);
        $types = $typeVehiculeRepository->findAll();
        if (!$types) {
            $this->addFlash('info', "la liste est vide");
        } else {
            return $this->render('back/vehicule/listetype.html.twig', [
                'types' => $types,
            ]);
        }
        
    }

    ///////create//////
    #[Route('/CreateType', name: 'CreateType')]
    public function CreateType(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger, RoleListRepository $roleListRepository, RoleModuleRepository $roleModuleRepository): Response
    {
        $this->verifierSession("STK", $roleListRepository, $roleModuleRepository);
        $type = new TypeVehicule();
        $form = $this->createForm(VehiculeTypeFormType::class, $type);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $photo = $form->get('ImageType')->getData();
            if ($photo) {
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $photo->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $photo->move(
                        $this->getParameter('vehiculeType_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $type->setImageType($newFilename);
                $manager = $doctrine->getManager();
                $manager->persist($type);
                $manager->flush();
                return $this->redirectToRoute('ListeType');
            }
        }
        return $this->render('back/vehicule/createType.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/CreateMarque', name: 'CreateMarque')]
    public function CreateMarque(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger, RoleListRepository $roleListRepository, RoleModuleRepository $roleModuleRepository): Response
    {
        $this->verifierSession("STK", $roleListRepository, $roleModuleRepository);
        $marque = new Marque();
        $form = $this->createForm(VehiculeMarqueFormType::class, $marque);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
           $photo = $form->get('LogoMarque')->getData();
            if ($photo) {
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $photo->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $photo->move(
                        $this->getParameter('vehiculeMarque_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $marque->setLogoMarque($newFilename);
                $manager = $doctrine->getManager();
                $manager->persist($marque);
                $manager->flush();
                return $this->redirectToRoute('ListeMarque');
            }
        }
        return $this->render('back/vehicule/createMarque.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/ListeVehicule/Show/{id}', name: 'ShowVehicule', methods:['GET'])]
    public function ShowVehicule(ManagerRegistry $doctrine,RoleListRepository $roleListRepository, RoleModuleRepository $roleModuleRepository, VehiculeRepository $vehiculeRepository, MarqueRepository $marqueRepository,$id, TypeVehiculeRepository $typeVehiculeRepository): Response{
        $this->verifierSession("STK", $roleListRepository, $roleModuleRepository);
        $vehicule = $vehiculeRepository->find($id);
        $marqueID = $vehicule->getIdMarque();
        $marque = $marqueRepository->find($marqueID);
        $typeID = $vehicule->getIdTypeVehicule();
        $type = $typeVehiculeRepository->find($typeID);
        return $this->render('back/vehicule/showVehicule.html.twig',['vehicule'=> $vehicule, 'marque' => $marque, 'type'=> $type]);
    }

    function addImage2($fileToUpload,$var,$url, RoleListRepository $roleListRepository, RoleModuleRepository $roleModuleRepository){
        $this->verifierSession("STK", $roleListRepository, $roleModuleRepository);
         $response= '';
        if (isset($_POST['submit'])) {
            //Taking the files from input
            $file = $_FILES["$fileToUpload"];
            //Getting the file name of the uploaded file
            $fileName = $_FILES["$fileToUpload"]['name'];
            //Getting the Temporary file name of the uploaded file
            $fileTempName = $_FILES["$fileToUpload"]['tmp_name'];
            //Getting the file size of the uploaded file
            $fileSize = $_FILES["$fileToUpload"]['size'];
            //getting the no. of error in uploading the file
            $fileError = $_FILES["$fileToUpload"]['error'];
            //Getting the file type of the uploaded file
            $fileType = $_FILES["$fileToUpload"]['type'];

            //Getting the file ext
            $fileExt = explode('.', $fileName);
            $fileActualExt = strtolower(end($fileExt));

            //Array of Allowed file type
            $allowedExt = array("jpg", "jpeg", "png", "pdf");

            //Checking, Is file extentation is in allowed extentation array
            if (in_array($fileActualExt, $allowedExt)) {
                //Checking, Is there any file error
                if ($fileError == 0) {
                    //Checking,The file size is bellow than the allowed file size
                    if ($fileSize < 10000000) {
                        //Creating a unique name for file
                        $fileNemeNew = uniqid('', true) . "." . $fileActualExt;
                        //File destination
                        $fileDestination = $url . $var . '/' . $fileNemeNew;
                        $response = $fileDestination;
                        //function to move temp location to permanent location
                        move_uploaded_file($fileTempName, $fileDestination);
                        return $fileDestination;
                        //Message after success
                        echo "File Uploaded successfully";
                    } else {
                        //Message,If file size greater than allowed size
                        echo "File Size Limit beyond acceptance";
                    }
                } else {
                    //Message, If there is some error
                    echo "Something Went Wrong Please try again!";
                }
            } else {
                //Message,If this is not a valid file type
                echo "You can't upload this extention of file";
            }
        }
        return array('response'=>$response);
    }


    #[Route('/CreateVehicule', name: 'CreateVehicule', methods: ['GET', 'POST'])]
    public function CreateVehicule(ManagerRegistry $doctrine,RoleListRepository $roleListRepository, RoleModuleRepository $roleModuleRepository, Request $request, SluggerInterface $slugger, MarqueRepository $marqueRepository, TypeVehiculeRepository $typeVehiculeRepository)
    {
        $this->verifierSession("STK", $roleListRepository, $roleModuleRepository);
        //$var = 'IMG-'; $response = ""; $url = "./uploads/vehicule/";
        $entityManager = $doctrine->getManager();
        $marques = $marqueRepository->findAll();
        $types = $typeVehiculeRepository->findAll();
        
        if($request->getMethod() == "POST") {
            $titre = $request->get("Titre");
            $modelYear = $request->get("ModelYear");
            $capacite = $request->get("Capacite");
            $description = $request->get("Description");
            $dateEenregitrement = $request->get("DateEnregistrement");
            $prix = $request->get("PrixDay");
            $marqueID = $request->get("Marque");
            $typeID = $request->get("Type");
            $marque = $marqueRepository->find($marqueID);
            $type = $typeVehiculeRepository->find($typeID);
            $statut = $request->get("Statut");
            $date = new \DateTime($dateEenregitrement);
            $dateC = new \DateTime('now');
            $Image = $request->get("Image");
            $model = $request->get("Model");
            $matricul = $request->get("matricul");
            $vehicule = new Vehicule();

            /**********************************/

            if(is_numeric($prix) && is_numeric($capacite) && is_numeric($modelYear)){
                if($typeID != '---' && $marqueID != '---' && $statut !='---'){
                    if($date <= $dateC){
                        if ($Image && $Image !== null) {
                            $originalFilename = pathinfo($Image->getClientOriginalName(), PATHINFO_FILENAME);
                            // this is needed to safely include the file name as part of the URL
                            $safeFilename = $slugger->slug($originalFilename);
                            $newFilename = $safeFilename . '-' . uniqid() . '.' . $Image->guessExtension();
                            // Move the file to the directory where brochures are stored
                            try {
                                $Image->move(
                                    $this->getParameter('vehicule_directory'),
                                    $newFilename
                                );
                            } catch (FileException $e) {
                                // ... handle exception if something happens during file upload
                            }
                            $vehicule->setImage($newFilename);
                        }
                        $vehicule->setTitre($titre);
                        $vehicule->setModelYear(intval($modelYear));
                        $vehicule->setCapacity(intval($capacite));
                        $vehicule->setDescription($description);
                        $vehicule->setDateRegistration($date);
                        $vehicule->setPrixDay(floatval($prix));
                        $vehicule->setStatusVehicule($statut);
                        $vehicule->setModel($model);
                        $vehicule->setMatricul($matricul);
                        $vehicule->setIdMarque($marque);
                        $vehicule->setIdTypeVehicule($type);
                        $entityManager->persist($vehicule);
                        $entityManager->flush();
                        return $this->redirectToRoute('ListeVehicule');
                    }else{
                        return $this->render('back/vehicule/CreateVehicule.html.twig', ['marques' => $marques, 'types' => $types, 'erreur' => 'Veuiler entrer une date de naissance valide!!']);
                    }
                }else{
                    return $this->render('back/vehicule/CreateVehicule.html.twig', ['marques' => $marques, 'types' => $types, 'erreur' => 'Un des champs TYPE, MARQUE ou STATUT est vide!!']);
                }
            }
            else{
                return $this->render('back/vehicule/CreateVehicule.html.twig', ['marques' => $marques, 'types' => $types, 'erreur' => 'Prix, Capacite ou ModelYear n\'est pas valide !!']);
            }
            /************************* */ 

        }
        return $this->render('back/vehicule/CreateVehicule.html.twig', ['marques' => $marques, 'types' => $types]);
    }
    //////// edit/////////

    #[Route('/EditMarque/{id}', name: 'EditMarque', methods: ['GET', 'POST'])]
    public function EditMarque(MarqueRepository $marqueRepository,RoleListRepository $roleListRepository, RoleModuleRepository $roleModuleRepository, ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger, $id): Response
    {
        $this->verifierSession("STK", $roleListRepository, $roleModuleRepository);
        $marque = $marqueRepository->find($id);
        $form = $this->createForm(VehiculeMarqueFormType::class, $marque);
        $form->handleRequest($request);
        $photo = $form->get('LogoMarque')->getData();
        if ($form->isSubmitted() && $form->isValid()) {
            if ($photo && $photo !== null ) {
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $photo->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $photo->move(
                        $this->getParameter('vehiculeMarque_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $marque->setLogoMarque($newFilename);
                $marque->setMarque($form->get('Marque')->getData());
                $manager = $doctrine->getManager();
                $manager->flush();
                return $this->redirectToRoute('ListeMarque');
            } else {
                $marque->setMarque($form->get('Marque')->getData());

                $manager = $doctrine->getManager();
                $manager->flush();
                return $this->redirectToRoute('ListeMarque');
            }
        }
        return $this->render('back/vehicule/editMarque.html.twig', ['marque' => $marque ,'form' => $form->createView()]);
    }

    #[Route('/EditType/{id}', name: 'EditType', methods: ['GET', 'POST'])]
    public function EditType(TypeVehiculeRepository $typeVehiculeRepository,RoleListRepository $roleListRepository, RoleModuleRepository $roleModuleRepository,ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger, $id): Response
    {
        $this->verifierSession("STK", $roleListRepository, $roleModuleRepository);
        $type = $typeVehiculeRepository->find($id);
        $form = $this->createForm(VehiculeTypeFormType::class, $type);
        $form->handleRequest($request);
        $photo = $form->get('ImageType')->getData();
        if ($form->isSubmitted() && $form->isValid()) {
            if ($photo && $photo !== null) {
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $photo->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $photo->move(
                        $this->getParameter('vehiculeType_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $type->setImageType($newFilename);
                $type->setTypeVehicule($form->get('TypeVehicule')->getData());
                $manager = $doctrine->getManager();
                $manager->flush();
                return $this->redirectToRoute('ListeType');
            } else {
                $type->setTypeVehicule($form->get('TypeVehicule')->getData());

                $manager = $doctrine->getManager();
                $manager->flush();
                return $this->redirectToRoute('ListeType');
            }
        }
        return $this->render('back/vehicule/editType.html.twig', ['marque' => $type, 'form' => $form->createView()]);
    }

    #[Route('/EditVehicule/{id}', name: 'EditVehicule', methods: ['GET', 'POST'])]
    public function EditVehicule(ManagerRegistry $doctrine, $id, SluggerInterface $slugger, Request $request,RoleListRepository $roleListRepository, RoleModuleRepository $roleModuleRepository, VehiculeRepository $vehiculeRepository, MarqueRepository $marqueRepository, TypeVehiculeRepository $typeVehiculeRepository): Response
    {
        $this->verifierSession("STK", $roleListRepository, $roleModuleRepository);
        $entityManager = $doctrine->getManager();   
        $marques = $marqueRepository->findAll();
        $types = $typeVehiculeRepository->findAll();
        $vehicule = $vehiculeRepository->find($id);

        if ($request->getMethod() == "POST") {
            if($vehicule) {
                $titre = $request->get("Titre");
                $modelYear = $request->get("ModelYear");
                $capacite = $request->get("Capacite");
                $description = $request->get("Description");
                $dateEenregitrement = $request->get("DateEnregistrement");
                $prix = $request->get("PrixDay");
                $marqueID = $request->get("Marque");
                $typeID = $request->get("Type");
                $marque = $marqueRepository->find($marqueID);
                $type = $typeVehiculeRepository->find($typeID);
                $statut = $request->get("Statut");
                $dateC = new \DateTime('now');
                $date = new \DateTime($dateEenregitrement);
                $Image = $request->get("Image");
                $model = $request->get("Model");
                $matricul = $request->get("matricul");

                /********************* */
                if (is_numeric($prix) && is_numeric($capacite) && is_numeric($modelYear)) {
                    if ($typeID != '---' && $marqueID != '---' && $statut != '---') {
                        if ($date <= $dateC) {
                            if ($Image && $Image !== null) {
                                $originalFilename = pathinfo($Image->getClientOriginalName(), PATHINFO_FILENAME);
                                // this is needed to safely include the file name as part of the URL
                                $safeFilename = $slugger->slug($originalFilename);
                                $newFilename = $safeFilename . '-' . uniqid() . '.' . $Image->guessExtension();
                                // Move the file to the directory where brochures are stored
                                try {
                                    $Image->move(
                                        $this->getParameter('vehicule_directory'),
                                        $newFilename
                                    );
                                } catch (FileException $e) {
                                    // ... handle exception if something happens during file upload
                                }
                                $vehicule->setImage($newFilename);
                            }
                            $vehicule->setTitre($titre);
                            $vehicule->setModelYear(intval($modelYear));
                            $vehicule->setCapacity(intval($capacite));
                            $vehicule->setDescription($description);
                            $vehicule->setDateRegistration($date);
                            $vehicule->setPrixDay(floatval($prix));
                            $vehicule->setStatusVehicule($statut);
                            $vehicule->setModel($model);
                            $vehicule->setMatricul($matricul);
                            $vehicule->setIdMarque($marque);
                            $vehicule->setIdTypeVehicule($type);
                            $entityManager->persist($vehicule);
                            $entityManager->flush();
                            return $this->redirectToRoute('ListeVehicule');
                        } else {
                            return $this->render('back/vehicule/CreateVehicule.html.twig', ['marques' => $marques, 'types' => $types, 'erreur' => 'Veuiler entrer une date de naissance valide!!']);
                        }
                    } else {
                        return $this->render('back/vehicule/editVehicule.html.twig', ['marques' => $marques, 'types' => $types, 'erreur' => 'Un des champs TYPE, MARQUE ou STATUT est vide!!']);
                    }
                }
                return $this->redirectToRoute('ListeVehicule');
            } else {
                $this->addFlash('error', "vehicule innexistante");
            }
        }  
        return $this->render('back/vehicule/editVehicule.html.twig', [
            'vehicule' => $vehicule, 'marques' => $marques, 'types' => $types
        ]);
    }
    
    ///////delete////
    #[Route('/DeleteType/{id}', methods: ['GET', 'DELETE'], name: 'DeleteType')]
    public function DeleteType(ManagerRegistry $doctrine,RoleListRepository $roleListRepository, RoleModuleRepository $roleModuleRepository, TypeVehiculeRepository $typeVehiculeRepository, $id): Response
    {
        $this->verifierSession("STK", $roleListRepository, $roleModuleRepository);
        $type = $typeVehiculeRepository->find($id);
        $entityManager = $doctrine->getManager();
        $entityManager->remove($type);
        $entityManager->flush();
        return $this->redirectToRoute('ListeType');
    }

    #[Route('/DeleteMarque/{id}', methods: ['GET', 'DELETE'], name: 'DeleteMarque')]
    public function DeleteMarque(ManagerRegistry $doctrine,RoleListRepository $roleListRepository, RoleModuleRepository $roleModuleRepository, MarqueRepository $marqueRepository, $id): Response
    {
        $this->verifierSession("STK", $roleListRepository, $roleModuleRepository);
        $marque = $marqueRepository->find($id);
        $entityManager = $doctrine->getManager();
        $entityManager->remove($marque);
        $entityManager->flush();
        return $this->redirectToRoute('ListeMarque');
    }

    #[Route('/DeleteVehicule/{id}', methods: ['GET', 'DELETE'], name: 'DeleteVehicule')]
    public function DeleteVehicule(ManagerRegistry $doctrine,RoleListRepository $roleListRepository, RoleModuleRepository $roleModuleRepository, VehiculeRepository $vehiculeRepository, $id): Response
    {
        $this->verifierSession("STK", $roleListRepository, $roleModuleRepository);
        $vehicule = $vehiculeRepository->find($id);
        $entityManager = $doctrine->getManager();
        $entityManager->remove($vehicule);
        $entityManager->flush();
        return $this->redirectToRoute('ListeVehicule');
    }
    #[Route('/ListeVehicule/imprimerVehicule', name: 'imprimerVehicule')]
    public function imprimerVehicule(VehiculeRepository $vehiculeRepository,RoleListRepository $roleListRepository, RoleModuleRepository $roleModuleRepository, MarqueRepository $marqueRepository, TypeVehiculeRepository $typeVehiculeRepository): Response
    {
        $this->verifierSession("STK", $roleListRepository, $roleModuleRepository);
        $vehicules = $vehiculeRepository->findAll();
        $html = $this->renderView('back/vehicule/imprimerVehicule.html.twig', [
            'vehicules' => $vehicules
        ]);
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($pdfOptions);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $output = $dompdf->output();
        $dompdf->stream("listVehicule.pdf", ["Attachement" => true]);
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

