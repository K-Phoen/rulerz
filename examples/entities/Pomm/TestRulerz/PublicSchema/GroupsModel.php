<?php

namespace Entity\Pomm\TestRulerz\PublicSchema;

use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;

use Entity\Pomm\TestRulerz\PublicSchema\AutoStructure\Groups as GroupsStructure;

/**
 * GroupsModel
 *
 * Model class for table groups.
 *
 * @see Model
 */
class GroupsModel extends Model
{
    use WriteQueries;

    /**
     * __construct()
     *
     * Model constructor
     */
    public function __construct()
    {
        $this->structure = new GroupsStructure();
        $this->flexible_entity_class = '\Entity\Pomm\TestRulerz\PublicSchema\Groups';
    }
}
