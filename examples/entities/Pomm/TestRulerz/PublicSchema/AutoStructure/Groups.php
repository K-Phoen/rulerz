<?php
/**
 * This file has been automatically generated by Pomm's generator.
 * You MIGHT NOT edit this file as your changes will be lost at next
 * generation.
 */

namespace Entity\Pomm\TestRulerz\PublicSchema\AutoStructure;

use PommProject\ModelManager\Model\RowStructure;

/**
 * Groups
 *
 * Structure class for relation public.groups.
 *
 * Class and fields comments are inspected from table and fields comments.
 * Just add comments in your database and they will appear here.
 *
 * @see http://www.postgresql.org/docs/9.0/static/sql-comment.html
 * @see RowStructure
 */
class Groups extends RowStructure
{
    /**
     * __construct.
     *
     * Structure definition.
     */
    public function __construct()
    {
        $this
            ->setRelation('public.groups')
            ->setPrimaryKey(['id'])
            ->addField('id', 'int4')
            ->addField('role_id', 'int4')
            ->addField('name', 'text')
            ;
    }
}
