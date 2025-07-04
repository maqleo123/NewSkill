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
   
   $nombre=$_POST["nombre"];
   $usuario=$_POST["usuario"];
    $password=$_POST["password"];
    $password2=$_POST["password2"];
    
   if($password2==$password){
       $queryguardausuario="INSERT INTO usuarios(usuario,password,nombre) VALUES 	('$usuario','$password', '$nombre')";
        mysql_query($queryguardausuario,$conexion);	
        session_start();
    $_SESSION["access"] = md5($usuario);
        ?>

            <SCRIPT LANGUAGE='JavaScript'>
            location.href='inicio.php?user=<?=$usario;?>';
            </SCRIPT>
        <?
   }else{
       ?>

            <SCRIPT LANGUAGE='JavaScript'>
            location.href='registrarse.php?user=<?=$usuario;?>&coderror=ec';
            </SCRIPT>
    
    

<?
}

?>