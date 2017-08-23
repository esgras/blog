<?php

namespace BlogBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', TextType::class, [
                                'label' => false,
                                'attr' => ['placeholder' => 'Username'],
                                'required' => false,
                                'constraints' =>
                                    new NotBlank(['message' => 'Value Required'])
                                ,
                    ])->add('password', PasswordType::class, [
                                'label' => false,
                                'attr' => ['placeholder' => 'Password'],
                                'required' => false
                    ])->add('submit', SubmitType::class, [
                            'attr' => ['class' => 'btn btn-primary']
                    ]);
    }

    public function getName()
    {
        return 'article';
    }
}