<?php 
/*doc index.php

Versión : 1.0
Fecha   : 2018-06-13
Autor   : Nelson Sánchez Bernal (nelson@moviltracing.com)
 
--------------------------------------------------------------
DISCLAIMER:

Use only for didactical purpose.
--------------------------------------------------------------
doc*/

include_once 'autoload.php';


// Leo Archivo de instrucciones
if( !isset($argv[1]) || !isset($argv[2]) ){
	echo "Parameter empty";
	exit;	
}

$source  = json_decode(file_get_contents($argv[1]),true);
$destinationFile = $argv[2];
//print_r($source);

// Creo Objeto robot
$robot = new Robot();
$robot->setDebug(true);
$exito = $robot->setData($source)->startClean($destinationFile);

if ($exito==true){
	echo "Process Finished Successfully";
}else {
	echo "Process Finished with Errors";
}



?>