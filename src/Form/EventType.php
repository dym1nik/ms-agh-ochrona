<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class EventType extends AbstractType
{
    public function __construct(
        private readonly AuthorizationCheckerInterface $auth
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('occurredAt', DateTimeType::class, [
            'widget' => 'single_text',
            'label' => 'Data i czas zdarzenia',
            'constraints' => [
                new Assert\NotBlank(message: 'Podaj datę i czas zdarzenia.'),
            ],
        ]);

        $builder->add('description', null, [
            'label' => 'Opis zdarzenia',
            'constraints' => [
                new Assert\NotBlank(message: 'Opis jest wymagany.'),
                new Assert\Length(min: 5, max: 500),
            ],
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
            'constraints' => [
                new Assert\NotBlank(message: 'Wybierz typ zdarzenia.'),
            ],
        ]);

        $builder->add('place', null, [
            'label' => 'Miejsce',
            'constraints' => [
                new Assert\NotBlank(message: 'Miejsce jest wymagane.'),
                new Assert\Length(min: 5, max: 100),
            ],
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
                'attr' => ['class' => 'form-select'],
                'row_attr' => ['class' => 'mb-3'],
                'label_attr' => ['class' => 'form-label fw-semibold'],
            ]);
        }

        // 12h wstecz -> teraz, ale TYLKO dla nie-admina
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event): void {
            if ($this->auth->isGranted('ROLE_ADMIN')) {
                return; // admin może wszystko
            }

            $form = $event->getForm();
            $data = $event->getData();

            if (!$data instanceof Event) {
                return;
            }

            $occurredAt = $data->getOccurredAt();
            if ($occurredAt === null) {
                return;
            }

            $now = new \DateTimeImmutable();
            $min = $now->sub(new \DateInterval('PT12H'));

            if ($occurredAt < $min || $occurredAt > $now) {
                $form->get('occurredAt')->addError(
                    new FormError('Data i czas zdarzenia muszą być z ostatnich 12 godzin i nie mogą być z przyszłości.')
                );
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}