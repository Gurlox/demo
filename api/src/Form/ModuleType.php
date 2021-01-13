<?php

namespace App\Form;

use App\DTO\ModuleDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModuleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', TextType::class)
            ->add('name', TextType::class)
            ->add('showInMenu', CheckboxType::class)
            ->add('labelInMenu', TextType::class)
            ->add('slug', TextType::class)
            ->add('items', CollectionType::class, [
                'entry_type' => ItemType::class,
                'allow_add' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ModuleDTO::class,
            'csrf_protection' => false,
            'allow_extra_fields' => true,
        ]);
    }
}
