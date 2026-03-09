<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\Event;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class EventExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('event_status_badge_class', [$this, 'getStatusBadgeClass']),
            new TwigFunction('event_status_label', [$this, 'getStatusLabel']),
        ];
    }

    public function getStatusBadgeClass(Event $event): string
    {
        return match ($event->getStatus()) {
            'new' => 'bg-primary',
            'in_progress' => 'bg-warning text-dark',
            'closed' => 'bg-success',
            default => 'bg-secondary',
        };
    }

    public function getStatusLabel(Event $event): string
    {
        return $event->getStatusLabel();
    }
}