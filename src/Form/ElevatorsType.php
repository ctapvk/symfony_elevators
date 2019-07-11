<?php

namespace App\Form;

use App\Entity\Elevators;
use App\Entity\Houses;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ElevatorsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('status')
            ->add('position')
//            ->add('house')
//            ->add('house', EntityType::class, [
//                'class' => 'App\Entity\Houses',
//            ])
            ->add('house', EntityType::class, array(
                'class' => 'App\Entity\Houses',
                'query_builder' => function (\Doctrine\ORM\EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.id', 'ASC')
                        ;
                },
                'error_bubbling' => true,
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Elevators::class,
        ]);
    }
}
