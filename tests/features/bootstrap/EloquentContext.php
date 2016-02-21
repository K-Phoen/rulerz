<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Model;

use Entity\Eloquent\Player;

class EloquentContext extends BaseContext
{
    /**
     * {@inheritDoc}
     */
    protected function initialize()
    {
        $capsule = new Capsule();

        $capsule->addConnection([
            'driver'    => 'sqlite',
            'database'  => __DIR__.'/../../../examples/rulerz.db', // meh.
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
        ]);

        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }

    /**
     * {@inheritDoc}
     */
    protected function getCompilationTarget()
    {
        return new \RulerZ\Compiler\Target\Sql\Eloquent();
    }

    /**
     * {@inheritDoc}
     */
    protected function getDefaultDataset()
    {
        return Player::query();
    }
}
