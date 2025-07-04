<?
   function Conectarse(){
    if (!($conexion=mysql_connect("localhost", "newskillcom_santy", "Diciembre1224#*")))
    {
    echo "Error conectando a la base de datos.";
    exit();
    }
    if (!mysql_select_db("newskillcom_newskill",$conexion))
    {
    echo "Error seleccionando la base de datos.";
    exit();
    }
    return $conexion;
    }
   $conexion=Conectarse();
   ?>
