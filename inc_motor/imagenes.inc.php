<?
include("../class_motor/herramientas_img.class.php");

$fuente=$_GET['src'];
$ancho=$_GET['w'];
$alto=$_GET['h'];

switch($_GET['modo'])
{
	//Reescalar una imagen en la que w es el ancho máximo deseado y h es la altura máxima deseada.
	case 'reescalar':
		$resultado=Herramientas_img::escalar_imagen_proporcion($fuente, $ancho, $alto, false, true, true, false);
	break;

	//Reescalar una imagen en la que w es el ancho máximo deseado y h es la altura máxima deseada.
	case 'reescalar_minimo':
		$resultado=Herramientas_img::escalar_imagen_proporcion($fuente, $ancho, $alto, false, true, true, true);
	break;

	case 'reescalar_recortar':
		$resultado=Herramientas_img::escalar_imagen_recortar($fuente, $ancho, $alto, null, true);
	break;
}
die($resultado);
?>
