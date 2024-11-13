<?php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'attr' => [
                    'class' => 'block w-full p-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500'
                ]
            ])
            ->add('password', PasswordType::class, [
                'attr' => [
                    'class' => 'block w-full p-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500'
                ]
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Admin' => 'ROLE_ADMIN',
                    'User' => 'ROLE_USER',
                    // Add any other roles you want to allow
                ],
                'expanded' => true, // To show checkboxes instead of a dropdown
                'multiple' => true, // Allow multiple selections
                'mapped' => true, // Map this field to the `roles` property
                'attr' => [
                    'class' => 'space-y-2' // For spacing between checkboxes
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
