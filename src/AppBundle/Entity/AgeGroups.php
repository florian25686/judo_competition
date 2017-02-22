<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AgeGroups
 * @ORM\Entity
 * @ORM\Table(name="agegroups")
 */
class AgeGroups
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
     * @var string
     * 
     * @ORM\Column(name="name", type="string", length=10)
     */
    private $name;

    /**
     * @var array
     * @ORM\OneToMany(targetEntity="Fighter", mappedBy="ageGroup")
     */
    private $fighters;
    
    /**
     * @var array
     * @ORM\OneToMany(targetEntity="Groups", mappedBy="ageGroup")
     */
     private $fightGroup;

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
        $this->fightGroup = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString() {
        return $this->getName();
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
     * Set name
     *
     * @param string $name
     *
     * @return AgeGroups
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set deleted
     *
     * @param \DateTime $deleted
     *
     * @return AgeGroups
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
     * @return AgeGroups
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
     * Get fighters
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFighters()
    {
        return $this->fighters;
    }

    /**
     * Add fightGroup
     *
     * @param \AppBundle\Entity\Groups $fightGroup
     *
     * @return AgeGroups
     */
    public function addFightGroup(\AppBundle\Entity\Groups $fightGroup)
    {
        $this->fightGroup[] = $fightGroup;

        return $this;
    }

    /**
     * Remove fightGroup
     *
     * @param \AppBundle\Entity\Groups $fightGroup
     */
    public function removeFightGroup(\AppBundle\Entity\Groups $fightGroup)
    {
        $this->fightGroup->removeElement($fightGroup);
    }

    /**
     * Get fightGroup
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFightGroup()
    {
        return $this->fightGroup;
    }
}
