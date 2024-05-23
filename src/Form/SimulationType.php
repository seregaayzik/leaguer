<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Repository\TeamRepository;

class SimulationType extends AbstractType
{
    private TeamRepository $_teamRepository;
    
    public function __construct(
        TeamRepository $_teamRepository
    ) {
        $this->_teamRepository = $_teamRepository;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $teamsArray = [];
        foreach ($this->_teamRepository->findAll() as $item) {
            $teamsArray[$item->getName()] = $item->getId();
        }
        $builder
            ->add('teams', ChoiceType::class, [
                'choices'  => $teamsArray,
                'multiple' => true,
                'expanded' => false,
                'label' => 'Select Team',
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\NotBlank(),
                    new \Symfony\Component\Validator\Constraints\Count(['min' => 4]),
                    new \App\Validator\TeamQuantity()
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
