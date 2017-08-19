<?php

namespace BlogBundle\Form\Type;

use BlogBundle\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
   # const $type = "PostStatus";

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $lookupService = $options['lookup'];
        $type = 'PostStatus';

        $choices = $lookupService->items($type);

        $builder->add('title', TextType::class, ['required' => false])
                 ->add('content', TextareaType::class, ['required' => false])
                 ->add('tags', TextType::class, ['required' => false])
                 ->add('status', ChoiceType::class, [
                     'choices' => $lookupService->items($type)
                 ], ['required' => false])
                 ->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class
        ]);
        $resolver->setRequired('lookup');
    }
}