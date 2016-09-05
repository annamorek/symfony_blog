<?php
/**
 * Tag repository.
 *
 * @copyright (c) 2016 Anna Morek
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class Tag.
 * @package AppBundle\Repository
 * @author Anna Morek
 */
class Tag extends EntityRepository
{
    /**
     * Save tags object.
     * @param \AppBundle\Entity\Tag $tag
     */
    public function save(\Appbundle\Entity\Tag $tag)
    {
        $this->_em->persist($tag);
        $this->_em->flush();
    }

    /**
     * Delete answer object.
     *
     * @param Tag $tag Tag object
     *
     * @return mixed
     */
    public function delete(\AppBundle\Entity\Tag $tag)
    {
        if (!$tag) {
            throw $this->createNotFoundException('No tag found');
        }
        $this->_em->remove($tag);
        $this->_em->flush();
    }
}
