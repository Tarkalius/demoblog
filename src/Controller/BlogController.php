<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class BlogController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render('blog/home.html.twig', [
            'slogan' => "La démo d'un blog",
            'age' => 28
        ]);
        // pour envoyer des variables depuis le controller, la méthode render() prend en 2ème arg un tableau associatif
    }

    /**
     * @Route("/blog", name="app_blog")
     */
    // une route est définie par 2 arguments : son chemin (/blog) et son nom (app_blog)
    // aller sur une route permet de lancer la méthode qui se trouve directement en-dessous

    // les méthodes d'un controller renvoient TOUJOURS un objet de classe Response
    public function index(ArticleRepository $repo): Response
    {
        // pour récuperer le repository, je le passe en arg de la méthode index()
        // cela s'appelle une injection de dépendance

        $articles = $repo->findAll();
        // j'utilise findAll() pour récupérer tous les articles en BDD

        return $this->render('blog/index.html.twig', [
            'articles' => $articles // j'envoie les articles au template
        ]);

        // render() permet d'afficher le contenu d'un template
    }

    /**
     * @Route("/blog/show/{id}", name="blog_show")
     */
    public function show($id, ArticleRepository $repo)   // $id correspond au {id} (param de route) dans l'URL
    {
        $article = $repo->find($id);
        // find() permet de récupérer 1 article en fonction de son id

        return $this->render('blog/show.html.twig', [
            'item' => $article
        ]);
    }

    /**
     * @Route("/blog/new", name="blog_create")
     * @Route("/blog/edit/{id}", name="blog_edit")
     */
    public function form(Request $globals, EntityManagerInterface $manager, Article $article = null)
    {
        // la classe Request contient les données véhiculées par les superglobales ($_POST, $_GET, $_SERVER...)

        if($article == null)
        {    
            $article = new Article; // je crée un objet de la classe Article vide prêt à être rempli
            $article->setCreatedAt(new \DateTime);  // ajout de la date seulement à l'insertion d'un article

            // Si $article est null, nous sommes dans la route blog_create : nous devons créer un nouvel article
            // sinon, $article n'est pas null, nous sommes dans la route blog_edit : nous récupérons l'article correspondant à l'id
        }

        $form = $this->createForm(ArticleType::class, $article);  // je lie le formulaire à mon objet $article
        // createForm() permet de récupérer un formulaire

        $form->handleRequest($globals);

        // dump($globals); // permet d'afficher les données de l'objet $globals (comme var_dump())
        // dump($article);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($article);    // prépare l'insertion de l'article en bdd
            $manager->flush();  // exécute la requête d'insertion

            return $this->redirectToRoute('blog_show', [
                'id' => $article->getId()
            ]);
            // cette méthode permet de nous rediriger vers la page de notre article nouvellement crée
        }

        return $this->renderForm("blog/form.html.twig", [
            'formArticle' => $form,
            'editMode' => $article->getId() !== null

            // si nous sommes sur la route /new : editMode = 0
            // sinon, editMode = 1
        ]);
    }

    /**
     * @Route("/blog/delete/{id}", name="blog_delete")
     */
    public function delete($id, EntityManagerInterface $manager, ArticleRepository $repo)
    {
        $article = $repo->find($id);

        $manager->remove($article); // prépare la suppression
        $manager->flush();  // exécute la suppression

        return $this->redirectToRoute('app_blog');  // redirection vers la liste des articles
    }
}
