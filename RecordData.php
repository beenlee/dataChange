<?php

/**
 * ���Ӱװ�켣����
 * @author howdo
 *
 */
class RecordData{

	private $_jsonData = array("info"=>array(),"records"=>array());
	//private $_colorMap = array( "#FF0000", "#0000FF","#FFFF00","#00FF00","#000000","#ffffff" );
	private $_colorMap = array( array( "r"=>"1.000000", "g"=>"0.000000", "b"=>"0.000000", "a"=>"1.000000" ),
								array( "r"=>"0.000000", "g"=>"0.000000", "b"=>"1.000000", "a"=>"1.000000" ),
								array( "r"=>"1.000000", "g"=>"1.000000", "b"=>"0.000000", "a"=>"1.000000" ),
								array( "r"=>"0.000000", "g"=>"1.000000", "b"=>"0.000000", "a"=>"1.000000" ),
								array( "r"=>"0.000000", "g"=>"0.000000", "b"=>"0.000000", "a"=>"1.000000" ),
								array( "r"=>"1.000000", "g"=>"1.000000", "b"=>"1.000000", "a"=>"1.000000" ) 
							);
	
	private $_lineSize = array("1.000000","2.000000","3.000000","4.000000","5.000000","6.000000","7.000000","8.000000","9.000000","10.000000","11.000000","12.000000","13.000000","14.000000","15.000000");
	
	
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

	/**
	 * 解析旧的数据并添加到数组
	 * @param unknown $old_data
	 */
	public function parser_and_add_records($old_data){
		$type = $old_data["type"];
		if($type == "stroke"){
			$color = $old_data["color"];
			$size = $old_data["size"];
			$start = $old_data["startTime"];
			$end = $old_data["endTime"];
			$line  = $old_data["line"];
			$count = count($line);
			if( $count == 0 ) return true;
			
			$add_time = ($end-$start) / $count;
			
			//设置颜色
			$tmpArr["class"] = "DRContextRecord";
			$tmpArr["type"] = "1";
			$tmpArr["timestamp"] = $start;
			$tmpArr["data"] = $this->_colorMap[$color];
			$this->add_record($tmpArr);
			unset($tmpArr);
			
			//设置画笔粗细
			$tmpArr["class"] = "DRContextRecord";
			$tmpArr["type"] = "2";
			$tmpArr["timestamp"] = $start;
			$tmpArr["data"] = $this->_lineSize[$size];
			$this->add_record($tmpArr);
			unset($tmpArr);
			
			//设置画笔类型
			$tmpArr["class"] = "DRContextRecord";
			$tmpArr["type"] = "3";
			$tmpArr["timestamp"] = $start;
			$tmpArr["data"] = "0";
			$this->add_record($tmpArr);
			unset($tmpArr);
			
			//转换轨迹
			$t = $start+$add_time;
			$strokeId = time();
			for($i = 0; $i < $count; $t += $add_time, $i++){
				//{"class":"DRStrokeRecord", "timestamp":1.415224, "strokeId":6175405760, "phase":0, "x":276.500000, "y":211.000000},
				$tmpArr["class"] = "DRStrokeRecord";
				$tmpArr["timestamp"] = $t;	
				$tmpArr["strokeId"] = $strokeId;
				$tmpArr["phase"] = ( $i == 0 ) ? 0 : ( ( $i == $count-1 ) ? 2 : 1 );
				$tmpArr["x"] = $line[$i][0];
				$tmpArr["y"] = $line[$i][1];
				$this->add_record($tmpArr);
				unset($tmpArr);
			}
			
		}elseif($type == "eraser"){
			$size = $old_data["size"];
			$start = $old_data["startTime"];
			$end = $old_data["endTime"];
			$line  = $old_data["line"];
			$count = count($line);
			if( $count == 0 ) return true;
			$add_time = ($end-$start) / $count;
			
			
			//设置画笔粗细
			$tmpArr["class"] = "DRContextRecord";
			$tmpArr["type"] = "2";
			$tmpArr["timestamp"] = $start;
			$tmpArr["data"] = $this->_lineSize[$size];
			$this->add_record($tmpArr);
			unset($tmpArr);
			
			//{"class":"DRContextRecord", "timestamp":4.465748, "type":3, "data":16},
			//设置画笔类型
			$tmpArr["class"] = "DRContextRecord";
			$tmpArr["type"] = "3";
			$tmpArr["timestamp"] = $start;
			$tmpArr["data"] = "16";
			$this->add_record($tmpArr);
			unset($tmpArr);
			
			//转换轨迹
			$t = $start+$add_time;
			$strokeId = time();
			for($i = 0; $i < $count; $t += $add_time, $i++){
				//{"class":"DRStrokeRecord", "timestamp":1.415224, "strokeId":6175405760, "phase":0, "x":276.500000, "y":211.000000},
				$tmpArr["class"] = "DRStrokeRecord";
				$tmpArr["timestamp"] = $t;
				$tmpArr["strokeId"] = $strokeId;
				$tmpArr["phase"] = ( $i == 0 ) ? 0 : ( ( $i == $count-1 ) ? 2 : 1 );
				$tmpArr["x"] = $line[$i][0];
				$tmpArr["y"] = $line[$i][1];
				$this->add_record($tmpArr);
				unset($tmpArr);
			}
			
		}elseif ($type == "image"){
			
			$start = $old_data["startTime"];
			//"http://s3.mayiming.net.cn/common/upload/images/upload/trailImg1399344230.jpg"
			$imgUrl = $old_data["imgName"]; 
			$x = $old_data["x"];
			$y  = $old_data["y"];
			$w = $old_data["w"];
			$h  = $old_data["h"];

			//设置图片参数
			$tmpArr["class"] = "DRExternalImageRecord";
			$tmpArr["timestamp"] = $start;
			$tmpArr["relativeSourcePath"] = $imgUrl;
			$tmpArr["x"] = $x;
			$tmpArr["y"] = $y;
			$tmpArr["width"] = $w;
			$tmpArr["height"] = $h;
			$this->add_record($tmpArr);
			unset($tmpArr);
			
		}elseif( $type == "text" ){
			$size = $old_data["size"];
			$start = $old_data["startTime"];
			$text = $old_data["text"];
			$x = $old_data["x"];
			$y = $old_data["y"];
			$w = $old_data["w"];
			$h = $old_data["h"];
			//暂无此接口定义
			
		}elseif($type == "clear"){
			//DRClearCanvasRecord" ： 清除画布事件
			$start = $old_data["startTime"];
			$tmpArr["class"] = "DRClearCanvasRecord";
			$tmpArr["timestamp"] = $start;
        
		}
		
		return true;
		
	}

}
