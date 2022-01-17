<?php

/** Loads the WordPress Environment and Template */
require '../../../wp-load.php';

if(!is_user_logged_in()){
    header('HTTP/1.0 403 Forbidden');
    die();
}

$fichier_local = ABSPATH.$_SERVER["REQUEST_URI"];
if(!file_exists($fichier_local)) die();


$mime_type = mime_content_type($fichier_local);
$file_content = file_get_contents($fichier_local);

header('Content-Type: '.$mime_type);
echo $file_content;
die();

