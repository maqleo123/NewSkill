<?php
//echo count($_FILES["file0"]["name"]);exit;
$idLimpio=$_GET["idLimpio"];
if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_FILES["fileToUpload"]["type"])){
$target_dir = "fotosPerfil/".$idLimpio."/";
$carpeta=$target_dir;
if (!file_exists($carpeta)) {
    mkdir($carpeta, 0777, true);
}







//---------------------------------------------------------------------------------------------------------------
function compressImage($source, $destination, $quality) { 
    // Obtenemos la información de la imagen
    $imgInfo = getimagesize($source); 
    $mime = $imgInfo['mime']; 
     
    // Creamos una imagen
    switch($mime){ 
        case 'image/jpeg': 
            $image = imagecreatefromjpeg($source); 
            break; 
        case 'image/png': 
            $image = imagecreatefrompng($source); 
            break; 
        case 'image/gif': 
            $image = imagecreatefromgif($source); 
            break; 
        default: 
            $image = imagecreatefromjpeg($source); 
    } 
     
    // Guardamos la imagen
    imagejpeg($image, $destination, $quality); 
     
    // Devolvemos la imagen comprimida
    return $destination; 
} 

//-----------------------------------------------------------------------------------------------------------------
$target_file = $carpeta . basename($_FILES["fileToUpload"]["name"]);

$uploadOk = 1;




$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

//--------------------------------------------------------------------------------------------------------------


//--------------------------------------------------------------------------------------------------------------

// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
        $errors[]= "El archivo es una imagen - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        $errors[]= "El archivo no es una imagen.";
        $uploadOk = 0;
    }
}
// Check if file already exists
if (file_exists($target_file)) {
    $errors[]="Lo sentimos, archivo ya existe.";
    $uploadOk = 0;
}
// Check file size

if ($_FILES["fileToUpload"]["size"] > 6054234) {
    $errors[]= "Lo sentimos, el archivo es demasiado grande.  Tamaño máximo admitido: 6.05 MB";
    $uploadOk = 0;
}
// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" && $imageFileType != "JPG" && $imageFileType != "PNG"  
&& $imageFileType != "JPEG" && $imageFileType != "GIF" 
) {
    $errors[]= "Lo sentimos, sólo archivos JPG, JPEG, PNG, GIF  son permitidos.";
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    $errors[]= "Lo sentimos, tu archivo no fue subido.";
// if everything is ok, try to upload file
} else {
	$imageTemp = $_FILES["fileToUpload"]["tmp_name"]; 
    include_once 'includes/dbconexion.php';
//include_once 'includes/session.php';
mysql_query ("update usuarios set 	img_perfil =  '$target_file' where idusuario = '$idLimpio'",$conexion);
	$compressedImage = compressImage($imageTemp, $target_file, 25);
  //  
	  if ($compressedImage) {
		  
	 
	//if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $compressedImage)) {	
       $messages[]= "El Archivo ha sido subido correctamente.";
	   
	   
    } else {
       $errors[]= "Lo sentimos, hubo un error subiendo el archivo.";
    }
}

if (isset($errors)){
	?>
	<div class="alert alert-danger alert-dismissible" role="alert">
	  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	  <strong>Error!</strong> 
	  <?php
	  foreach ($errors as $error){
		  echo"<p>$error</p>";
	  }
	  ?>
	</div>
	<?php
}

if (isset($messages)){
	?>
	<div class="alert alert-success alert-dismissible" role="alert">
	  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	  <strong>Aviso!</strong> 
	  <?php
	  foreach ($messages as $message){
		  echo"<p>$message</p>";
	  }
	  ?>
	</div>
	<?php
}
}
?> 



<script>
function closeIt() {
    self.close();
}
</script>
<script>
//alert("Actualiza la pagina para tu foto");setTimeout(function() {closeIt();} ,2000);
</script>

<script>
window.opener.location.reload('editarPerfil.php?tipo=<?=$_GET["user"]?>&i=<?=$_GET["i"]?>');setTimeout(function() {closeIt();} ,3000);
</script>
