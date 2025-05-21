<?php

namespace App\Form;

use Dom\Text;
use BcMath\Number;
use App\Entity\Session;
use App\Entity\Student;
use App\Entity\Trainer;
use App\Entity\Formation;
use Doctrine\DBAL\Types\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class SessionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la session',
                'attr' => [
                    'placeholder' => 'Nom de la session',
                ],
            ])
            ->add('nbPlace', NumberType::class, [
                'label' => 'Nombre de places',
                'attr' => [
                    'placeholder' => 'Nombre de places',
                ],
            ])
            ->add('beggin', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
                'attr' => [
                    'placeholder' => 'Date de début',
                ],
            ])
            ->add('end', DateType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'attr' => [
                    'placeholder' => 'Date de fin',
                ],
            ])
            ->add('students', EntityType::class, [
                'class' => Student::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('trainer', EntityType::class, [
                'class' => Trainer::class,
                'choice_label' => 'id',
            ])
            ->add('formation', EntityType::class, [
                'class' => Formation::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Session::class,
        ]);
    }
}
