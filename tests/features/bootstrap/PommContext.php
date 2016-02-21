<?php

use PommProject\Foundation\Pomm;

class PommContext extends BaseContext
{
    private $pomm;

    /**
     * {@inheritDoc}
     */
    protected function initialize()
    {
        $this->pomm = new Pomm(['test_rulerz' => [
            'dsn'                   => sprintf('pgsql://%s:%s@%s:%d/%s', $_ENV['POSTGRES_USER'], $_ENV['POSTGRES_PASSWD'], $_ENV['POSTGRES_HOST'], $_ENV['POSTGRES_PORT'], $_ENV['POSTGRES_DB']),
            'class:session_builder' => '\PommProject\ModelManager\SessionBuilder'
        ]]);
    }

    /**
     * {@inheritDoc}
     */
    protected function getCompilationTarget()
    {
        return new \RulerZ\Compiler\Target\Sql\Pomm();
    }

    /**
     * {@inheritDoc}
     */
    protected function getDefaultDataset()
    {
        return $this->pomm['test_rulerz']->getModel('\Entity\Pomm\TestRulerz\PublicSchema\PlayersModel');
    }
}
