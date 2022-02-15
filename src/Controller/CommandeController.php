<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Panier;
use App\Entity\Product;
use App\Form\CommandeType;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use PharIo\Manifest\Email;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email as MimeEmail;
use Symfony\Component\Routing\Annotation\Route;

class CommandeController extends AbstractController
{
    #[Route('/commande', name: 'commande')]
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

        return $this->render('commande/index.html.twig',[
            'items' => $PanierWithData,
            'total' => $total,
        ]);
        
    }
    #[Route('/commande/validate', name: 'commande_confirm')]
    public function confirm(SessionInterface $session,ProductRepository $repo,ManagerRegistry $doctrine)
    {   
        
        $cart = $session->get('panier',[]);
        $user = $this->getUser();
        $panier = new Panier();
        $panier -> setDatePn(new \DateTime());
        $panier -> setUser($user);
        
        foreach($cart as $id=>$quantity)
        {
            $article = $repo->find($id);
            $panier -> addArticle($article);
        }
        $manager = $doctrine -> getManager();
        $manager -> persist($panier);
        $manager -> flush();
        $id = $panier -> getId();
       
        
        
        return $this -> redirectToRoute('commande_valider',[
            'id' => $id,
        ]);
    }


    #[Route('/commande/validate/validation/{id}', name: 'commande_valider')]
    public function validate(Request $request,Panier $panier,ManagerRegistry $doctrine,MailerInterface $mailer,SessionInterface $session)
    {
        //handle form
        $commande = new Commande();
        $form = $this->createForm(CommandeType::class,$commande);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            //get form data
            $contactform = $form -> getData();
            //get the session
            $commande -> setDateCm(new \DateTime());
            $commande -> setPanier($panier);
            //insert in db
            $manager = $doctrine -> getManager();
            $manager -> persist($commande);
            $manager -> flush();
            //send email
            $email = (new MimeEmail())
            ->from('wael.chabchoub@aiesec.net')
            ->to('wael.chabchoub@aiesec.net')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Commande')
            ->text('Sending emails is fun again!')
            ->html('<p>This is a template for commande email</p>');

        $mailer->send($email);
            //
            $session->clear();
            return $this -> redirectToRoute('blog');


        }

        return $this->render('commande/validate.html.twig',[
            'CommandeForm' => $form -> createView(),
        ]);

    }
}
