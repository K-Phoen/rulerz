<?php

namespace Entity;

/**
 * @Entity
 */
class User
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    /**
     * @Column(type="text")
     */
    public $name;

    /**
     * @Column(type="text")
     */
    public $group;

    /**
     * @Column(type="integer")
     */
    public $points;

    public function __construct($name, $group, $points)
    {
        $this->name = $name;
        $this->group = $group;
        $this->points = $points;
    }
}
