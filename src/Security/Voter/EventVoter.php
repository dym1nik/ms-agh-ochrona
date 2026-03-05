<?php

namespace App\Security\Voter;

use App\Entity\Event;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class EventVoter extends Voter
{
    public const EDIT = 'EVENT_EDIT';
    public const DELETE = 'EVENT_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return \in_array($attribute, [self::EDIT, self::DELETE], true)
            && $subject instanceof Event;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var Event $event */
        $event = $subject;

        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ADMIN może zawsze
        if (\in_array('ROLE_ADMIN', $token->getRoleNames(), true)) {
            return true;
        }

        // USER tylko swoje
        $identifier = method_exists($user, 'getUserIdentifier') ? $user->getUserIdentifier() : (string) $user;
        if ($event->getAuthor() !== $identifier) {
            return false;
        }

        // USER tylko do 24h od utworzenia
        $limit = (new \DateTimeImmutable())->sub(new \DateInterval('PT24H'));

        return $event->getCreatedAt() >= $limit;
    }
}