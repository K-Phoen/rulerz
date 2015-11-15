<?php

/** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
list($entityManager, $_) = require __DIR__ . '/../../examples/bootstrap/bootstrap_doctrine.php';

/** @var \Solarium\Client $client */
list($client, $_) = require __DIR__ . '/../../examples/bootstrap/bootstrap_solarium.php';

$players = $entityManager
    ->createQueryBuilder()
    ->select('p')
    ->from('Entity\Doctrine\Player', 'p')
    ->getQuery()->execute();

foreach ($players as $player) {
    // get an update query instance
    $update = $client->createUpdate();

    // create a new document for the data
    $doc = $update->createDocument();

    $doc->id       = uniqid();
    $doc->pseudo   = $player->pseudo;
    $doc->fullname = $player->fullname;
    $doc->age      = (int) $player->age;
    $doc->gender   = $player->gender;
    $doc->points   = (int) $player->points;

    // add the document and a commit command to the update query
    $update->addDocument($doc);
    $update->addCommit();

    $client->update($update);
}
