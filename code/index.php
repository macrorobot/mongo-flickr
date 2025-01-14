<?php

use MongoDB\Client as MongoDbClient;
$tag = $_POST['tag'];
$con = new MongoDB\Driver\Manager("mongodb://localhost:27017");

if($con !== null){
   //echo "Connecté à Mongo !";
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
$listPhotos = json_decode($results, true);
$arrayPhotos = json_decode(json_encode($results),true);
if(count($listPhotos['photos']['photo']) === 0) {
	echo "No images found";
}
$arrayIds = [];

$query = new MongoDB\Driver\Query([]);
$rows = $con->executeQuery('db.FlickrPhotos', $query);
foreach($rows as $r){
	array_push($arrayIds, json_decode(json_encode($r),true)['id']);
	$res = json_decode(json_encode($r),true);
}

$validbulk = 0;
foreach ($listPhotos['photos']['photo'] as $id => $item) {
	if (in_array($item['id'], $arrayIds)) {
		
	} else {
		$validbulk = 1;
		$bulk->insert($item);
	}
}
if($validbulk === 1){
   $result = $con->executeBulkWrite('db.FlickrPhotos', $bulk);
}

$decoded_json = json_decode($results);
$photos = $decoded_json->photos->photo;

for ($i = 0; $i < count($photos); $i++) {
    echo "<img src='".$photos[$i]->url_s."'>";
	
}

