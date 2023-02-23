<?php

namespace App\Security\Voter;

use App\Entity\Dishes;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;


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
        return in_array($attribute, [self::EDIT, self::DELETE]) && $dishes instanceof Dishes;
    }

     /**
      * Summary of voteOnAttribute
      * @param string $attribute
      * @param mixed $dishes
      * @param TokenInterface $token
      * @return bool
      */
    protected function voteOnAttribute(string $attribute, $dishes, TokenInterface $token ): bool
 {
     // On récupère l'utilisateur à partir du token
     $dishes = $token->getUser();


     if(!$dishes instanceof UserInterface) return false;

     // On vérifie si l'utilisateur est admin
     if($this->security->isGranted('ROLE_ADMIN')) return true;

     // On vérifie les permissions
     switch($attribute){
         case self::EDIT:
             // On vérifie si l'utilisateur peut éditer
             return $this->security->isGranted('ROLE_DISHES_ADMIN');
             break;
         case self::DELETE:
             // On vérifie si l'utilisateur peut supprimer
             return $this->security->isGranted('ROLE_ADMIN');
             break;
     }
 }
}