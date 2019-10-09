<?php

require('functions.php');

// phpinfo();

// test_mongo();

$tag = $_POST['tag'];
$min_upload_date = $_POST['min_upload_date'];
$max_upload_date = $_POST['max_upload_date'];

$con = connectToMongo();
var_dump($con);

echo "Tag recherché: ".$tag."<br><br>";

$file_content = file('credentials.txt');

$API_KEY_line = $file_content[0];
$API_KEY_string = "API_KEY=";

checkAPI_KEY($API_KEY_line, $API_KEY_string);

$API_KEY = str_replace($API_KEY_string, '', $API_KEY_line);

$params = array(
    'nojsoncallback'    => 1, # La réponse ne sera pas entourée d'une fonction jsonFlickrApi
    'api_key'           => $API_KEY,
    'method'            => 'flickr.photos.search',
    'tags'              => $tag,
    'per_page'          => '10',
    'format'            => 'json',
    'extras'            => 'date_upload, url_s',
    'min_upload_date'   => convertStringDateToUnix($min_upload_date),
    'max_upload_date'   => convertStringDateToUnix($max_upload_date),
);

$photos = get_photos($params);

display_images($photos);