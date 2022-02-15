<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EditorController extends AbstractController
{   
    
    #[Route('/editor', name: 'editor')]
    public function index(ManagerRegistry $doctrine): Response
    {   
        $repo = $doctrine -> getRepository(Product::class);
        $products = $repo -> findAll();
        $notebooks = $repo->findBy(['catalogue'=>1]);
        $stickers = $repo->findBy(['catalogue'=>2]);
        return $this->render('editor/index.html.twig',[
            'products' => $products,
            'notebooks' => $notebooks,
            'stickers' => $stickers,
        ]);
    }

    #[Route('/editor/notebooks', name: 'display_notebooks')]
    public function notebooks(ManagerRegistry $doctrine): Response
    {   
        $repo = $doctrine -> getRepository(Product::class);
        $notebooks = $repo->findBy(['catalogue'=>1]);
        $stickers = $repo->findBy(['catalogue'=>2]);
        return $this->render('editor/notebooks.html.twig',[
            
            'notebooks' => $notebooks,
            'stickers' => $stickers,
            
        ]);
    }

    #[Route('/editor/stickers', name: 'display_stickers')]
    public function stickers(ManagerRegistry $doctrine): Response
    {   
        $repo = $doctrine -> getRepository(Product::class);
        $notebooks = $repo->findBy(['catalogue'=>1]);
        $stickers = $repo->findBy(['catalogue'=>2]);
        return $this->render('editor/stickers.html.twig',[
            
            'notebooks' => $notebooks,
            'stickers' => $stickers,
        ]);
    }

    
    #[Route('/editor/edit/{id}', name: 'edit_product',requirements:["id"=>"\d+"])]
    #[Route('/editor/add_product', name: 'add_product')]
    public function product(Request $request,ManagerRegistry $doctrine,Product $product=null): Response
    {   if( !$product)
        {
        $product = new Product();
        }
        $form = $this -> createForm(ProductType::class,$product);
        $form -> handleRequest($request);
        if($form -> isSubmitted() && $form -> isValid())
        {
            
            $file = $form->get('file_image')->getData();
            
            $uploads_directory = $this -> getParameter('uploads_directory');
            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            $file -> move(
                $uploads_directory,
                $filename
            );
            $product -> setImage($filename);
            $product -> setDateAj(new \DateTime());
            $manager = $doctrine -> getManager();
            $manager -> persist($product);
            $manager -> flush();
            return $this -> redirectToRoute('editor'); 
        }

        return $this->render('editor/product.html.twig',[
            'ProductForm' => $form -> createView(),
            'editMode' => $product -> getId() !==null,
        ]);
    }

    #[Route('/editor/delete_product/{id}', name: 'delete_product',requirements:["id"=>"\d+"])]
    public function delete(Product $product,ManagerRegistry $doctrine): Response
    {   
        $manager = $doctrine -> getManager();
        $manager -> remove($product);
        $manager->flush();
        return $this -> redirectToRoute('editor');
    }


}
