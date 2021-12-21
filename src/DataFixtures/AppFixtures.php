<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Category;
use App\Entity\Post;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i=1; $i < 11; $i++) { 
            $categorie = new Category();
            $categorie->setName("Categorie n°$i");

                for ($j=1; $j < 11; $j++) { 
                    $post = new Post();
                    $post->setTitle("Article n°". $i * $j);
                    $post->setContent("Lorem ipsum dolor sit amet consectetur adipisicing elit. Ab, voluptatem ut eveniet alias quibusdam fugiat. Porro necessitatibus totam in earum minus dolor nostrum vitae, impedit temporibus nesciunt optio error ut?
                    Exercitationem voluptas, enim obcaecati magnam possimus debitis nostrum magni impedit similique molestias rerum, eum corporis provident delectus optio eligendi totam aliquam iste culpa? Incidunt quidem nisi dolorem soluta voluptatibus omnis?
                    At non cupiditate, dolorum veritatis quibusdam ullam nihil id quidem, nesciunt consequuntur rerum, iure molestiae eum sequi sit veniam maxime temporibus eos tempore. Molestiae repudiandae voluptate officiis, quam dolor unde.");
                    $post->setCreatedAt(new \DateTime());
                    $post->setCategory($categorie);
                    $manager->persist($post);
                }
            $manager->persist($categorie);
        }

        $manager->flush();
    }
}
