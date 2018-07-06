<?php
/*doc Robot.php

Versión : 1.0
Fecha   : 2018-06-13
Autor   : Nelson Sanchez Bernal (nelson@moviltracing.com)

--------------------------------------------------------------

DESCARGO:

--------------------------------------------------------------
doc*/


Class  Robot {
	
	protected $map;
	protected $posIni;
	protected $posAct;
	protected $batteryIni;
	protected $batteryEnd;
	protected $startCommands;
	protected $backStrategy = array(array("TR","A"), 
									array("TL","B","TR","A"), 
									array("TL", "TL", "A"),
									array("TR", "B", "TR", "A"),
									array("TL", "TL", "A")
									);
	protected $debug = false;
	protected $destFile;
	
	function __construct(){}
	
	/**
	 * Set data to start process
	 * @param Array $data
	 * @return Robot
	 */
	public function setData($data){
		
		// Load initial data
		$this->map = new Map();
		$this->map->setMap($data['map']);
		
		// Load star pos
		$this->posIni = new Position($data['start']['X'], $data['start']['Y'], $data['start']['facing']);
		$this->posAct = clone $this->posIni;
		
		// Load Battery Level
		$this->batteryIni= intval($data['battery']);
		$this->batteryEnd= $this->batteryIni;
		
		// Load program
		$this->startCommands = $data['commands'];
		
		return $this;
	}
	
	/**
	 * Start Clean Process
	 * 
	 * @param string $file  Destination file to save process result
	 * @return boolean
	 */
	public function startClean($file){
		
		$exito = true;
		// Set destination file
		$this->destFile = $file;
		
		if ($this->_executeProcess($this->startCommands)==true) {
			// Finished OK
		}else {
			// Finished with error
			$exito = false;
		}
		
		$this->_createOutput();
		$this->_printStepByStep(); // Print final result
		return $exito;
	}
	
	/**
	 * Method that execute a list of commands
	 * 
	 * @param array $commands
	 * @return boolean
	 */
	protected function _executeProcess($commands){
		
		foreach ($commands as $value){
			
			// Validate Battery
			if ( intval($this->batteryEnd)<=4) {
				return false;
			}

			if ($this->_executeCommand($value)!==true) {
			
				// Error case
				if ($value=="A") {
					$exito2=false;
					$i = 0;
					do {
						echo "\tApply error commands...".$i.PHP_EOL;
						if ($this->_executeBackStrategy($this->backStrategy[$i])===true) {
							$exito2 = true;
							break;
						}
						$i++;
					} while ($i < count($this->backStrategy) );
					//return false;
					if ($exito2==false) {
						return false;
					}
				}
			}
		}
		return true;
	}
	
	protected function _executeBackStrategy($commands){
		
		foreach ($commands as $value){
			// Validate Battery
			if ( intval($this->batteryEnd)<=4) {
				return false;
			}
			
			if ($this->_executeCommand($value)!==true) {
				return false;
			}
		}
		return true;
	}
	
	/**
	 * Method tha execute only command
	 * 
	 * @param unknown $command
	 * @return boolean
	 */
	private function _executeCommand($command){

		$exito = true;
		$bateria = 0;
		
		// TL y TR
		// ==> Change orientation
		if (strtoupper($command)=="TL" || strtoupper($command)=="TR") {
			$bateria = 1;
			$this->_changeOrientation($command);
		}
		
		// A
		// ==> Change pos - Change battery level, change orientation
		if (strtoupper($command)=="A" ) {
			$bateria = 2;
			
			if ($this->_advance($command)==false) {
				$exito = false;
			}
		}
		// B
		// ==> Change pos - change cell statte and change battery level
		if (strtoupper($command)=="B" ) {
			$bateria = 3;
			if ($this->_advance($command)==false) {
				$exito = false;
			}
		}
		
		// C
		// ==> Change cell state and battery level
		if (strtoupper($command)=="C" ) {
			$bateria = 5;
			
			if ( $this->map->getCellState($this->posAct->getPos()['X'], $this->posAct->getPos()['Y'])!="C" 
					&& $this->map->getCellState($this->posAct->getPos()['X'], $this->posAct->getPos()['Y'])!=null
					&& $this->batteryEnd>$bateria) {
				$this->_changeCellState($command);
			}else {
				$exito = false;
			}
	
		}
		
		if ($exito) {
			// Update battery level
			$this->_updateBatteryLevel($bateria);
		}
		
		// Print Messages
		$this->_printStepByStep($command);
		
		return $exito;
	}
	
	/**
	 * Print Battery value in debug mode
	 */
	private function _printBattery(){
		echo "Battery : ".$this->batteryEnd.PHP_EOL;
	}
	
	/**
	 * Set debug value
	 * 
	 * @param boolean $valor
	 */
	public function setDebug($valor=false){
		$this->debug = $valor;
	}
	
	/**
	 * Print actual data only in debug mode
	 */
	private function _printStepByStep($command="X"){
		
		if ($this->debug) {
			$this->map->printMap();
			if ($command!="X") {
				echo "Command : ".$command.PHP_EOL;
			}
			$this->posAct->printPos();
			$this->_printBattery();
			$this->map->printCleaned();
			$this->map->printVisited();
			
			echo PHP_EOL;
			sleep(1);
		}
	}
	
	/**
	 * Create output file
	 * 
	 * @return boolean
	 */
	private function _createOutput(){
		
		$result = array("battery"=>$this->batteryEnd,
						"final" => $this->posAct->getPos(),
						"cleaned" =>$this->map->getCleaned() ,
						"visited" => $this->map->getVisited()
						);
		
		$data = json_encode($result);
		$exito = file_put_contents ( $this->destFile , $data );
		
		return ($exito!==false)?true:$exito;
	}
	
	/**
	 * Update Battery value
	 * 
	 * @param int $valor
	 */
	private function _updateBatteryLevel($valor){
		$this->batteryEnd = $this->batteryEnd-$valor;
	}

	/**
	 * Change orientation of robot
	 * 
	 * @param string $command
	 * @return boolean
	 */
	private function _changeOrientation($command){
	
		$f = $this->posAct->getFacing();
		switch ($f) {
			case "N":
				if ($command=="TL") {
					$new = "W";
				}else {
					$new = "E";
				}
				break;
			case "S":
				if ($command=="TL") {
					$new = "E";
				}else {
					$new = "W";
				}
				break;
			case "E":
				if ($command=="TL") {
					$new = "N";
				}else {
					$new = "S";
				}
				break;
			case "W":
				if ($command=="TL") {
					$new = "S";
				}else {
					$new = "N";
				}
				break;
		}
		
		$this->posAct->setFacing($new);
		return true;
	}
	
	/**
	 * Change the state of the actual position
	 * Apply for Clean or visited cell
	 * 
	 * @param string $command
	 */
	private function _changeCellState($command){
		
		$x = $this->posAct->getPos()['X'];
		$y = $this->posAct->getPos()['Y'];
		
		if ($command=="C") {
			$this->map->changeCellState($x, $y, "C");
		}else {
			$this->map->changeCellState($x, $y, "V");
		}
	}
	
	private function _advance($command) {
		
		$exito = false;
		
		$x = $this->posAct->getPos()['X'];
		$y = $this->posAct->getPos()['Y'];
		$f = $this->posAct->getPos()['facing'];
		
		switch ($f) {
			case "N":
				if ($command=="B") {
					$x2 = $x;
					$y2 = $y+1;
				}else {
					$x2 = $x;
					$y2 = $y-1;
				}
				break;
			case "S":
				if ($command=="B") {
					$x2 = $x;
					$y2 = $y-1;
				}else {
					$x2 = $x;
					$y2 = $y+1;
				}
				break;
			case "E":
				if ($command=="B") {
					$x2 = $x-1;
					$y2 = $y;
				}else {
					$x2 = $x+1;
					$y2 = $y;
				}
				break;
			case "W":
				if ($command=="B") {
					$x2 = $x+1;
					$y2 = $y;
				}else {
					$x2 = $x-1;
					$y2 = $y;
				}
				break;
		}
		
		$newPos = new Position($x2, $y2, $f);
		
		if ($this->map->cellExist($x2, $y2)) {
			if ($this->map->isCellAvailable($x2, $y2)) {
				$this->posAct = $newPos;
				$exito = true;
				$this->map->setVisited($x2, $y2);
				echo "\t Si pudo avanzar...".PHP_EOL;
			}
		}
		
		return $exito;
	}
	
}
	
?>
	
