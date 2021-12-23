<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $choices = $this->createChoices();
        $builder
            ->add('title', TextType::class, [
                "label" => "Titre:",
                "attr" => ["class" => "form-group"]
            ])
            ->add('content', TextareaType::class, [
                "label" => "Contenu de l'article"
            ])
            // Lorsqu'on a une relation entre 2 entités et qu'on doit ajouter le choix d'un élément d'une entité
            // au formulaire d'une autre entité, on utilise l'EntityType
            ->add('category', EntityType::class, [
                // L'entityType nous oblige a utiliser 2 options:
                // class qui indique à quelle entité on fait référence
                "class" => Category::class,
                // choice_label qui représente la propriété qui va être utilisée pour l'affichage des options
                // ici name pour le name des catégories
                "choice_label" => "name"
            ])
            ->add('picture', FileType::class, ["label" => "Image (JPEG/PNG)", "required" => false])
            ->add("Ajouter", SubmitType::class)

            ->add("select", ChoiceType::class, [
                "choices" => $choices,
                "mapped" => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }

    public function createChoices()
    {
        $choices = array();
        for ($i=0; $i < 10; $i++) { 
            $choices[] = $i;
        }
        return $choices;
    }
}
