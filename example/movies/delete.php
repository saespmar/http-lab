<?php

// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
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
    
    // Delete operation
    $bulkWrite = new MongoDB\Driver\BulkWrite;
    $bulkWrite->delete(
        ['_id' => new \MongoDB\BSON\ObjectId($queryID)]
    );
    try {
        $result = $dbDriver->executeBulkWrite("$dbName.$collection", $bulkWrite); 
    } catch (Exception $e) {
        error_log('Deleting movie failed: ' . $e->getMessage() . '\n');
    }

    // Check the result
    if ($result->getDeletedCount() != 1) {
        http_response_code(404);
        echo json_encode(
            array("error" => "movie not found")
        );
    } else {
        echo json_encode(
            array("message" => "movie deleted successfully")
        );
    }
}

?>