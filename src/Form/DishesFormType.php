<?php

namespace App\Form;

use App\Entity\Dishes;
use App\Entity\Categories;
use App\Repository\CategoriesRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class DishesFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', options: [
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'Nom',
                'class' => 'form-control'
                ]
            ])
            ->add('description', options: [
                'label' => 'Description',
                'attr' => [
                    'placeholder' => 'Description',
                'class' => 'form-control'
                ]
            ])
            ->add('price', IntegerType::class, options: [
                'label' => 'Prix en €',
                'attr' => [
                    'placeholder' => 'Prix',
                'class' => 'form-control'
                ],
                'constraints' => [
                    new Positive(
                        message: 'Le prix ne peut pas être négatif'
                    )
                ]
            ])
            ->add('categories', EntityType::class, [
                'class' => Categories::class,
                'choice_label' => 'name',
                'label' => 'Catégorie',
                'group_by' => 'parent.name',
                'attr' => [
                    'placeholder' => 'Nom du plat',
                'class' => 'form-control'
                ],
                'query_builder' => function (CategoriesRepository $cr) {
                    return $cr->createQueryBuilder('c')
                        ->where('c.parent IS NOT NULL')
                        ->orderBy('c.name', 'ASC');
                }
            ])
            ->add('images', FileType::class, [
                'label' => false,
                'multiple' => true,
                'mapped' => false,
                'required' => false,
                'attr' => [
                'class' => 'form-control'
                ],
                'constraints' => [
                    new All(
                        new Image([
                            'maxWidth' => 3000,
                            'maxWidthMessage' => 'L\'image doit faire {{ max_width }} pixels de large au maximum'
                ])
                )
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Dishes::class,
        ]);
    }
}
