<?php

namespace Entity\Doctrine;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(name="roles")
 */
class Role
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @Column(type="text")
     */
    public $name;

    /**
     * @OneToMany(targetEntity="Group", mappedBy="role")
     */
    public $groups;

    public function __construct($name)
    {
        $this->name = $name;

        $this->groups = new ArrayCollection();
    }
}
