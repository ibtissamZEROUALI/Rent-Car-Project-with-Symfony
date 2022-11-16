<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\User;
use App\Entity\RoleModule;
use App\Repository\UserRepository;
use App\Repository\RoleListRepository;
use App\Repository\RoleModuleRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class BackController extends AbstractController
{

    #[Route('/backHome', name: 'backHome')]
    public function index(): Response
    {
        $session = new Session();
        if ($session->get("connect") != "OK" || $session->get("user") == null) {
            return $this->redirect('/login');
        } else {
        return $this->render('back/base.html.twig');
        }
    }

    #[Route('/indexUser', name: 'indexUser')]
    public function indexUser(UserRepository $userRepository, RoleListRepository $roleListRepository, RoleModuleRepository $roleModuleRepository): Response
    {
        $this->verifierSession("ADM", $roleListRepository, $roleModuleRepository);
        $utilisateurs = $userRepository->findAll();
        return $this->render('back/User/index.html.twig', [
            'utilisateurs' => $utilisateurs
        ]);
    }


    #[Route('/createUser', name: 'createUser', methods: ['GET', 'POST'])]
    public function createUser(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger, UserRepository $repository, RoleListRepository $roleListRepository, RoleModuleRepository $roleModuleRepository): Response
    {
        $this->verifierSession("ADM", $roleListRepository, $roleModuleRepository);
            $roleList = $roleListRepository->findAll();

            if ($request->getMethod() == "POST") {
                $entityManager = $doctrine->getManager();
                $Nom = $request->get("Nom");
                $Prenom = $request->get("Prenom");
                $Email = $request->get("Email");
                $Password = $request->get("Password");
                $Cin = $request->get("Cin");
                $Adresse = $request->get("Adresse");
                $Sexe = $request->get("Sexe");
                $DateNaissance = $request->get("DateNaissance");
                $Photo = $request->get("Photo");
                $date = new \DateTime($DateNaissance);
                $dateCurrent = new \DateTime('now');
                if (filter_var($Email, FILTER_VALIDATE_EMAIL)) {
                    if (strlen($Password) >= 8 && strlen($Password) <= 16) {
                        if (!empty($Password)) {
                            $mem = $repository->findOneByEmail($Email);
                            $mec = $repository->findOneByCin($Cin);
                            if ($mem || $mec) {
                                $response = "Administrateur existant, veuillez changer d'adresse mail ou cin";
                            } else {
                                if($date > $dateCurrent){
                                    $utilisateur = new User();
                                    $utilisateur->setNom($Nom);
                                    $utilisateur->setPrenom($Prenom);
                                    $utilisateur->setEmail($Email);
                                    $utilisateur->setPassword($Password);
                                    $utilisateur->setCin($Cin);
                                    $utilisateur->setAdresse($Adresse);
                                    $utilisateur->setSexe($Sexe);
                                    $utilisateur->setDateNaissance($date);
                                    if ($Photo) {
                                    $originalFilename = pathinfo($Photo->getClientOriginalName(), PATHINFO_FILENAME);
                                    // this is needed to safely include the file name as part of the URL
                                    $safeFilename = $slugger->slug($originalFilename);
                                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $Photo->guessExtension();

                                    // Move the file to the directory where brochures are stored
                                    try {
                                        $Photo->move(
                                            $this->getParameter('vehiculeType_directory'),
                                            $newFilename
                                        );
                                    } catch (FileException $e) {
                                        // ... handle exception if something happens during file upload
                                    }
                                    $utilisateur->setPhoto($newFilename);
                                    }
                                    $entityManager->persist($utilisateur);

                                    for ($i = 0; $i < count($roleList); $i++) {
                                        $Permition = '';
                                        if (isset($_POST[$roleList[$i]->getId()])) {
                                            $Permition = 1;
                                        } else {
                                            $Permition = 0;
                                        }
                                        $roles = new RoleModule();
                                        $roles->setPermition($Permition);
                                        $roles->setIdUser($utilisateur);
                                        $roles->setIdRoleList($roleList[$i]);
                                        $entityManager->persist($roles);
                                        $entityManager->flush();
                                    }
                                    $entityManager->flush();
                                    return $this->redirectToRoute('indexUser');
                                }else{
                                   return $this->render('back/User/ajouter.html.twig', ['roleList' => $roleList,'erreur' => "Veuillez definir une date de naissance valide !!"]);
                                }
                            }
                        } else {
                            return $this->render('back/User/ajouter.html.twig', ['roleList' => $roleList,'erreur' => "Veuillez definir un password !!" ]);
                        }
                    } else {
                        return $this->render('back/User/ajouter.html.twig', ['roleList' => $roleList,'erreur' => "Le mot de passe doit comprendre entre 8 et 16 caractéres"]);
                    }
                } else {
                    return $this->render('back/User/ajouter.html.twig', ['erreur' => "Email incorrect !!", 'roleList' => $roleList]);
                }
            }
        return $this->render('back/User/ajouter.html.twig', [
            'roleList' => $roleList
        ]);
    }

    #[Route('/indexUser/editUser/{id}', name: 'editUser', methods: ['GET', 'POST'])]
    public function edit(UserRepository $repository_user, Request $request, $id, ManagerRegistry $doctrine, SluggerInterface $slugger, RoleListRepository $roleListRepository, RoleModuleRepository $roleModuleRepository): Response
    {
        $entityManager = $doctrine->getManager();
        $utilisateur = $repository_user->find($id);
        $roleList = $roleListRepository->findAll();
        $this->verifierSession("ADM", $roleListRepository, $roleModuleRepository);
        if ($request->getMethod() == "POST") {

            if ($utilisateur) {
                $Nom = $request->get("Nom");
                $Prenom = $request->get("Prenom");
                $Email = $request->get("Email");
                $Password = $request->get("Password");
                $Cin = $request->get("Cin");
                $Adresse = $request->get("Adresse");
                $DateNaissance = $request->get("DateNaissance");
                $Sexe = $request->get("Sexe");
                $Photo = $request->get("Photo");
                $date = new \DateTime($DateNaissance);
                $dateCurrent = new \DateTime('now');
                if (filter_var($Email, FILTER_VALIDATE_EMAIL)) {
                    if (strlen($Password) >= 8 && strlen($Password) <= 16) {
                        if (!empty($Password)) {
                                if($date > $dateCurrent){
                                    $utilisateur = new User();
                                    $utilisateur->setNom($Nom);
                                    $utilisateur->setPrenom($Prenom);
                                    $utilisateur->setEmail($Email);
                                    $utilisateur->setPassword($Password);
                                    $utilisateur->setCin($Cin);
                                    $utilisateur->setAdresse($Adresse);
                                    $utilisateur->setSexe($Sexe);
                                    $utilisateur->setDateNaissance($date);
                                    if ($Photo) {
                                    $originalFilename = pathinfo($Photo->getClientOriginalName(), PATHINFO_FILENAME);
                                    // this is needed to safely include the file name as part of the URL
                                    $safeFilename = $slugger->slug($originalFilename);
                                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $Photo->guessExtension();

                                    // Move the file to the directory where brochures are stored
                                    try {
                                        $Photo->move(
                                            $this->getParameter('vehiculeType_directory'),
                                            $newFilename
                                        );
                                    } catch (FileException $e) {
                                        // ... handle exception if something happens during file upload
                                    }
                                    $utilisateur->setPhoto($newFilename);
                                    }
                                    $entityManager->persist($utilisateur);

                                    for ($i = 0; $i < count($roleList); $i++) {
                                        $Permition = '';
                                        if (isset($_POST[$roleList[$i]->getId()])) {
                                            $Permition = 1;
                                        } else {
                                            $Permition = 0;
                                        }
                                        $roles = new RoleModule();
                                        $roles->setPermition($Permition);
                                        $roles->setIdUser($utilisateur);
                                        $roles->setIdRoleList($roleList[$i]);
                                        $entityManager->persist($roles);
                                        $entityManager->flush();
                                    }
                                    $entityManager->flush();
                                    return $this->redirectToRoute('indexUser');
                                }else{
                                   return $this->render('back/User/ajouter.html.twig', ['roleList' => $roleList,'erreur' => "Veuillez definir une date de naissance valide !!"]);
                                }
                        } else {
                            return $this->render('back/User/ajouter.html.twig', ['roleList' => $roleList,'erreur' => "Veuillez definir un password !!" ]);
                        }
                    } else {
                        return $this->render('back/User/ajouter.html.twig', ['roleList' => $roleList,'erreur' => "Le mot de passe doit comprendre entre 8 et 16 caractéres"]);
                    }
                } else {
                    return $this->render('back/User/ajouter.html.twig', ['erreur' => "Email incorrect !!", 'roleList' => $roleList]);
                }
            } else {
                $this->addFlash('erreur', "Utilisateur innexistante");
            }
        }
        return $this->render('back/User/Edit.html.twig', [
            'utilisateur' => $utilisateur
        ]);
    }

    #[Route('/indexUser/deleteUser/{id}/', name: 'deleteUser', methods: ['GET', 'DELETE'])]
    public function  delete(ManagerRegistry $doctrine, UserRepository $repository_user, $id, RoleListRepository $roleListRepository, RoleModuleRepository $roleModuleRepository): Response
    {

        $this->verifierSession("ADM", $roleListRepository, $roleModuleRepository);
        $utilisateur = $repository_user->find($id);
        $entityManager = $doctrine->getManager();
        $entityManager->remove($utilisateur);
        $entityManager->flush();
        return $this->redirectToRoute('indexUser');

        /* $em = $doctrine->getManager();
        $response = "no";
        $evenement = $repository_user->find($id);
        $em->remove($evenement);
        $em->flush();
        $response = "ok";
        return new Response($response);*/
    }

    #[Route('/imprimerUser', name: 'imprimerUser', methods: ['GET'])]
    public function indexP(UserRepository$userRepository, RoleListRepository $roleListRepository, RoleModuleRepository $roleModuleRepository): Response
    {
        $this->verifierSession("ADM", $roleListRepository, $roleModuleRepository);
        $utilisateurs = $userRepository->findAll();
        $html = $this->renderView('back/User/imprimer.html.twig', [
            'utilisateurs' => $utilisateurs
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

        $dompdf->stream("list.pdf", ["Attachement" => true]);

        // Send some text response
        return new Response("The PDF file has been succesfully generated !");
    }


    #[Route('/login', name: 'login')]
    public function login(Request $request, UserRepository $userRepository, ManagerRegistry $doctrine)
    {
        $session = new Session();
        if ($session->get("connect") == "OK" && $session->get("user") != null) {
            return $this->redirectToRoute('backHome');
        } else {
            $response = "";

            if ($request->getMethod() == "POST") {

                $email = $request->get("login");
                $pass = $request->get("pass");
                $entityManager = $doctrine->getManager();
                if ($email == "" || $pass == "") {
                    $response = "Veuillez renseigner  l'email d'utilisateur et le mot de passe";
                } else {
                    $ent = $userRepository->findOneby(array("Email" => $email, "Password" => $pass));
                    if ($ent) {
                        $u = clone $ent;
                        $session->set("connect", "OK");
                        $session->set("user", $u);
                        $ent->setDernierecnx(new \DateTime());
                        $entityManager->flush();

                        return $this->redirectToRoute('backHome');
                    } else {
                        $response = "Nom d'utilisateur ou mot de passe incorrect";
                    }
                }
            }
            return $this->render('back/User/login.html.twig', array("response" => $response));
        }
    }

    #[Route('/logout', name: 'logout')]
    public function logout()
    {
        $session = new Session();
        $session->set("connect", "NO");
        $session->set("user", null);
        return $this->redirectToRoute('login');
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
