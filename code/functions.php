<?php

use MongoDB\Client as MongoDbClient;

function connectToMongo() {
    $con = new MongoDB\Driver\Manager("mongodb://localhost:27020");

    if ($con == null){
        echo "La connexion à Mongo a échoué !";
    } else {
        return $con;
    }
}

function checkAPI_KEY($content, $string) {
    if (strlen(strstr($content, $string)) == 0) {
        echo "La clef API n'a pa été trouvée, aucune photo n'a pu être trouvée.";
        exit;
    }
}

function convertStringDateToUnix($date) {
    return strtotime($date);
}

function encode_params($params) {
    $encoded_params = array();
    
    foreach ($params as $k => $v) {
        $encoded_params[] = urlencode($k).'='.urlencode($v);
    }

    return $encoded_params;
}

function get_photos($params) {
    $url = "https://api.flickr.com/services/rest/?".implode('&', encode_params($params));

    $results = file_get_contents($url);
    $decoded_json = json_decode($results);
    $photos = $decoded_json->photos->photo;

    insertPhotosInDB($photos);

    if (count($photos) > 0) {
        return $photos;
    }

    echo "No images found.";
}

function insertPhotosInDB($photos) {
    $arrayIds = [];

    $query = new MongoDB\Driver\Query([]);
    var_dump($query);

    $rows = $con->executeQuery('db.flickr', $query);
    var_dump($rows);

    foreach ($rows as $row){
        array_push($arrayIds, json_decode(json_encode($row),true)['id']);
        $res = json_decode(json_encode($row),true);
    }

    $bulk = new MongoDB\Driver\BulkWrite;
    $validbulk = 0;

    foreach ($listPhotos['photos']['photo'] as $id => $item) {
        # Inserts photo in the collection if it does not exist
        if (!in_array($item['id'], $arrayIds)) {
            $validbulk = 1;
            $bulk->insert($item);
        }
    }

    if ($validbulk === 1){
        $result = $con->executeBulkWrite('db.flickr', $bulk);
    }
}

function display_images($photos) {
    for ($i = 0; $i < count($photos); $i++) {
        echo "<img src='".$photos[$i]->url_s."'>";
    }
}