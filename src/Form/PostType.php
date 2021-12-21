<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
