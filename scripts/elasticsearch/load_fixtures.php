<?php

/** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
list($entityManager, $_) = require __DIR__ . '/../../examples/bootstrap_doctrine.php';

/** @var \Elasticsearch\Client $client  */
/** @var \RulerZ\RulerZ $rulerz  */
list($client, $rulerz) = require __DIR__ . '/../../examples/bootstrap_elasticsearch.php';

$players = $entityManager
    ->createQueryBuilder()
    ->select('p')
    ->from('Entity\Player', 'p')
    ->getQuery()->execute();

foreach ($players as $player) {
    $params = [
        'body'  => [
            'pseudo'   => $player->pseudo,
            'fullname' => $player->fullname,
            'age'      => (int) $player->age,
            'gender'   => $player->gender,
            'points'   => (int) $player->points
        ],
        'index' => 'rulerz_tests',
        'type'  => 'player',
        'id'    => uniqid(),
    ];
    $client->index($params);
}
