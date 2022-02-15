<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    #[Route('/signup', name: 'signup')]
    public function signup(Request $request,ManagerRegistry $doctrine,UserPasswordHasherInterface $encoder): Response
    {
        $user = new User();
        $form = $this-> createForm(UserType::class,$user);
        $form -> handleRequest($request);
        if($form -> isSubmitted() && $form ->isValid())
        {
            $manager = $doctrine -> getManager();
            //hashing the password
            $hash = $encoder -> hashPassword($user,$user->getPassword());
            $user -> setPassword($hash);
            $manager -> persist($user);
            $manager ->flush();
            return $this -> redirectToRoute('signin');
        }
        return $this->render('security/signup.html.twig',[
            'UserForm' => $form -> createView()
        ]);
    }

    #[Route('/signin', name: 'signin')]
    public function signin(): Response
    {
        return $this->render('security/signin.html.twig');
    }

    #[Route('/logout', name: 'logout')]
    public function logout()
    {
        
    }
}
