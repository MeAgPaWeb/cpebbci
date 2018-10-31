<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Library
 *
 * @ORM\Table(name="library")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LibraryRepository")
 */
class Library
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
     * @Assert\NotBlank()
     * @ORM\Column(name="address", type="string")
     */
    private $address;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="name", type="string", unique=true, nullable=false)
     */
    private $name;

    /**
     * @var int
     * @Assert\NotBlank()
     * @ORM\Column(name="age", type="integer", nullable=false)
     * @Assert\Length(
     *      max = 4,
     *      min = 4,
     * )
     */
    private $age;

    /**
     * @ORM\OneToMany(targetEntity="Room", mappedBy="library")
     */
    private $rooms;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=false)
     */
    private $enabled;

    /**
     * @var \AppBundle\Entity\User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $owner;

    /**
     * @var string
     *
     * @ORM\Column(name="colour", type="string")
     */
    private $colour;

    /**
     * @var string
     *
     * @ORM\Column(name="position", type="string")
     */
    private $position;

    /**
     * One Library has Many Follow-up Solicitations.
     * @ORM\OneToMany(targetEntity="Solicitation", mappedBy="library")
     */
    private $followers;

    public function __construct()
    {
        $this->rooms = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->enabled=true;
        $this->position=true;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set address.
     *
     * @param string $address
     *
     * @return Library
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address.
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Library
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set age.
     *
     * @param int $age
     *
     * @return Library
     */
    public function setAge($age)
    {
        $this->age = $age;

        return $this;
    }

    /**
     * Get age.
     *
     * @return int
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * Add room.
     *
     * @param \AppBundle\Entity\Room $room
     *
     * @return Library
     */
    public function addRoom(\AppBundle\Entity\Room $room)
    {
        $this->rooms[] = $room;

        return $this;
    }

    /**
     * Remove room.
     *
     * @param \AppBundle\Entity\Room $room
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeRoom(\AppBundle\Entity\Room $room)
    {
        return $this->rooms->removeElement($room);
    }

    /**
     * Get rooms.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRooms()
    {
        return $this->rooms;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Set enabled.
     *
     * @param bool $enabled
     *
     * @return Library
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled.
     *
     * @return bool
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set owner.
     *
     * @param \AppBundle\Entity\User|null $owner
     *
     * @return Library
     */
    public function setOwner(\AppBundle\Entity\User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner.
     *
     * @return \AppBundle\Entity\User|null
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set colour.
     *
     * @param string $colour
     *
     * @return Library
     */
    public function setColour($colour)
    {
        $this->colour = $colour;

        return $this;
    }

    /**
     * Get colour.
     *
     * @return string
     */
    public function getColour()
    {
        return $this->colour;
    }

    /**
     * Set position.
     *
     * @param string $position
     *
     * @return Library
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position.
     *
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Add follower.
     *
     * @param \AppBundle\Entity\Solicitation $follower
     *
     * @return Library
     */
    public function addFollower(\AppBundle\Entity\Solicitation $follower)
    {
        $this->followers[] = $follower;

        return $this;
    }

    /**
     * Remove follower.
     *
     * @param \AppBundle\Entity\Solicitation $follower
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeFollower(\AppBundle\Entity\Solicitation $follower)
    {
        return $this->followers->removeElement($follower);
    }

    /**
     * Get followers.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFollowers()
    {
        return $this->followers;
    }
}
