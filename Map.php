<?php
/*doc Map.php

Versión : 1.0
Fecha   : 2018-06-13
Autor   : Nelson Sanchez Bernal (nelson@moviltracing.com)

--------------------------------------------------------------
DISCLAIMER:

Use only for didactical purpose.
--------------------------------------------------------------
doc*/


Class  Map {
	
	protected $map;
	protected $visited = [];
	
	function __construct(){
	}
	
	/**
	 * Create map with data provided
	 * @param array $data
	 */
	public function setMap($data){
		$this->map = $data;
	}
	
	/**
	 * Print all map
	 */
	public function printMap(){
		echo PHP_EOL;
		foreach ($this->map as $key => $value){
			foreach ($value as $key2 => $value2){
				if ($value2=="S") {
					echo "[\xb0]\t";
				}elseif ($value2=="null") {
					echo "[\x20]\t";
				}else{
					echo "[".$value2."]\t";
				}
				
			}
			echo PHP_EOL;
		}
	}
	
	/**
	 * Print list of cleaned cells
	 */
	public function printCleaned(){
		$result = $this->getCleaned();
		echo "Cleaned : ".implode(",", $result) . PHP_EOL;
	}
	
	/**
	 * Get list of clean cells
	 * Warning : the list contains original cells marked as clean
	 * 
	 * @return string[]
	 */
	public function getCleaned(){
		$result = [];
		for($i=0; $i<count($this->map); $i++){
			for($j=0; $j<count($this->map[$i]); $j++){
				if ($this->map[$i][$j]=="C") {
					$result[] = "{X:".$j.", Y:".$i."}";
				}
			}
		}
		return $result;
	}
	
	/**
	 * Print list of visited cells
	 */
	public function printVisited(){
		$result = $this->getVisited();
		echo "Visited : ".implode(",", $result) . PHP_EOL;
	}
	
	/**
	 * Return list of visited cells
	 * 
	 * @return array
	 */
	public function getVisited(){
		return $this->visited;
	}
	
	/**
	 * Change cell state
	 * 
	 * @param int $x
	 * @param int $y
	 * @param string $state
	 */
	public function changeCellState($x, $y, $state){
		$this->map[$y][$x] = $state;
		//$this->setVisited($x, $y);
	}
	
	/**
	 * Add position to cell visited list
	 * 
	 * @param int $x
	 * @param int $y
	 */
	public function setVisited($x, $y){
		$this->visited[] = "{X:".$x.", Y:".$y."}";
	}
	
	/**
	 * Return cell state (C,S,null)
	 * 
	 * @param int $x
	 * @param int $y
	 * @return string
	 */
	public function getCellState($x, $y){
		return $this->map[$y][$x];	
	}

	/**
	 * Determines if the cell given is available to clean.
	 * 
	 * @param int $x
	 * @param int $y
	 * @return boolean
	 */
	public function isCellAvailable($x, $y){
		
		if ($this->map[$y][$x]=="S") {
			return true;
		}
		return false;
	}
	
	/**
	 * Determines if the cell given exists.
	 * 
	 * @param int $x
	 * @param int $y
	 * @return boolean
	 */
	public function cellExist($x, $y){
		return isset($this->map[$y][$x]);
	}
}

?>