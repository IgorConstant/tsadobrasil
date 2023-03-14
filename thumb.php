<?php

// recebendo a url da imagem
$filename = $_GET['img'];
$largura = (int)$_GET['w'];
$altura = (int)$_GET['h'];


$tipo = explode(".", $filename);

// Cabe�alho que ira definir a saida da pagina

if(end($tipo) == "gif"){
	header('Content-type: image/gif');
}elseif(end($tipo) == "png"){
	header('Content-type: image/png');
}else{
	header('Content-type: image/jpeg');
}


// pegando as dimensoes reais da imagem, largura e altura

list($width, $height) = getimagesize($filename);



//setando a largura da miniatura

$new_width = $largura;

//setando a altura da miniatura

$new_height = $altura;



//gerando a a miniatura da imagem

$image_p = imagecreatetruecolor($new_width, $new_height);

if(end($tipo) == "gif"){
	$image = imagecreatefromgif($filename);
}elseif(end($tipo) == "png"){
	$image = imagecreatefrompng($filename);
	imagealphablending($image_p, false);
	imagesavealpha($image_p,true);
}else{
	$image = imagecreatefromjpeg($filename);
}


imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);



//o 3� argumento � a qualidade da miniatura de 0 a 100

if(end($tipo) == "gif"){
imagegif($image_p, null, 90);
}elseif(end($tipo) == "png"){
imagepng($image_p, null, 9);
}else{
imagejpeg($image_p, null, 90);
}

imagedestroy($image_p);

?>

