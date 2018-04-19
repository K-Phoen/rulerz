<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;

use Entity\Eloquent\Player;

class EloquentContext extends BaseContext
{
    /**
     * {@inheritdoc}
     */
    protected function initialize()
    {
        $capsule = new Capsule();

        $capsule->addConnection([
            'driver' => 'sqlite',
            'database' => __DIR__.'/../../../examples/rulerz.db', // meh.
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
        ]);

        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }

    /**
     * {@inheritdoc}
     */
    protected function getCompilationTarget()
    {
        return new \RulerZ\Target\Eloquent\Eloquent();
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultDataset()
    {
        return Player::query();
    }
}
