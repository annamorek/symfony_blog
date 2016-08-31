<?php
/**
 * Post repository.
 *
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class Post.
 * @package AppBundle\Repository
 * @author Anna Morek
 */
class Post extends EntityRepository
{
    /**
     * Save post object.
     * @param \AppBundle\Entity\Post $post
     * @param
     */
    public function save(\Appbundle\Entity\Post $post)
    {
        $this->_em->persist($post);
        $this->_em->flush();
    }

    /**
     * Delete answer object.
     *
     * @param Post $post Post object
     *
     * @return mixed
     */
    public function delete(\AppBundle\Entity\Post $post)
    {
        if (!$post) {
            throw $this->createNotFoundException('No post found');
        }
        $this->_em->remove($post);
        $this->_em->flush();
    }

    public function findAll()
    {
        return $this->findBy(array(), array('created_at' => 'DESC'));
    }

}