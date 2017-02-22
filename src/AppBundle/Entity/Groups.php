<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Groups
 *
 * @ORM\Table(name="groups")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GroupsRepository")
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
     * @ORM\ManyToOne(targetEntity="AgeGroups", inversedBy="fightGroup")
     * @ORM\JoinColumn(name="agegroup", referencedColumnName="id")
     */
     private $ageGroup;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted", type="datetime", options={"default": null}, nullable=true)
     */
    private $deleted;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->fighters = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function __toString() {
        return (string) $this->id;
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
     * Add fighter
     *
     * @param \AppBundle\Entity\Fighter $fighter
     *
     * @return Groups
     */
    public function addFighter(\AppBundle\Entity\Fighter $fighter)
    {
        $this->fighters[] = $fighter;

        return $this;
    }

    /**
     * Remove fighter
     *
     * @param \AppBundle\Entity\Fighter $fighter
     */
    public function removeFighter(\AppBundle\Entity\Fighter $fighter)
    {
        $this->fighters->removeElement($fighter);
    }

    /**
     * Set ageGroup
     *
     * @param \AppBundle\Entity\AgeGroups $ageGroup
     *
     * @return Groups
     */
    public function setAgeGroup(\AppBundle\Entity\AgeGroups $ageGroup = null)
    {
        $this->ageGroup = $ageGroup;

        return $this;
    }

    /**
     * Get ageGroup
     *
     * @return \AppBundle\Entity\AgeGroups
     */
    public function getAgeGroup()
    {
        return $this->ageGroup;
    }
}
