<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'cart')]
    public function index(SessionInterface $session,ProductRepository $repo): Response
    {   
        $panier = $session -> get('panier',[]);
        $PanierWithData = [];
        foreach($panier as $id => $quantity)
        {
            $PanierWithData[]=[
                'product' => $repo->find($id),
                'quantity' => $quantity,
            ];
        }
        $total =0;
        foreach($PanierWithData as $item)
        {
            $totalItem = $item['product'] -> getprice() * $item['quantity'];
            $total += $totalItem;
        }
        
        return $this->render('cart/index.html.twig', [
            'items' => $PanierWithData,
            'total' => $total,
        ]);
    }

    #[Route('/cart/add/{id}', name: 'cart_add')]
    public function add($id,SessionInterface $session)
    {
        
        $quantite =(int)$_POST['quantity'];
        $panier = $session->get('panier',[]);
        if(!empty($panier[$id]))
        {
            $panier[$id]=$quantite;
            
        }else{
            $panier[$id]=$quantite;
        }
        $session -> set('panier',$panier);
        
        return $this->redirectToRoute('blog');
    }

    #[Route('/cart/remove/{id}', name: 'cart_remove')]
    public function remove(SessionInterface $session,$id)
    {
        $panier = $session -> get('panier',[]);
        if(!empty($panier[$id]))
        {
            unset($panier[$id]);
        }
        $session ->set('panier',$panier);
        return $this->redirectToRoute('cart');
    }
}
