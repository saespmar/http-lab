<?php

// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include database parameters
include_once '../dbParameters.php';
$collection = 'movies';

// Get request data
$body = json_decode(file_get_contents("php://input"), true);

// Check request data
if (json_last_error() != JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(
		array("error" => "body is empty or isn't in JSON format")
	);
} elseif (!array_key_exists('title', $body) || !is_string($body['title']) || strlen($body['title']) > 150) {
    http_response_code(400);
    echo json_encode(
        array("error" => "title missing or invalid")
    );
} else {

    // Filter other allowed fields
    $allowed = array('title', 'year', 'director', 'cast', 'plot');
    $body = array_intersect_key($body, array_flip($allowed));
    if (!array_key_exists('year', $body)) $body['year'] = null;
    if (!array_key_exists('director', $body)) $body['director'] = null;
    if (!array_key_exists('cast', $body)) $body['cast'] = null;
    if (!array_key_exists('plot', $body)) $body['plot'] = null;

    // Check optional fields
    if ($body['year'] != null && (!is_int($body['year']) || $body['year'] < 1878 || $body['year'] > 2100)) {
        http_response_code(400);
        echo json_encode(
            array("error" => "invalid year")
        );
    } elseif ($body['director'] != null && (!is_string($body['director']) || strlen($body['director']) > 150)) {
        http_response_code(400);
        echo json_encode(
            array("error" => "invalid director")
        );
    } elseif ($body['cast'] != null && (array_sum(array_map('is_string', $body['cast'])) != count($body['cast']) || max(array_map('strlen', $body['cast'])) > 150)) {
        http_response_code(400);
        echo json_encode(
            array("error" => "invalid cast array")
        );
    } elseif ($body['plot'] != null && (!is_string($body['plot']) || strlen($body['plot']) > 500)) {
        http_response_code(400);
        echo json_encode(
            array("error" => "invalid plot")
        );
    } else {
        
        // All fields are OK
        $movie = array(
            '_id'      => new \MongoDB\BSON\ObjectId(),
            'title'    => $body['title'], 
            'year'     => $body['year'],
            'director' => $body['director'],
            'cast'     => $body['cast'],
            'plot'     => $body['plot']
        );

        // Insert movie
        $bulkWrite = new MongoDB\Driver\BulkWrite;
        $bulkWrite->insert($movie);
        try {
            $result = $dbDriver->executeBulkWrite("$dbName.$collection", $bulkWrite); 
        } catch (Exception $e) {
            error_log('Adding movie failed: ' . $e->getMessage() . '\n');
        }

        // Return the results
        if ($result->getInsertedCount() != 1) {
            http_response_code(500);
            echo json_encode(
                array("error" => "movie wasn't added")
            );
        } else {
            http_response_code(201);
            echo json_encode(
                array(
                    "id"       => "".$movie["_id"], 
                    "title"    => $movie["title"],
                    "year"     => $movie["year"],
                    "director" => $movie["director"],
                    "cast"     => $movie["cast"],
                    "plot"     => $movie["plot"]
                )
            );
        }
    }
}

?>