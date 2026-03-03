<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class EventType extends AbstractType
{
    private AuthorizationCheckerInterface $auth;

    public function __construct(AuthorizationCheckerInterface $auth)
    {
        $this->auth = $auth;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('occurredAt', DateTimeType::class, [
            'widget' => 'single_text',
            'label' => 'Data i czas zdarzenia',
        ]);

        $builder->add('description', null, [
            'label' => 'Opis zdarzenia',
        ]);

        $builder->add('type', ChoiceType::class, [
            'label' => 'Typ zdarzenia',
            'choices' => [
                'Interwencja' => 'interwencja',
                'Zgłoszenie mieszkańca' => 'zgloszenie_mieszkanca',
                'Incydent agresji' => 'incydent_agresji',
                'Zdarzenie drogowe' => 'zdarzenie_drogowe',
                'Inne' => 'inne',
            ],
        ]);

        $builder->add('place', null, [
            'label' => 'Miejsce',
        ]);

        if ($this->auth->isGranted('ROLE_ADMIN')) {
    $builder->add('status', ChoiceType::class, [
        'label' => 'Status',
        'placeholder' => 'Wybierz status',
        'choices' => [
            'Nowe' => 'new',
            'W trakcie' => 'in_progress',
            'Zamknięte' => 'closed',
        ],
        'attr' => [
            'class' => 'form-select',
        ],
        'row_attr' => [
            'class' => 'mb-3',
        ],
        'label_attr' => [
            'class' => 'form-label fw-semibold',
        ],
    ]);
}
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}