<?php

namespace App\Form;

use App\DBAL\Types\CauseDefaillanceType;
use App\DBAL\Types\DepartementType;
use App\DBAL\Types\Priorite;
use App\Entity\DemandeIntervention;
use App\Entity\Pole;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class DemandeInterventionType extends AbstractType
{

    //permet de gérer les options des formulaires
    public function getConfiguration($label, $placeholder, $options = []): array
    {
        return array_merge([
            'label' => $label,
            'attr' => [
                'placeholder' => $placeholder,
            ]
        ], $options);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nomDemandeur', TextType::class, $this->getConfiguration('', 'John Doe'))
            ->add('emailDemandeur', TextType::class, $this->getConfiguration('', 'toto@example.com'))
            ->add('contactDemandeur', TextType::class, $this->getConfiguration('', '(00221) 77 XXX XX XX'))
            ->add('fonction', TextType::class, $this->getConfiguration('', 'Enseignant'))
            ->add('description', TextareaType::class, $this->getConfiguration('', 'Saisissez, si possible, une brève description de votre problème'))
            ->add('priorite', ChoiceType::class, $this->getConfiguration('', '', [
                'choices' => Priorite::getChoices(),
                'attr'=>[
                    'class'=>'form-select'
                ]
            ]))
            ->add('departement', ChoiceType::class, $this->getConfiguration('department', '', [
                'choices' => DepartementType::getChoices(),
                'attr'=>[
                    'class'=>'form-select'
                ]
            ]))
            ->add('causeDefaillance', ChoiceType::class, $this->getConfiguration('Cause défaillance', '', [
                'choices' => CauseDefaillanceType::getChoices(),
                'attr'=>[
                    'class'=>'form-select'
                ],
                'help'=>'Si connue, indiquer la cause la défaillance'
            ]))
            ->add('poleConcerne', EntityType::class, $this->getConfiguration('Type de défaillance', '', [
                'class' => Pole::class,
                'choice_label' => 'nomPole',
                'multiple' => false,
                'attr'=>[
                    'class'=>'form-select'
                ]
            ]));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DemandeIntervention::class, 
            'attr' => [
                'novalidate' => 'novalidate'
            ]
        ]);
    }
}
