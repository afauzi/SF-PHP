<?php
#Simple Native Frameworks Engine
#By Anan Fauzi (mr.ananfauzi@gmail.com)
#Price Free
#Licensed MIT
class Engine{
    #Properties Declaration
    public $Controller;
	public $Sfunction;
	public $Param=array();
	protected $array_uri_segment=null;
	
	public function __construct($default_controller=null,$segment_start=null,$url_string=null){
	    if(isset($segment_start)&&!empty($url_string)){
		    $this->array_uri_segment = $this->set_uri_segment($default_controller,$segment_start,$url_string);
		}else{echo "ERROR: DEFAULT CONTROLLER DID\'NT SET";}
	}
	
	#Get controller segment
	protected function set_uri_segment($default_controller=null,$segment_start=null,$url_string=null){
	    $segment=array();$url=null;
		if(empty($this->array_uri_segment)){
		    $url = explode("/",$url_string);
			for($i=0;$i<count($url);$i++){if(intval($i) > intval($segment_start)){if(!empty($url[$i])){$segment[] = $url[$i];}}}
			if(empty($segment)){$segment[]=$default_controller;}
		}
		return $segment;
	}
	
	#Load controller & function
	public function include_controller(){
	    if(!empty($this->array_uri_segment)){
		    $controller=$this->array_uri_segment[0];
			if(file_exists($controller.".php")){
			    include($controller.".php");
				$this->Controller = new $controller();
				if(count($this->array_uri_segment) > 1){
				    $this->Sfunction = $this->array_uri_segment[1];
					if(method_exists($this->Controller,$this->Sfunction)){
					    $this->Controller->load_function($this->array_uri_segment);
					}else{echo "ERROR: METHOD NOT FOUND";}
				}else{$this->Controller->load_function();}
			}else{echo "ERROR: CONTROLLER NOT FOUND";}
		}
	}
}

#Parent class for controller
class Controller{
    #properties declaration
	protected $Model;
    protected $Segment = null;
	protected $Library;
	
	#empty construct
	public function __construct(){}
	
	#empty index
	public function index(){}
	
	#load function/method
	public function load_function($segment=null){
	    if(!empty($segment)){$this->Segment = $segment;}
		if(!empty($this->Segment)){$function = $this->Segment[1];}else{$function = 'index';}
		#execute function/method
		$this->$function();
	}
	
	#get url param
	public function get_uri_param($index=null){
	    $result=null;
	    if(!empty($index) && intval($index) > 0){
	        if(!empty($this->Segment[$index+1])){$result = $this->Segment[$index+1];}
			else{echo "error 404: Parameter ".$index." did\'nt Found";die();}
	    }
	    return $result;
	}
	
	#load model
	public function load_model($model=null){
	    if(!empty($model)){
		    include_once($model.".php");
			$model_file = explode("/",$model);
			$model_class = end($model_file);
		    $this->Model = new $model_class();
		    return $this->Model;
		}
		else{echo "error 404: Model Can't be empty";die();}
	}
	
	#load view
	public function load_view($view=null,$data=null){
	    if(!empty($view)){
		    #Create dynamic variable & include view page
	        if(!empty($data)){
			    foreach($data as $val => $key)
				    {${$val} = $key;}}
			    include_once($view.".php");
		}else{echo "error 404: View Can't be empty";die();}
	}
	
	#load library
	public function load_library($lib=null){
	    if(!empty($lib)){include_once($lib."php");}
	}
	
	#redirect function
	public function redirect($url=null){
	    if(!empty($url)){
	        header("location:http://".$url);
	    }
	}
}

#parent class for model
class Model{
    protected $db;
    protected $conn;
	
	public function __construct(){
	    $this->db = new DB_Connection();
		$this->conn = $this->db->get_connection();
	}
	
	public function close_connection(){$this->conn = null;}
}

#connection class
class DB_Connection{
	protected $dbh;
	public $ora;
	
	public function __construct(){
		#open connection
		$this->open_connection();
		$this->ora = $this->oracle_connect();
	}
	
	protected function open_connection(){
	    try {
            $this->dbh = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
			$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }catch (PDOException $e) {
            echo $e->getMessage();
        }
	}
	
	protected function oracle_connect(){
	    try{
		    $dbh = new PDO ('oci:dbname=daisy:1529/PRODHR','BNI_LPM','BNI_LPM');
			#$dbh = new PDO('mysql:host=localhost;dbname=upload_ijazah','root','');
		    return $dbh;
		}catch(PDOException $e){
		    echo $e->getMessage();
			foreach(PDO::getAvailableDrivers() as $driver)
                echo $driver, '<br>';
		}
	}
	
	public function get_connection(){
	    return $this->dbh;
	}
}
?>