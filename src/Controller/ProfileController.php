<?php

namespace App\Controller;

use App\Repository\CommandeRepository;
use App\Repository\PanierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'profile')]
    public function index(PanierRepository $PanierRepo,CommandeRepository $CommandeRepo): Response
    {   
        $user = $this -> getUser();
        $paniers = $PanierRepo -> findBy(['user'=>$user]);
        $commandes = $CommandeRepo -> findBy(['panier'=>$paniers]);
        
        return $this->render('profile/index.html.twig', [
            'commandes' => $commandes,
        ]);
    }
}
