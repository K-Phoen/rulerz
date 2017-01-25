<?php

namespace Entity\Doctrine;

/**
 * @Embeddable
 */
class Address
{
    /**
     * @Column(type = "string")
     */
    public $street;

    /**
     * @Column(type = "string")
     */
    public $postalCode;

    /**
     * @Column(type = "string")
     */
    public $city;

    /**
     * @Column(type = "string")
     */
    public $country;
}
