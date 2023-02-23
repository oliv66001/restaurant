<?php

namespace App\Security\Voter;

use App\Entity\Dishes;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\dishes\DishesInterface;


/**
 * Summary of DishesVoter
 */
class DishesVoter extends Voter

{
    const EDIT = 'DISHES_EDIT';
    const DELETE = 'DISHES_DELETE';

    private $security;

    /**
     * Summary of __construct
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $dishes): bool
    {
        if(!in_array($attribute, [self::EDIT, self::DELETE])){
            return false;
        }
        if(!$dishes instanceof Dishes){
            return false;
        }
        return true;

        // return in_array($attribute, [self::EDIT, self::DELETE]) && $dishes instanceof Dishes;
    }

    protected function voteOnAttribute($attribute, $dishes, TokenInterface $token, Security $security ): bool
    {
        // On récupère l'utilisateur à partir du token
        $dishes = $token->getDishes();

        if(!$dishes instanceof DishesInterface) return false;

        // On vérifie si l'utilisateur est admin
        if($this->$security->isGranted('ROLE_ADMIN')) return true;

        // On vérifie les permissions
        switch($attribute){
            case self::EDIT:
                // On vérifie si l'utilisateur peut éditer
                return $this->canEdit();
                break;
            case self::DELETE:
                // On vérifie si l'utilisateur peut supprimer
                return $this->canDelete();
                break;
        }
    }

    private function canEdit(){
        return $this->security->isGranted('ROLE_DISHES_ADMIN');
    }
    private function canDelete(){
        return $this->security->isGranted('ROLE_ADMIN');
    }
}

