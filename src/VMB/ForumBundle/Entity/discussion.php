<?php

namespace VMB\ForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * discussion
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="VMB\ForumBundle\Entity\discussionRepository")
 */
class discussion
{
     /**
   * @ORM\ManyToOne(targetEntity="VMB\UserBundle\Entity\User")
   * @ORM\JoinColumn(nullable=false)þ
   */
  private $user;
    
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
       
    /**
     * Constructor
     */
     public function __construct()
    {   
        $this->date = new \Datetime();
        $this->setState(false);
        $this->comments = new ArrayCollection();
    }

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;
    
    /**
    * @ORM\ManyToOne(targetEntity="VMB\PresentationBundle\Entity\Topic",inversedBy="discussions")
    * @ORM\JoinColumn(nullable=false)
    */
    private $topic;
    
    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var boolean
     *
     * @ORM\Column(name="state", type="boolean")
     */
    private $state;
    
    /**
     * @ORM\OneToMany(targetEntity="VMB\ForumBundle\Entity\Comment", mappedBy="discussion")
     */
    private $comments;
    
    
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
     * Set date
     *
     * @param \DateTime $date
     * @return discussion
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

     /**
     * Set topic
     *
     * @param \VMB\PresentationBundle\Entity\Topic $topic
     * @return discussion
     */
    public function setTopic($topic)
    {
        $this->topic = $topic;

        return $this;
    }

    /**
     * Get topic
     *
     * @return \VMB\PresentationBundle\Entity\Topic 
     */
    public function getTopic()
    {
        return $this->topic;
    }
    
    /**
     * Set title
     *
     * @param string $title
     * @return discussion
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return discussion
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set state
     *
     * @param boolean $state
     * @return discussion
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return boolean 
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set user
     *
     * @param \VMB\UserBundle\Entity\User $user
     * @return discussion
     */
    public function setUser(\VMB\UserBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \VMB\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
    
    public function addComment(Comment $comment)
  {
    $this->comments[] = $comment;
    $comment->setDiscussion($this);

    return $this;
  }

  public function removeComment(Comment $comment)
  {
    $this->comments->removeElement($comment);
  }

  public function getComments()
  {
    return $this->comments;
  }
}
