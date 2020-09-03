<?php

namespace App\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

use App\Entity\Item;
use App\Entity\User;

class CMSVoter extends Voter
{
    const CREATE = 'create';
    const EDIT = 'edit';
    const DELETE = 'delete';

    protected function supports($attribute, $object)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::CREATE, self::EDIT, self::DELETE])) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $object, TokenInterface $token)
    {
        $user = $token->getUser();
        switch ($attribute) {
            case self::CREATE:
                return $this->canCreate($object, $user);
            case self::EDIT:
                return $this->canEdit($object, $user);
            case self::DELETE:
                return $this->canDelete($object, $user);
        }
    }

    private function canCreate(Item $object, User $user)
    {
        return $this->canEdit($object, $user);
    }

    private function canEdit(Item $object, User $user)
    {
        return false;
    }

    private function canDelete(Item $object, User $user)
    {
        return false;
    }
}