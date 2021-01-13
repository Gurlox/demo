<?php

namespace App\Form;

use App\DTO\ItemDataDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\DataMapper\PropertyPathMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ItemDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        foreach ($options['injected_data'] as $key => $datum) {
            $form = $builder->getForm();
            $this->addField($form, $key, $datum);
        }

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $node = $event->getData();
            $form = $event->getForm();

            foreach ($node as $key => $value) {
                $this->addField($form, $key, $value);
            }
        });
    }

    private function addField(FormInterface &$form, string $key, $value): void
    {
        if (is_array($value)) {
            $form->add($key, ItemDataType::class, [
                'injected_data' => $value,
                'data_class' => null,
            ]);
        } else {
            $form->add($key, TextType::class);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ItemDataDTO::class,
            'csrf_protection' => false,
            'injected_data' => [],
        ]);
    }
}
