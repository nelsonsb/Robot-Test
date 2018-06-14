<?php
/*doc Position.php

Versión : 1.0
Fecha   : 2018-06-13
Autor   : Nelson Sanchez Bernal (nelson@moviltracing.com)

--------------------------------------------------------------
DISCLAIMER:

Use only for didactical purpose.
--------------------------------------------------------------
doc*/

Class  Position {
	
	protected $x;
	protected $y;
	protected $f;
	
	function __construct($x, $y, $f){
	
		$this->x = $x;
		$this->y = $y;
		$this->f = strtoupper($f);
	}
	

	public function printPos(){
		echo "Actual position : X=".$this->x." Y=".$this->y." f=".$this->f.PHP_EOL;
	}
	
	public function getPos(){
		return array("X"=>$this->x, "Y"=>$this->y, "facing"=>$this->f);
	}
	
	public function getFacing(){
		return $this->f;
	}
	
	public function setFacing($valor){
		$this->f = $valor;
	}
}

?>