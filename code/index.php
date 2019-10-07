<?php
echo "Tag recherché: ", $_POST['tag'];
echo "<br><br>";

# Création de l'URL API à appeler
$params = array(
    'api_key'	=> 'API_KEY',
    'method'	=> 'flickr.photos.search',
    'tags'	    => 'sport',
    'per_page'  => '5',
    'format'    => 'json',
    'extras'     => 'url_s',
);

$encoded_params = array();

foreach ($params as $k => $v){
	$encoded_params[] = urlencode($k).'='.urlencode($v);
}

echo "encoded_params: ";
print_r($encoded_params);
echo "<br><br>";

# Appel de l'API et décodage de la réponse
$url = "https://api.flickr.com/services/rest/?nojsoncallback=1&".implode('&', $encoded_params);

$photos = file_get_contents($url);

echo "photos: ";
echo $photos;
echo "<br><br>";

echo "decoded: ";
var_dump(json_decode('{"test":"1234"}'));
echo "<br><br>";

$decoded_json = json_decode($photos);
echo "decoded_photos: ";
var_dump($decoded_json);
echo "<br><br>";

echo "json_vars: ";
var_dump(get_object_vars($decoded_json));
echo "<br><br>";

echo "photos: ";
var_dump($decoded_json->photos);
echo "<br><br>";

$single_photo = $decoded_json->photos->photo[0];
echo "single_photo: ";
var_dump($single_photo);
echo "<br><br>";

echo "single_photos_url: ";
var_dump($single_photo->url_s);
echo "<br><br>";

echo "<img src='".$single_photo->url_s."'>";