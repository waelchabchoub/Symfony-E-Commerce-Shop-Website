<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AdminType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $repo = $doctrine -> getRepository(User::class);
        $users = $repo -> findAll();
        return $this->render('admin/index.html.twig', [
            'users' => $users,
        ]);
    }
    #[Route('/admin/edit/{id}', name: 'edit_user',requirements:["id"=>"\d+"])]
    #[Route('/admin/add', name: 'add_user')]
    public function user_form(ManagerRegistry $doctrine,Request $request,UserPasswordHasherInterface $encoder,User $user=null): Response
    {   
        if(! $user) 
        {
            $user = new User();
        }
        $form = $this -> createForm(AdminType::class,$user);
        $form -> handleRequest($request);
        if($form -> isSubmitted() && $form -> isValid())
        {
            $manager = $doctrine -> getManager();
            //hashing the password
            $hash = $encoder -> hashPassword($user,$user->getPassword());
            $user -> setPassword($hash);
            $manager -> persist($user);
            $manager -> flush();
            return $this -> redirectToRoute('admin');
        }
        return $this->render('admin/user_form.html.twig', [
            'UserForm' => $form ->createView(),
            'editMode' => $user -> getId() !==null,
        ]);
    }
    #[Route('/admin/delete/{id}', name: 'delete_user')]
    public function delete(User $user,ManagerRegistry $doctrine): Response
    {
        $manager = $doctrine -> getManager();
        $manager -> remove($user);
        $manager -> flush();
        return $this->redirectToRoute('admin');
    }
}
