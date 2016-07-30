<?php
/**
 * User repository.
 *
 * @copyright (c) 2016 Anna Morek
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class User.
 * @package AppBundle\Repository
 * @author Anna Morek
 */
class User extends EntityRepository
{
    /**
     * Save users object.
     * @param \AppBundle\Entity\User $users
     * @param
     */
    public function save(\Appbundle\Entity\User $user)
    {
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * Delete answer object.
     *
     * @param User $user User object
     *
     * @return mixed
     */
    public function delete(\AppBundle\Entity\User $user)
    {
        if (!$user) {
            throw $this->createNotFoundException('No user found');
        }
        $this->_em->remove($user);
        $this->_em->flush();
    }
}