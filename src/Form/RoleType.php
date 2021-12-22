<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class RoleType extends AbstractType
{

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($this->security->isGranted("ROLE_SUPER_ADMIN")) {
            $builder
                ->add('user', EntityType::class, [
                    "class" => User::class,
                    "choice_label" => 'email',
                    "mapped" => false
                ])
                ->add('roles', ChoiceType::class, [
                    "choices" => [
                        "ROLE_ADMIN" => "ROLE_ADMIN",
                        "ROLE_SUPER_ADMIN" => "ROLE_SUPER_ADMIN"
                    ],
                    "multiple" => true
                ])
                ->add("Modifier", SubmitType::class);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
