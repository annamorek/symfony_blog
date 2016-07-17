<?php
/**
 * Post entity.
 *
 * @copyright (c) 2016 Tomasz Chojna
 * @link http://epi.chojna.info.pl
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Post.
 *
 * @package Model
 * @author Tomasz Chojna
 *
 * @ORM\Table(name="posts")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Post")
 */
class Post
{
    /**
     * @ORM\Id
     * @ORM\Column(
     *     type="integer",
     *     nullable=false,
     *     options={
     *         "unsigned" = true
     *     }
     * )
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\Column(
     *     name="topic",
     *     type="string",
     *     length=128,
     *     nullable=false
     * )
     */
    private $topic;

    /**
     * @ORM\Column(
     *     name="content",
     *     type="text",
     *     nullable=false
     * )
     */
    private $content;

    /**
     * @ORM\Column(
     *     name="enabled",
     *     type="boolean",
     *     nullable=false
     * )
     */
    private $enabled;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set topic
     *
     * @param string $topic
     * @return Post
     */
    public function setTopic($topic)
    {
        $this->topic = $topic;

        return $this;
    }

    /**
     * Get topic
     *
     * @return string 
     */
    public function getTopic()
    {
        return $this->topic;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Post
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Post
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean 
     */
    public function getEnabled()
    {
        return $this->enabled;
    }
}
