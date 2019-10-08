<?php

use MongoDB\Client as MongoDbClient;
$tag = $_POST['tag'];
$con = new MongoDB\Driver\Manager("mongodb://localhost:27017");

if($con !== null){
   echo "Connecté à Mongo !";
}



echo "Tag recherché: ".$tag."<br><br>";

$file_content = file('credentials.txt');
$API_KEY = str_replace('API_KEY=', '', $file_content[0]);

# Création de l'URL API à appeler
$params = array(
    'api_key'   => $API_KEY,
    'method'    => 'flickr.photos.search',
    'tags'      => $tag,
    'per_page'  => '10',
    'format'    => 'json',
    'extras'    => 'url_s',
);

$encoded_params = array();

foreach ($params as $k => $v) {
	$encoded_params[] = urlencode($k).'='.urlencode($v);
}

# Appel de l'API et décodage de la réponse.
# Le paramètre nojsoncallback=1 permet d'obtenir une réponse qui ne sera pas entourée d'une fonction jsonFlickrApi
$url = "https://api.flickr.com/services/rest/?nojsoncallback=1&".implode('&', $encoded_params);

# Traitement des résultat pour obtenir la liste de photos
$results = file_get_contents($url);

$bulk = new MongoDB\Driver\BulkWrite;
$listPhotos = json_decode($results);

//var_dump($listPhotos);
$bulk->insert($listPhotos);
$con->executeBulkWrite('db.MyCollection', $bulk);

//foreach ($listPhotos[photos][photo] as $id => $item) {
//   $con->insert($item);
//}

$decoded_json = json_decode($results);
$photos = $decoded_json->photos->photo;
//echo $results;
for ($i = 0; $i < count($photos); $i++) {
    echo "<img src='".$photos[$i]->url_s."'>";
	
}