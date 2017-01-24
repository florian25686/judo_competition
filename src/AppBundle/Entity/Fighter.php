<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fighter")
 */
class Fighter
{

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="lastName", type="string", length=100)
     */
     private $lastName;

    /**
     * @ORM\Column(name="firstName", type="string", length=100)
     */
     private $firstName;

    /**
     * @ORM\Column(name="weight", type="decimal", scale=2)
     */
     private $weight;

    /**
     * @ORM\Column(name="club", type="string", length=100)
     */
     private $club;

    /**
     * @ORM\Column(name="ageGroup", type="string", length=50, options={"default": 0})
     */
     private $ageGroup;
     
     /**
     * @ORM\Column(name="birthDate", type="date")
     */
     private $birthDate;

    /**
     * @ORM\Column(name="gender", type="string", length=1)
     */
     private $gender;
     
     /**
      * @ORM\Column(name="groupId", type="integer")
      */
     private $groupId;
     
     /**
      * @ORM\ManyToOne(targetEntity="Groups", inversedBy="fighters")
      * @ORM\JoinColumn(name="group_id", referencedColumnName="id")
      */
     private $groups;
     
     /**
      * Used to tell if the group in which the fighter is in has been printed equals he is in fight
      * @ORM\Column(name="inFight", type="boolean", options={"default": false})
      */
     private $inFight;
     
     /**
      * @ORM\Column(name="deleted", type="boolean", options={"default": false})
      */
     private $deleted;

    public function __construct()
    {
        $this->gender = 'm';
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
     * Set lastName
     *
     * @param string $lastName
     *
     * @return Fighter
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return Fighter
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set weight
     *
     * @param string $weight
     *
     * @return Fighter
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight
     *
     * @return string
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set club
     *
     * @param string $club
     *
     * @return Fighter
     */
    public function setClub($club)
    {
        $this->club = $club;

        return $this;
    }

    /**
     * Get club
     *
     * @return string
     */
    public function getClub()
    {
        return $this->club;
    }

    /**
     * Set ageGroup
     *
     * @param string $ageGroup
     *
     * @return Fighter
     */
    public function setAgeGroup($ageGroup)
    {
        $this->ageGroup = $ageGroup;

        return $this;
    }

    /**
     * Get ageGroup
     *
     * @return string
     */
    public function getAgeGroup()
    {
        return $this->ageGroup;
    }

    /**
     * Set gender
     *
     * @param string $gender
     *
     * @return Fighter
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set groupId
     *
     * @param integer $groupId
     *
     * @return Fighter
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;

        return $this;
    }

    /**
     * Get groupId
     *
     * @return integer
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * Set inFight
     *
     * @param boolean $inFight
     *
     * @return Fighter
     */
    public function setInFight($inFight)
    {
        $this->inFight = $inFight;

        return $this;
    }

    /**
     * Get inFight
     *
     * @return boolean
     */
    public function getInFight()
    {
        return $this->inFight;
    }

    /**
     * Set groups
     *
     * @param \AppBundle\Entity\Groups $groups
     *
     * @return Fighter
     */
    public function setGroups(\AppBundle\Entity\Groups $groups = null)
    {
        $this->groups = $groups;

        return $this;
    }

    /**
     * Get groups
     *
     * @return \AppBundle\Entity\Groups
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Set birthDate
     *
     * @param \DateTime $birthDate
     *
     * @return Fighter
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * Get birthDate
     *
     * @return \DateTime
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * Set deleted
     *
     * @param \tinyint $deleted
     *
     * @return Fighter
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
        
        return $this;
    }

    /**
     * Get deleted
     *
     * @return \tinyint
     */
    public function getDeleted()
    {
        return $this->deleted;
    }
}
