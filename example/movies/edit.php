<?php

// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include database parameters
include_once '../dbParameters.php';
$collection = 'movies';

// Get request data
parse_str($_SERVER['QUERY_STRING'], $queryString);
if (array_key_exists('id', $queryString)) $queryID = $queryString['id'];
else $queryID = null;
$body = json_decode(file_get_contents("php://input"), true);

// Check request data
if ($queryID == null || strlen($queryID) != 24 || !ctype_xdigit($queryID)) {
    http_response_code(400);
    echo json_encode(
        array("error" => "provide a valid ID")
    );    
} elseif (json_last_error() != JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(
        array("error" => "body must be in JSON format")
    );
} else {

    // Filter editable fields
    $allowed = array('title', 'year', 'director', 'cast', 'plot');
    $body = array_intersect_key($body, array_flip($allowed));

    // Check fields
    if (array_key_exists('title', $body) && (!is_string($body['title']) || strlen($body['title']) > 150)){
        http_response_code(400);
        echo json_encode(
            array("error" => "invalid title")
        );
    } elseif (array_key_exists('year', $body) && $body['year'] != null && (!is_int($body['year']) || $body['year'] < 1878 || $body['year'] > 2100)) {
        http_response_code(400);
        echo json_encode(
            array("error" => "invalid year")
        );
    } elseif (array_key_exists('director', $body) && $body['director'] != null && (!is_string($body['director']) || strlen($body['director']) > 150)) {
        http_response_code(400);
        echo json_encode(
            array("error" => "invalid director")
        );
    } elseif (array_key_exists('cast', $body) && $body['cast'] != null && (array_sum(array_map('is_string', $body['cast'])) != count($body['cast']) || max(array_map('strlen', $body['cast'])) > 150)) {
        http_response_code(400);
        echo json_encode(
            array("error" => "invalid cast array")
        );
    } elseif (array_key_exists('plot', $body) && $body['plot'] != null && (!is_string($body['plot']) || strlen($body['plot']) > 500)) {
        http_response_code(400);
        echo json_encode(
            array("error" => "invalid plot")
        );
    } else {

        // Update movie
        $bulkWrite = new MongoDB\Driver\BulkWrite;
        $bulkWrite->update(
            ['_id' => new \MongoDB\BSON\ObjectId($queryID)], 
            ['$set' => $body]
        );
        try {
            $result = $dbDriver->executeBulkWrite("$dbName.$collection", $bulkWrite); 
        } catch (Exception $e) {
            error_log('Updating movie failed: ' . $e->getMessage() . '\n');
        }

        // Return the results
        if ($result->getMatchedCount() != 1) {
            http_response_code(404);
            echo json_encode(
                array("error" => "movie not found")
            );
        } else {

            // Search for the updated movie and return it
            $query = new \MongoDB\Driver\Query(
                ['_id' => new \MongoDB\BSON\ObjectId($queryID)], 
                []
            );
            try {
                $result = $dbDriver->executeQuery("$dbName.$collection", $query); 
            } catch (Exception $e) {
                error_log('Updating movie failed: ' . $e->getMessage() . '\n');
            }
    
            $result = iterator_to_array($result);
            http_response_code(201);
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
}

?>