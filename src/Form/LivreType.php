<?php

namespace App\Form;

use App\Entity\Auteur;
use App\Entity\Genre;
use App\Entity\Livre;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class LivreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            // 📘 Titre
            ->add('titre', TextType::class, [
                'label' => 'Titre du livre',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez le titre'
                ]
            ])

            ->add('resume', TextareaType::class, [
    'label' => 'Résumé',
    'attr' => ['class' => 'form-control']
])

->add('isbn', TextType::class, [
    'label' => 'ISBN',
    'attr' => ['class' => 'form-control']
])

->add('nbPages', IntegerType::class, [
    'label' => 'Nombre de pages',
    'attr' => ['class' => 'form-control']
])

            // 📅 Date de publication
            ->add('datePublication', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de publication',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])

            // 👤 Auteur (BDD)
            ->add('auteur', EntityType::class, [
                'class' => Auteur::class,
                'choice_label' => 'nom',
                'label' => 'Auteur',
                'placeholder' => 'Choisir un auteur',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])

            // 🎭 Genre (BDD)
            ->add('genre', EntityType::class, [
                'class' => Genre::class,
                'choice_label' => 'nom',
                'label' => 'Genre',
                'placeholder' => 'Choisir un genre',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])

            // 🏷️ Tags (checkbox multiple)
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'nom',
                'multiple' => true,
                'expanded' => true, // checkbox
                'by_reference' => false, // IMPORTANT ManyToMany
                'label' => 'Tags'
            ])

            // ✅ Disponible
            ->add('disponible', CheckboxType::class, [
                'label' => 'Disponible',
                'required' => false
            ])

            // 🖼️ Image de couverture
            ->add('imageFile', FileType::class, [
                'label' => 'Image de couverture',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                new File(
    maxSize: '2M',
    mimeTypes: ['image/jpeg', 'image/png', 'image/webp'],
    mimeTypesMessage: 'Format invalide (JPEG, PNG, WEBP uniquement)'
)
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])

            // 💾 Bouton submit Bootstrap
            ->add('enregistrer', SubmitType::class, [
                'label' => '💾 Enregistrer le livre',
                'attr' => [
                    'class' => 'btn btn-primary w-100 mt-3'
                ]
            ])
        ;
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Livre::class,
        ]);
    }
}
