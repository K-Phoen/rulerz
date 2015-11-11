<?php

namespace Entity\Pomm\TestRulerz\PublicSchema;

use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\Projection;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;

use PommProject\Foundation\Where;

use Entity\Pomm\TestRulerz\PublicSchema\AutoStructure\Groups as GroupsStructure;
use Entity\Pomm\TestRulerz\PublicSchema\Groups;

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
     *
     * @access public
     */
    public function __construct()
    {
        $this->structure = new GroupsStructure;
        $this->flexible_entity_class = '\Entity\Pomm\TestRulerz\PublicSchema\Groups';
    }
}
