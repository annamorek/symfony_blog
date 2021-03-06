<?php
/**
 * Post entity.
 *
 * @copyright (c) 2016 Anna Morek
 * @link http://epi.chojna.info.pl
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use \DateTime;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

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
     * Id
     *
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
     * Topic
     *
     * @ORM\Column(
     *     name="topic",
     *     type="string",
     *     length=128,
     *     nullable=false
     * )
     * @Assert\NotBlank(groups={"post-default"})
     * @Assert\Length(min=3, max=128, groups={"post-default"})
     */
    private $topic;

    /**
     * Content.
     *
     * @ORM\Column(
     *     name="content",
     *     type="text",
     *     nullable=false
     * )
     * @Assert\NotBlank(groups={"post-default"})
     * @Assert\Length(min=3, max=1000, groups={"post-default"})
     */
    private $content;

    /**
     * Enabled
     *
     * @ORM\Column(
     *     name="enabled",
     *     type="boolean",
     *     nullable=false
     * )
     */
    private $enabled;

    /**
     * Created at
     * @ORM\Column(
     *     name="created_at",
     *     type="datetime",
     *     nullable=false
     * )
     */
    protected $created_at;

    /**
     * Tags array
     *
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="posts")
     * @ORM\JoinTable(
     *      name="posts_tags",
     *      joinColumns={@ORM\JoinColumn(name="post_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id")}
     * )
     *
     * @var \Doctrine\Common\Collections\ArrayCollection $tags
     */
    protected $tags;

    /**
     * Comments
     *
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="post", cascade={"remove"})
     */
    private $comments;

    /**
     * Post constructor.
     */
    public function __construct()
    {
        $this->created_at = new DateTime('now');
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
        $this->comments = new \Doctrine\Common\Collections\ArrayCollection();
    }

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

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Post
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Add tags
     *
     * @param \AppBundle\Entity\Tag $tags
     * @return Post
     */
    public function addTag(\AppBundle\Entity\Tag $tags)
    {
        $this->tags[] = $tags;

        return $this;
    }

    /**
     * Remove tags
     *
     * @param \AppBundle\Entity\Tag $tags
     */
    public function removeTag(\AppBundle\Entity\Tag $tags)
    {
        $this->tags->removeElement($tags);
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Add comments
     *
     * @param \AppBundle\Entity\Comment $comments
     * @return Post
     */
    public function addComment(\AppBundle\Entity\Comment $comments)
    {
        $this->comments[] = $comments;

        return $this;
    }

    /**
     * Remove comments
     *
     * @param \AppBundle\Entity\Comment $comments
     */
    public function removeComment(\AppBundle\Entity\Comment $comments)
    {
        $this->comments->removeElement($comments);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }
}
