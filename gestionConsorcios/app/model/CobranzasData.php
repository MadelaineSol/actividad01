<?php
    
	// require_once 'app/database.php';
    require_once __DIR__ . '/../database.php';
    
class CobranzasData extends Database{
    
    var $tabla = 'cobranzas';
    
    public function loguear($datos){ // Buscar usuario en tabla de usuarios 
        
       $user = $datos['usuario'];
       $pass = $datos['password'];
       
         $sql = "SELECT *  FROM `".$this->tabla."` WHERE `user` LIKE '$user' AND `pass` LIKE '$pass' LIMIT 1";
      //  echo $sql;
      $data = $this->query($sql);
    //  echo "<pre>"; print_r($data); echo "</pre>";
        foreach($data as $value){
            
        }
        return $value;
    }
    public function sucursal_asignada($user_id){ // Buscar sucursales asignadas al usuario
        
        $sql = "SELECT * FROM `sucursal_asignada` WHERE `id_usuario` = $user_id";
        // echo $sql; exit();
        $data = $this->query($sql);
        
        return $data;
    }
        public function datos_sucursal($id){ // Buscar sucursales asignadas al usuario
        /**
        * $id = Id de la sucursal o el Hash asignado
        **/
        $sql = "SELECT * FROM `sucursal` WHERE `id_sucursal` = '$id' OR `hash` LIKE '$id' LIMIT 1";
        //echo $sql; exit();
        $data = $this->query($sql);
        
         foreach($data as $value){
            
        }
        return $value;
    }
            public function datos_empresa($id){ // Buscar sucursales asignadas al usuario
        /**
        * $id = Id de la empresa o el Hash asignado
        **/
        $sql = "SELECT * FROM `empresa` WHERE `id_empresa` = '$id' OR `hash` LIKE '$id' LIMIT 1";
        // echo $sql; exit();
        $data = $this->query($sql);
        
         foreach($data as $value){
            
        }
        return $value;
    }
        public function cajas_asignada($user_id){ // Buscar sucursales asignadas al usuario
        
        $sql = "SELECT * FROM `caja` WHERE `id_sucursal` = $user_id";
        // echo $sql; exit();
        $data = $this->query($sql);
        
        return $data;
    }
    
        public function get_cobranza_by_id_empresa($id){ // Buscar sucursales asignadas al usuario
        /**
        * $id = Id de la caja o el Hash asignado
        **/
         $sql = "SELECT * FROM `cobranzas` WHERE `id_empresa_administradora` = '$id' ";
        // echo $sql; exit();
        $data = $this->query($sql);
        
        //  foreach($data as $value){
            
        // }
        return $data;
    }
            public function formas_pago_caja($id = 0){ // Buscar sucursales asignadas al usuario
        /**
        * $id = Id de la caja o el Hash asignado
        **/
        $sql = "SELECT * FROM `cbte_forma_pago` WHERE `id_caja` = $id";
        // echo $sql; exit();
        $data = $this->query($sql);
        
      
        return $data;
    }
}

?>