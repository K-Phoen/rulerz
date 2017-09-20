<?php

namespace Entity\Doctrine;

/**
 * @Entity
 * @Table(name="players")
 */
class Player
{
    /**
     * @Id
     * @Column(type="text")
     */
    public $pseudo;

    /**
     * @Column(type="text")
     */
    public $fullname;

    /**
     * @Column(type="text")
     */
    public $gender;

    /**
     * @Column(type="integer")
     */
    public $points;

    /**
     * @ManyToOne(targetEntity="Group", inversedBy="players")
     * @JoinColumn(name="group_id", referencedColumnName="id")
     */
    public $group;

    /**
     * @Embedded(class = "Address")
     */
    public $address;

    /**
     * @Column(type="date")
     */
    public $birthday;

    public function __construct($pseudo, $fullname, $gender, $points, $group = null, \DateTime $birthday = null)
    {
        $this->pseudo = $pseudo;
        $this->fullname = $fullname;
        $this->gender = $gender;
        $this->points = $points;
        $this->group = $group;
        $this->birthday = $birthday;
    }
}
