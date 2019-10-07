<?php
$tag = $_POST['tag'];

echo "Tag recherché: ", $tag;
echo "<br><br>";

# Création de l'URL API à appeler
$params = array(
    'api_key'	=> 'API_KEY',
    'method'	=> 'flickr.photos.search',
    'tags'	    => $tag,
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
# Le paramètre nojsoncallback=1 permet d'obtenir une réponse qui ne sera pas entourée d'une fonction jsonFlickrApi
$url = "https://api.flickr.com/services/rest/?nojsoncallback=1&".implode('&', $encoded_params);

$photos = file_get_contents($url);

echo "photos: ";
echo $photos;
echo "<br><br>";

$decoded_json = json_decode($photos);
echo "decoded_photos: ";
var_dump($decoded_json);
echo "<br><br>";

echo "json_vars: ";
var_dump(get_object_vars($decoded_json));
echo "<br><br>";

$photos = $decoded_json->photos->photo;
echo "photos: ";
var_dump($photos);
echo "<br><br>";

$single_photo = $photos[0];
echo "single_photo: ";
var_dump($single_photo);
echo "<br><br>";

echo "single_photos_url: ";
var_dump($single_photo->url_s);
echo "<br><br>";

for ($i = 0; $i < count($photos); $i++) {
    echo "<img src='".$photos[$i]->url_s."'><br><br>";
}
