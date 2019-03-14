<?php
require __DIR__ . '/../vendor/autoload.php';

use \Rmlx\SourceHandler\JSONSource;

//error_reporting(E_ERROR);
#$mapper = new RmlMapper();
#$mapper->open("test.rml.ttl");
$graph = new EasyRdf_Graph("../data/test.ttl");

?>