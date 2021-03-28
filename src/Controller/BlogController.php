<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class BlogController extends AbstractController

{
    #[Route('/blog', name: 'blog')]
    public function index(): Response
    {
        $Repo = $this->getDoctrine()->getRepository(Article::class);

        $articles = $Repo->findAll();
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles'=>$articles
        ]);
    }

    /**
     * @Route("/", name="home")
     */
    public function home(){
        return $this->render('blog/home.html.twig');
    }

    /**
     * @Route("/blog/new", name="blog_create")
     * @Route("/blog/{id}/edit", name="blog_edit")
     */
    public function myForm(Article $article=null, Request $request, EntityManagerInterface $manager){
        if(!$article){
            $article = new Article();
        }
        
        $form = $this->createFormBuilder($article)
                     ->add('title')
                     ->add('content')
                     ->add('image')
                     ->getForm();

        $form->handleRequest($request);   
        
        if($form->isSubmitted() && $form->isValid()){
            if(!$article->getId()){
               $article->setCreatedAt(new \DateTime());
            }
            
            $manager->persist($article);
            $manager->flush();
            return $this->redirectToRoute('blog_show',[
                'id'=>$article->getId()
            ]);
        }

        return $this->render('blog/Create.html.twig', [
            'formArticle'=>$form->createView(),
            'editMode'=>$article->getId()!== null
        ]);
    }

    /**
    * @Route("/blog/{id}", name="blog_show")
    */
    public function show(Article $article){
       return $this->render('blog/show.html.twig',[
           'article' => $article
       ]);
    }

    /**
    * @Route("/blog/{id}/delete", name="blog_delete")
    */
    public function delete(Article $article, EntityManagerInterface $manager ){
        $manager->remove($article);
        $manager->flush();
        return $this->redirectToRoute('blog'
            );
     }

}
