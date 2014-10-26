<?php

session_start();
session_name("gigi");

global $APPLICATION_PATH, $PUBLIC_PATH, $PUBLIC_DOMAIN, $MEDIA_PATH, $MEDIA_URL;

$BASE_PATH = "/home/user/project/";
$APPLICATION_PATH = $BASE_PATH . "application/";
$PUBLIC_PATH = $BASE_PATH . "public/";
$PUBLIC_DOMAIN = $_SERVER["SERVER_NAME"];
$MEDIA_PATH = "/home/user/project/media/";
$MEDIA_URL = "http://media.project.ext/media/";

if (isset ($content_type)){
    header ("Content-Type: " . $content_type . "; charset=utf-8");
}else{
    header ("Content-Type: text/html; charset=utf-8");
}
