<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Product;
use App\Form\CommentsType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    #[Route('/', name: 'blog')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $repo = $doctrine -> getRepository(Product::class);
        $products = $repo -> findAll();
        $notebooks = $repo->findBy(['catalogue'=>1]);
        $stickers = $repo->findBy(['catalogue'=>2]);
        return $this->render('blog/index.html.twig',[
            'products' => $products,
            'notebooks' => $notebooks,
            'stickers' => $stickers,
        ]);
        
    }
    #[Route('/blog/notebooks', name: 'display_notebooks_blog')]
    public function notebooks(ManagerRegistry $doctrine): Response
    {   
        $repo = $doctrine -> getRepository(Product::class);
        $notebooks = $repo->findBy(['catalogue'=>1]);
        $stickers = $repo->findBy(['catalogue'=>2]);
        return $this->render('blog/notebooks.html.twig',[
            
            'notebooks' => $notebooks,
            'stickers' => $stickers,
            
        ]);
    }

    #[Route('/blog/stickers', name: 'display_stickers_blog')]
    public function stickers(ManagerRegistry $doctrine): Response
    {   
        $repo = $doctrine -> getRepository(Product::class);
        $notebooks = $repo->findBy(['catalogue'=>1]);
        $stickers = $repo->findBy(['catalogue'=>2]);
        return $this->render('blog/stickers.html.twig',[
            
            'notebooks' => $notebooks,
            'stickers' => $stickers,
        ]);
    }
    
    #[Route('/blog/product/{id}', name: 'product_details')]
    public function product(Product $product,Request $request,ManagerRegistry $doctrine): Response
    {
        $user = $this -> getUser();
        $comment = new Comments();
        $form = $this -> createForm(CommentsType::class,$comment);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $comment -> setDateCom(new \DateTime());
            $comment -> setArticle($product);
            $comment -> setAuthor($user->getUserIdentifier());
            $manager = $doctrine -> getManager();
            $manager -> persist($comment);
            $manager -> flush();
        }

        
        return $this->render('blog/product.html.twig',[
            'product' => $product,
            'CommentForm' => $form -> createView(),
            
        ]);
    }

}
