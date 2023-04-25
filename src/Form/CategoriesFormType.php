<?php

namespace App\Form;

use App\Entity\Categories;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use App\Repository\CategoriesRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CategoriesFormType extends AbstractType
{
    private $categoriesRepository;

    public function __construct(CategoriesRepository $categoriesRepository)
    {
        $this->categoriesRepository = $categoriesRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Nom',
            ])

            ->add('parent', EntityType::class, [
                'class' => Categories::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Parent',
            ])

            ->add('image', FileType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Image',
            ])
        

        ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $category = $event->getData();
            $parent = $category->getParent();

            if ($parent) {
                $maxOrder = $this->categoriesRepository->getMaxCategoryOrderForParent($parent);
                $category->setCategoryOrder($maxOrder + 1);
            } else {
                $category->setCategoryOrder(1);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Categories::class,
        ]);
    }
}
