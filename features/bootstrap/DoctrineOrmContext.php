<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

use Entity\Player;

class DoctrineOrmContext extends BaseContext
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct()
    {
        $paths = [__DIR__.'/../../examples/entities'];
        $isDevMode = true;

        // the connection configuration
        $dbParams = array(
            'driver' => 'pdo_sqlite',
            'path'   => __DIR__.'/../../examples/rulerz.db',
        );

        $config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);

        $this->entityManager = EntityManager::create($dbParams, $config);
    }

    protected function getTarget()
    {
        return new \RulerZ\Compiler\Target\Sql\DoctrineQueryBuilderVisitor();
    }

    protected function getDefaultDataset()
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('p')
            ->from('Entity\Player', 'p');
    }

    /**
     * @When I use the query builder dataset
     */
    public function iUseTheQueryBuilderDataset()
    {
        $this->dataset = $this->getDefaultDataset();
    }
}
