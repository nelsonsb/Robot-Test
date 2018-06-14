<?php
/*doc autoload.php

Versión : 1.0
Fecha   : 2018-06-13
Autor   : Nelson Sánchez Bernal (nelson@moviltracing.com)

--------------------------------------------------------------
DISCLAIMER:

Use only for didactical purpose.
--------------------------------------------------------------
doc*/


//echo __DIR__;

$ficheros = scandir(__DIR__,  SCANDIR_SORT_DESCENDING);

// print_r($ficheros);
// exit;

foreach ($ficheros as $value) {
	$value1 = __DIR__ . DIRECTORY_SEPARATOR . $value;
	if( is_file($value1) ){
		if (preg_match("/(.*.php)/i", $value)) {
			if($value != "index.php"){
				include_once $value1;
			}
		}
	}
}


?>