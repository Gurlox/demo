<?php

namespace App\Form;

use App\DTO\ItemDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', NumberType::class, [
                'required' => false,
            ])
            ->add('data', TextType::class)
            ->add('name', TextType::class)
        ;

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $node = $event->getData();
            $form = $event->getForm();

            if (!$node) {
                return;
            }

            if (isset($node['data'])) {
                $form->add('data', ItemDataType::class);
            }

            if (isset($node['items']) && sizeof(@$node['items'])){
                $form->add('items', CollectionType::class, [
                    'required' => false,
                    'allow_add' => true,
                    'delete_empty' => true,
                    'entry_type' => ItemType::class,
                    'by_reference' => false,
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ItemDTO::class,
            'csrf_protection' => false,
            'allow_extra_fields' => true,
        ]);
    }
}
