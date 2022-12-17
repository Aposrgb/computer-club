<?php

namespace App\Form;

use App\Entity\Schedule;
use App\Helper\EnumStatus\ScheduleStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScheduleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateStart')
            ->add('createdAt')
            ->add('status', ChoiceType::class, [
                'choices' => ScheduleStatus::getStatus()
            ])
            ->add('dateEnd')
            ->add('price')
            ->add('user', null, ['mapped' => false])
            ->add('pc', null, ['mapped' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Schedule::class,
        ]);
    }
}
