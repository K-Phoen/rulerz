<?php

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;

class DoctrineDBALContext extends BaseContext
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $connection;

    /**
     * {@inheritDoc}
     */
    protected function initialize()
    {
        $connectionParams = [
            'driver' => 'pdo_sqlite',
            'path'   => __DIR__.'/../../../examples/rulerz.db', // meh.
        ];
        $this->connection = DriverManager::getConnection($connectionParams, new Configuration());
    }

    /**
     * {@inheritDoc}
     */
    protected function getCompilationTarget()
    {
        return new \RulerZ\Compiler\Target\Sql\DoctrineDBAL();
    }

    /**
     * {@inheritDoc}
     */
    protected function getDefaultDataset()
    {
        return $this->connection
            ->createQueryBuilder()
            ->select('*')
            ->from('players');
    }

    /**
     * @When I use the query builder dataset
     */
    public function iUseTheQueryBuilderDataset()
    {
        $this->dataset = $this->getDefaultDataset();
    }
}
