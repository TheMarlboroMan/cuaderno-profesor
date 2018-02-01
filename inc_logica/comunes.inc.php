<?
session_start();

if(!isset($_SESSION[Constantes::SITIO_SESION][Constantes::SECCION_SESION_WEB])) $_SESSION[Constantes::SITIO_SESION][Constantes::SECCION_SESION_WEB]=array();
$sesion=&$_SESSION[Constantes::SITIO_SESION][Constantes::SECCION_SESION_WEB];

//if(isset($sesion['idioma'])) $session['idioma']=null;
//$idiomas_comunes=array('comunes');
//$IDIOMA=new Idioma($sesion['idioma'], 'en');
?>
