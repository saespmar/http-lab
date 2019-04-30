<?php

$host = 'localhost';
$port = '27017';
$dbName = 'httplab';

try {
    $dbDriver = new MongoDB\Driver\Manager('mongodb://' . $host . ':' . $port);
} catch (Exception $e) {
    echo 'MongoDB Driver failed: ' . $e->getMessage() . '\n';
}

?>