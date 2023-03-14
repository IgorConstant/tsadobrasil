<?php 
include_once ('class-mb/Resize.Class.php');

function anti_sql2($sql){
	$sql = preg_replace(sql_regcase("/(from|select|insert|delete|where|drop table|show tables|#|\*|---|\\\\)/"), "" ,$sql);
	$sql = trim($sql);
	$sql = strip_tags($sql);
	$sql = (get_magic_quotes_gpc()) ? $sql : addslashes($sql);
	return $sql;
}


// recebendo a url da imagem
$filename = $_GET['img'];
$w = $_GET['w'];
$h = $_GET['h'];




$tipo = @end(explode(".", $filename));


// Cabe�alho que ira definir a saida da pagina
if($tipo == "gif"){
	header('Content-type: image/gif');
}elseif($tipo == "png"){
	header('Content-type: image/png');
}else{
	header('Content-type: image/jpeg');
}


$resizer = new Resize($filename);
$resizer->resizeImage($w, $h,'crop');
$resizer->saveImage('output.jpg', 100);
