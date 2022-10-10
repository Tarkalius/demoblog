<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // l'objet $builder permet de créer un formulaire
        // la méthode add() permet d'ajouter un champ au formulaire
        $builder
            ->add('title')
            ->add('content') // je modifie le champ content en tant qu'input text
            ->add('image')
            ->add('category', EntityType::class, [  // on indique qu'il faut récupérer les categories dans une entity
                'class' => Category::class, // on précise le nom de l'entity
                'choice_label' => 'title'   // on indique quoi afficher à l'utilisateur
            ])
            // ->add('createdAt')
            // nous commentons ce champ car la date d'ajout sera ajoutée automatiquement lors de l'insertion de l'article
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
