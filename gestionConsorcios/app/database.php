<?php
	
class Database{
public static $db;	
public static $con;
public static $debug = true;
    public function __construct(){
      	$this->user="solcareaga_adminGesCon";
        $this->pass="Lucia2021...";
        $this->host="10.0.10.70";
        $this->ddbb="solcareaga_GesCon";
        
  }
    	


	function connect(){
		$con = mysqli_connect($this->host,$this->user,$this->pass,$this->ddbb) or  die("fallo la conexión");

         
          mysqli_set_charset($con,"utf8");
		return $con;   
        
	
	}    public function query($sql){
        
        
     //   echo "<br /><br />sql a ejecutar-> ".$sql;
        $con = $this->connect();
        $res = mysqli_query($con,$sql) or die (mysqli_error($con));
        if($res){
            
           // echo "<pre>"; print_r($res); echo "</pre>";
            return $res;
            }
        else{
            if($this->debug) printf("Error: %s || Query '%s'", mysqli_error($con),$sql);
        }
        
    }

	public static function getCon(){
		if(self::$con==null && self::$db==null){
			self::$db = new Database();
			self::$con = self::$db->connect();
		}
		return self::$con;
	}
      public function insertarArray($data, $tableName) {
    if(!count($data)) {
        return '';
    }
    $columnNames = implode("`,`",array_keys($data));
    $values = implode("','", array_values($data));
    $sql= "INSERT INTO `".$tableName."` (`".$columnNames."`)"." VALUES ('".$values."');";
    
    $this->query($sql);
    
    $lastid = mysqli_insert_id($this->db); 
    
    return $lastid;
    
}
    public function insertar($tabla, $data){
        $consulta="insert into ".$tabla." values(null,". $data .")";
        $resultado=$this->query($consulta);
        if ($resultado) {
            return true;
        }else {
            return false;
        }
     }
    public function mostrar($tabla,$condicion){
        $consul="select * from ".$tabla." where ".$condicion.";";
            $resu=$this->query($consul);
            
            return $resu;
        } 
    public function actualizar($tabla, $data, $condicion){       
        $consulta="update ".$tabla." set ". $data ." where ".$condicion;
        $resultado=$this->db->query($consulta);
        if ($resultado) {
            return true;
        }else {
            return false;
        }
     }
    public function eliminar($tabla, $condicion){
        $eli="delete from ".$tabla." where ".$condicion;
        $res=$this->db->query($eli);
        if ($res) {
            return true; 
        }else {
            return false;
        }
    }
}
?>