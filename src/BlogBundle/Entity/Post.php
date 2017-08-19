<?php

namespace BlogBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;

/**
 * @ORM\Entity(repositoryClass="BlogBundle\Repository\PostRepository")
 * @ORM\Table(name="post")
 * @ORM\HasLifecycleCallbacks()
 */
class Post
{
    const STATUS_DRAFT = 1;
    const STATUS_PUBLISHED = 2;
    const STATUS_ARCHIVED=3;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @ORM\Column(type="text")
     */
    protected $content;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $tags;

    /**
     * @ORM\Column(type="integer")
     */
    protected $status;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $create_time;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $update_time;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="posts")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     */
    protected $author;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="post")
     * @ORM\OrderBy({"create_time" = "DESC"})
     */
    protected $comments;

    protected $oldTags = array();

    /**
     * @return mixed
     */
    public function getComments($needApprove=false)
    {
        if ($needApprove) {
            $criteria = Criteria::create()->where(Criteria::expr()->eq('status', Comment::STATUS_APPROVED));
            return $this->comments->matching($criteria);
        }

        return $this->comments;
    }

    public function getCommentsCount($needApprove=false)
    {
        return $this->comments->count();
    }

    /**
     * @param mixed $comments
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
    }


     public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getcreate_time()
    {
        return $this->create_time;
    }

    /**
     * @return mixed
     */
    public function getupdate_time()
    {
        return $this->update_time;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        $this->oldTags = '100200';
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param mixed $tags
     */
    public function setTags($tags)
    {
        //по умолчанию в Doctrine пустое поле содержит строку - числовой идентификатор
        if (is_string($this->oldTags) && is_numeric($this->oldTags)) {
            $this->setOldTags($this->tags);
        }

        $this->tags = $tags;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     */
    public function setAuthor(User $author)
    {
        $this->author = $author;
    }

    /**
     * @ORM\PrePersist()
     */
    public function beforeCreate()
    {
        $this->create_time = new \DateTime;
        $this->update_time = new \DateTime;
    }

    /**
     * @ORM\PreUpdate()
     */
    public function beforeUpdate()
    {
       $this->update_time = new \DateTime;
    }

    public function addComment(Comment $comment)
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setPost($this);
        }
    }

    public function removeComment(Comment $comment)
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            $comment->setAuthor(NULL);
        }
    }


    public static function getStatusOptions()
    {
        return [
            'Draft' => self::STATUS_DRAFT,
            'Published' => self::STATUS_PUBLISHED,
            'Archived' => self::STATUS_ARCHIVED
        ];
    }

    public function getStatusText()
    {
        $statuses =  self::getStatusOptions();
        foreach ($statuses as $key => $status) {
            if ($this->status == $status) {
                return $key;
            }
        }

        return '';
    }

    public function getOldTags()
    {
        return $this->oldTags;
    }

    public function setOldTags($tags) {
        $this->oldTags = $tags;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('title', new NotBlank(['message' => '{{ value }} cannot be empty']));
        $metadata->addPropertyConstraint('title', new Length(['max' => 128, 'maxMessage' => 'Title cannot be greater than {{ limit }} characters']));
        $metadata->addPropertyConstraint('content', new NotBlank());
        $metadata->addPropertyConstraint('status', new NotBlank());
        $metadata->addPropertyConstraint('status', new Type('integer'));
        $metadata->addPropertyConstraint('status', new Range([
            'min' => 1, 'max' => 3,
            'minMessage' => "Value must be not less than {{ limit }}",
            'maxMessage' => "Value must be not greater than {{ limit }}"
        ]));
        $metadata->addPropertyConstraint('tags', new  Regex(['pattern' => '#^[\w,\s]+$#', 'message'=>'В тегах можно использовать только буквы.']));
    }
}