<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

class DoctrineOrmContext extends BaseContext
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * {@inheritDoc}
     */
    protected function initialize()
    {
        $paths     = [__DIR__.'/../../../examples/entities']; // meh.
        $isDevMode = true;

        // the connection configuration
        $dbParams = [
            'driver' => 'pdo_sqlite',
            'path'   => __DIR__.'/../../../examples/rulerz.db', // meh.
        ];

        $config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);

        $this->entityManager = EntityManager::create($dbParams, $config);
    }

    /**
     * {@inheritDoc}
     */
    protected function getCompilationTarget()
    {
        return new \RulerZ\Target\DoctrineORM\DoctrineORM();
    }

    /**
     * {@inheritDoc}
     */
    protected function getDefaultDataset()
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('p')
            ->from('Entity\Doctrine\Player', 'p');
    }

    /**
     * @When I use the query builder dataset
     */
    public function iUseTheQueryBuilderDataset()
    {
        $this->dataset = $this->getDefaultDataset();
    }
}
