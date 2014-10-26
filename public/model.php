<?php

require_once "../application/config.php";
include ($APPLICATION_PATH . "orm/model_gen.php");

$md = new Model_Generator();
$md->entities();
$md->daos();