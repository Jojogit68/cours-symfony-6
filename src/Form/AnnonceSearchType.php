<?php

namespace App\Form;

use App\Entity\Annonce;
use App\Entity\AnnonceSearch;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AnnonceSearchType extends AbstractType
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Titre'
                ]

            ])
            ->add('maxPrice', IntegerType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Prix maximum'
                ]
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Très mauvais' => Annonce::STATUS_VERY_BAD,
                    'Mauvais'      => Annonce::STATUS_BAD,
                    'Bon' => Annonce::STATUS_GOOD,
                    'Très bon' => Annonce::STATUS_VERY_GOOD,
                    'Parfait' => Annonce::STATUS_PERFECT
                ],
                'label' => false,
                'required' => false,
                'placeholder' => 'État',
            ])
            ->add('createdAt', DateType::class, [
                'widget' => 'single_text',
                'label' => false,
                'required' => false,
            ])
            ->add('tags', EntityType::class, [
                'required' => false,
                'label' => false,
                'class' => Tag::class,
                'choice_label' => 'name',
                'multiple' => true
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AnnonceSearch::class,
            'method' => 'get',
            'csrf_protection' => false,
            'action' => $this->urlGenerator->generate('app_annonce_search'), // on définit l'action qui doit traiter le formulaire. Si cette option n'est pas renseignée, le formulaire sera traité par la page en cours, ce qui n'est pas ce que l'on souhaite (tu peux essayer d'enlever cette option et envoyer le formulaire pour voir)
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}