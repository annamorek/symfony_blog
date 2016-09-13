<?php
/**
 * Post repository.
 *
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

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

    /**
     * Find all posts.
     * @return array
     */
    public function findAll()
    {
        return $this->findBy(array(), array('created_at' => 'DESC'));
    }

    /**
     * Get all posts
     *
     * @param $currentPage
     * @return Paginator
     */
    public function getAllPosts($currentPage)
    {
        // Create our query
        $query = $this->createQueryBuilder('p')
            ->orderBy('p.created_at', 'DESC')
            ->getQuery();

        // No need to manually get get the result ($query->getResult())

        $paginator = $this->paginate($query, $currentPage);

        return $paginator;
    }

    /**
     * Paginate
     *
     * @param $dql
     * @param int $page
     * @param int $limit
     * @return Paginator
     */
    public function paginate($dql, $page = 1, $limit = 3)
    {
        $paginator = new Paginator($dql);

        $paginator->getQuery()
            ->setFirstResult($limit * ($page - 1)) // Offset
            ->setMaxResults($limit); // Limit

        return $paginator;
    }
}
