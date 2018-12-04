<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                // input type text
                TextType::class,
                [
                    'label' => 'Titre'
                ]
            )
            ->add(
                'content',
                // input type textarea
                TextareaType::class,
                [
                    'label' => 'Contenu'
                ]
            )
            ->add(
                'category',
                // permet de faire une liste déroulante select sur une entité Doctrine
                EntityType::class,
                [
                    'label' => 'Catégorie',
                    // nom de l'entity sur laquelle on construit un select
                    'class' => Category::class,
                    // nom de l'attribut utilisé pour l'affichage des options
                    'choice_label' => 'name',
                    // pour avoir la première option vide
                    'placeholder' => 'Choisissez une catégorie'
                ]
            )
            ->add(
                'image',
                FileType::class,
                [
                    'label' => 'Illustration',
                    'required' => false
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
