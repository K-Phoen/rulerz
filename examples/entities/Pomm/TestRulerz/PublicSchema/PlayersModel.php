<?php

namespace Entity\Pomm\TestRulerz\PublicSchema;

use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;

use Entity\Pomm\TestRulerz\PublicSchema\AutoStructure\Players as PlayersStructure;

/**
 * PlayersModel
 *
 * Model class for table players.
 *
 * @see Model
 */
class PlayersModel extends Model
{
    use WriteQueries;

    /**
     * __construct()
     *
     * Model constructor
     */
    public function __construct()
    {
        $this->structure = new PlayersStructure();
        $this->flexible_entity_class = '\Entity\Pomm\TestRulerz\PublicSchema\Players';
    }
}
