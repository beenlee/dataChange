<?php

/**
 * 电子白板轨迹对象
 * @author howdo
 *
 */
class RecordData{

	private $_jsonData = array("info"=>array(),"records"=>array());

	public function __construct(){

	}
	// "info":{"version":"1.0.0.0","canvasWidth":0.000000,"canvasHeight":0.000000,"audioOffset":0.000000},
	public function set_version( $ver ){
		$this->_jsonData['info']["version"] = $ver;
		return $this;
	}

	public function set_width( $width ){
		$this->_jsonData['info']["canvasWidth"] = $width;
		return $this;
	}

	public function set_height( $height ){
		$this->_jsonData['info']['canvasHeight'] = $height;
		return $this;

	}

	public function set_audioOffset( $audioOffset ){
		$this->_jsonData['info']['audioOffset'] = $audioOffset;
		return $this;
	}

	//
	public function add_record( $record ){
		$this->_jsonData["records"][] = $record;
		return $this;
	}
	
	public function clear(){
		$this->_jsonData = array("info"=>array(),"records"=>array());
	}

	public function to_json(){
		return json_encode($this->_jsonData);
	}


}
