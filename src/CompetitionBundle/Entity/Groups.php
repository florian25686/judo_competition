<?php

namespace CompetitionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Groups
 *
 * @ORM\Table(name="groups")
 * @ORM\Entity(repositoryClass="CompetitionBundle\Repository\GroupsRepository")
 */
class Groups
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var array
     * @ORM\OneToMany(targetEntity="Fighter", mappedBy="groups")
     */
    private $fighters;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="smallint")
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted", type="datetime", options={"default": null}, nullable=true)
     */
    private $deleted;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set fighters
     *
     * @param array $fighters
     *
     * @return Groups
     */
    public function setFighters($fighters)
    {
        $this->fighters = $fighters;

        return $this;
    }

    /**
     * Get fighters
     *
     * @return array
     */
    public function getFighters()
    {
        return $this->fighters;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Groups
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set deleted
     *
     * @param \DateTime $deleted
     *
     * @return Groups
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return \DateTime
     */
    public function getDeleted()
    {
        return $this->deleted;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->fighters = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add fighter
     *
     * @param \CompetitionBundle\Entity\Fighter $fighter
     *
     * @return Groups
     */
    public function addFighter(\CompetitionBundle\Entity\Fighter $fighter)
    {
        $this->fighters[] = $fighter;

        return $this;
    }

    /**
     * Remove fighter
     *
     * @param \CompetitionBundle\Entity\Fighter $fighter
     */
    public function removeFighter(\CompetitionBundle\Entity\Fighter $fighter)
    {
        $this->fighters->removeElement($fighter);
    }
}
