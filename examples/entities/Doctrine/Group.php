<?php

namespace Entity\Doctrine;

/**
 * @Entity
 * @Table(name="groups")
 */
class Group
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
     * @OneToMany(targetEntity="Player", mappedBy="group")
     */
    public $players;

    /**
     * @ManyToOne(targetEntity="Role", inversedBy="groups")
     * @JoinColumn(name="role_id", referencedColumnName="id")
     */
    public $role;

    public function __construct($name)
    {
        $this->name = $name;
    }
}
