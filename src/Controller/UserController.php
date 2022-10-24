<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/index', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository,UserPasswordHasherInterface $encoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //met tout en 0 et pas en +33
            $user->setTelephone(str_replace("+33","0",$user->getTelephone()));
            //hash le mot de passe
            $hash = $encoder->hashPassword($user , $user->getPassword());
            $user->setPassword($hash);
            //ajoute un role à l'utilisateur
            $user->setRoles(['ROLE_USER']);
            if($userRepository->findOneBy(['telephone'=>$user->getTelephone()])){
                $this->addFlash('error', 'Ce numéro de télephone est déja associé à un compte.');
            }
            else{
                $userRepository->add($user, true);
                // envoie un message de succes
                $this->addFlash('success', 'Un mail vous à été envoyer, afin de procéder à une verification de votre compte.');
            }
            
            
        }
        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository,UserPasswordHasherInterface $encoder): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->hashPassword($user , $user->getPassword());
            $user ->setPassword($hash);
            $userRepository->add($user, true);

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManage): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $user->setDateDelete(new \DateTime('@'.strtotime('now')));
            $entityManager->persist($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/login', name: 'app_user_login', methods: [])]
    public function editLastDateLogin(User $user, EntityManagerInterface $entityManager): Response
    {
        $user->setLastDateLogin(new \DateTime());
        $entityManager->persist( $user);
        $entityManager->flush();
        return $this->redirectToRoute('produit_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/thsi/logout', name: 'app_user_logout', methods: ['GET'])]
    public function editLastDateLogout(User $user, EntityManagerInterface $entityManager): Response
    {
        dd('logout');
    }
    
}
