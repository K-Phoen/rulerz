<?php

namespace Entity\Pomm\TestRulerz\PublicSchema;

use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;

use Entity\Pomm\TestRulerz\PublicSchema\AutoStructure\Roles as RolesStructure;

/**
 * RolesModel
 *
 * Model class for table roles.
 *
 * @see Model
 */
class RolesModel extends Model
{
    use WriteQueries;

    /**
     * __construct()
     *
     * Model constructor
     */
    public function __construct()
    {
        $this->structure = new RolesStructure();
        $this->flexible_entity_class = '\Entity\Pomm\TestRulerz\PublicSchema\Roles';
    }
}
