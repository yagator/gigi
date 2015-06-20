<?php

require_once "../application/config.php";
include ($APPLICATION_PATH . "orm/model_gen.php");

$md = new ORM\Model_Generator();
$md->entities();
$md->daos();