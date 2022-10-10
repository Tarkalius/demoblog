<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        // créer 3 catégories
        for($i = 1; $i <= 3; $i++)
        {
            $category = new Category;
            $category->setTitle($faker->sentence(3, false));    // renvoie 3 mots aléatoire

            $manager->persist($category);

            // créer entre 4 et 6 articles par catégorie
            for($j = 1; $j <= mt_rand(4, 6); $j++)
            {
                // mt_rand() renvoie un nombre aléatoire parmi les arg

                $article = new Article;

                $content = '<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>';
                // join() prend en param un séparateur et un tableau et renvoie une chaine de caractères

                $article->setTitle($faker->sentence())
                        ->setContent($content)
                        ->setImage($faker->imageUrl())
                        ->setCreatedAt($faker->dateTimeBetween("-6 months"))
                        ->setCategory($category);

                $manager->persist($article);

                // créer entre 5 et 10 commentaires par article
                for($k = 1; $k <= mt_rand(5, 10); $k++)
                {
                    $comment = new Comment;

                    $content = '<p>' . join('</p><p>', $faker->paragraphs(2)) . '</p>';

                    $now = new \DateTime;   // récupération de la date d'aujourd'hui
                    $interval = $now->diff($article->getCreatedAt());   // intervalle entre aujourd'hui et création de l'article
                    $days = $interval->days;    // on récupère l'intervalle en jours

                    $comment->setAuthor($faker->name())
                            ->setContent($content)
                            ->setCreatedAt($faker->dateTimeBetween('-' . $days . ' days'))
                            ->setArticle($article);

                    $manager->persist($comment);
                }
            }
        }

        $manager->flush();
        // flush() permet d'exécuter la requête SQL préparée grâce à persist()
    }
}
