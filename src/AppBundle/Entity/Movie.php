<?php

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\DateTimeType;
/**
 * @ORM\Entity
 * @ORM\Table(name="movie")
 */
class Movie {
    /**
     * @ORM\Id()
     * @ORM\Column(name="movie_id", type = "integer", nullable=false)
     * @ORM\GeneratedValue(strategy = "IDENTITY")
     * @var integer
     */
    protected $movie_id;

    /**
     * @ORM\Column(name="user_id", type = "integer", nullable=false)
     * @var integer
     */
    protected $user_id;
    /**
     * @ORM\Column (type = "string", length = 255)
     * @var string
     */
    protected $title;

    /**
     * @var \DateTime
     * @ORM\Column(name="createdAt", type="datetime", nullable=false)
     */
    protected $createdAt;
    /**
     * @var \DateTime
     * @ORM\Column(name="modifiedAt", type="datetime", nullable=false)
     */
    protected $modifiedAt;

    public function __construct(
        $title,
        $userId
    ) {
        $date = new \DateTime();
        $this->title = $title;
        $this->user_id = $userId;
        $this->createdAt = $date;
        $this->modifiedAt = $date;
    }


    /**
     * @return int
     */
    public function getMovieId()
    {
        return $this->movie_id;
    }

    /**
     * @param int $movie_id
     */
    public function setMovieId($movie_id)
    {
        $this->movie_id = $movie_id;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param int $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }


    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * @param \DateTime $modifiedAt
     */
    public function setModifiedAt($modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;
    }

}
