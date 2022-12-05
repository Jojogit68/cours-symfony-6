<?php

namespace App\Form;

use App\Entity\Annonce;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnnonceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, ['label' => 'title'])
            ->add('description', null, ['label' => 'description'])
            ->add('price', null, ['label' => 'price'])
            ->add('status', ChoiceType::class, [
                'label' => 'status.word',
                'choices' => [
                    'status.very_bad' => Annonce::STATUS_VERY_BAD,
                    'status.bad' => Annonce::STATUS_BAD,
                    'status.good' => Annonce::STATUS_GOOD,
                    'status.very_good' => Annonce::STATUS_VERY_GOOD,
                    'status.perfect' => Annonce::STATUS_PERFECT
                ]
            ])
            ->add('isSold', null, ['label' => 'sold'])
            ->add('createdAt', DateType::class, [
                'label' => 'created_at',
                'widget' => 'single_text',
                'input'  => 'datetime_immutable'
            ])
            ->add('slug', null, ['label' => 'slug'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Annonce::class,
        ]);
    }
}
