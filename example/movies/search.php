<?php

// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include database parameters
include_once '../dbParameters.php';
$collection = 'movies';

// Get request data
parse_str($_SERVER['QUERY_STRING'], $queryString);
if (array_key_exists('id', $queryString)) $queryID = $queryString['id'];
else $queryID = null;

// Check request data
if ($queryID == null || strlen($queryID) != 24 || !ctype_xdigit($queryID)) {
    http_response_code(400);
    echo json_encode(
        array("error" => "provide a valid ID")
    );    
} else {
    
    // Search for the movie
    $query = new \MongoDB\Driver\Query(
        ['_id' => new \MongoDB\BSON\ObjectId($queryID)], 
        []
    );
    try {
        $result = $dbDriver->executeQuery("$dbName.$collection", $query); 
    } catch (Exception $e) {
        error_log('Searching movie failed: ' . $e->getMessage() . '\n');
    }
    $result = iterator_to_array($result);

    // Check the result
    if (count($result) < 1) {
        http_response_code(404);
        echo json_encode(
            array("error" => "movie not found")
        );
    } else {
        echo json_encode(
            array(
                "id"       => "".$result[0]->_id, 
                "title"    => $result[0]->title,
                "year"     => $result[0]->year,
                "director" => $result[0]->director,
                "cast"     => $result[0]->cast,
                "plot"     => $result[0]->plot
            )
        );
    }
}

?>