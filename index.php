<?php

require_once(__DIR__ . '/bootstrap.php');

use Kickipedia2\Models\Entry;

dispatch('/', function() {
    return 'Hello World!';
});

dispatch('/entry/:id', function() {
    $id = params('id');
    $connection = new \Mongo('localhost');
    $database = $connection->selectDB('kickipedia2');
    $entry = new Entry($database);

    $entry->load($id);

    $loader = new \Twig_Loader_Filesystem(__DIR__ .'/Kickipedia2/views');
    $twig = new \Twig_Environment($loader, array(
      'cache' => __DIR__ .'/cache',
      'auto_reload' => true
    ));

    echo $twig->render('index.html.twig', array('entry' => $entry));

    $connection->close();
});

run();