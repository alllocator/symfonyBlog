<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="tags")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TagsRepository")
 */


class Tags extends EntityRepository
{
    /**
     * @ORM\ManyToMany(targetEntity="BlogPost", mappedBy="tags")
     */
    private $blogposts;

    public function __construct() {
        $this->blogposts = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function addBlogPost(BlogPost $post)
    {
        $this->blogposts[] = $post;
    }

    /**
     * @return mixed
     */
    public function getBlogposts()
    {
        return $this->blogposts;
    }

    /**
     * @param mixed $blogposts
     */
    public function setBlogposts($blogposts)
    {
        $this->blogposts = $blogposts;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param string $tag
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
    }

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="tag", type="string")
     */
    private $tag;



}