<?
require("configuracion/constantes.class.php");
require(Constantes::RUTA_SERVER."inc_motor/clases_motor.inc.php");
require(Constantes::RUTA_SERVER."inc_logica/clases_app.inc.php");
Consulta_mysql::conectar(Constantes::BBDD_HOST, Constantes::BBDD_USER, Constantes::BBDD_PASS, Constantes::BBDD_BASE_DATOS);
require(Constantes::RUTA_SERVER."inc_logica/comunes.inc.php");

/*
TODO TODO TODO TODO TODO

>> Revisar los QSA del .htaccess y ver lo que podemos hacer con ellos.

>> Revisar los métodos de URL y la conveniencia de que exista aún el parseo de URL en la máquina.

>> Los números de los informes no están bien.

>> Estadísticas x clase

>> Casilla nota final

>> Ayuda en cada pantalla. Guardar en perfil si muestra o no la ayuda. Guardar en cookie tb. Combinar esto.

>> Pantalla perfil, guardar cosas como "al iniciar sesión ir a pantalla de ...".
*/

//El tipo de máquina lo dictamina el htaccess usando alguna palabra clave en get.
$MAQUINA_WEB=new Maquina_web($_GET, $_POST, $_FILES, $sesion);
$MAQUINA_WEB->validar_usuario();

switch($MAQUINA_WEB->acc_estado_maquina())
{
	case 'logica':
		$plugin_logica=&Factoria_plugins::generar_logica_para_maquina($MAQUINA_WEB);
		if(!$plugin_logica instanceof Plugin_logica_maquina_web) die('ERROR: No se ha generado logica');
		$MAQUINA_WEB->recibir_plugin_logica($plugin_logica);		
		die();
	break;

	case 'vista':
		$plugin_vista=&Factoria_plugins::generar_vista_para_maquina($MAQUINA_WEB);
		if(!$plugin_vista instanceof Plugin_vista_maquina_web) die('ERROR: No se ha generado vista');
		$MAQUINA_WEB->recibir_plugin_vista($plugin_vista);
		$MAQUINA_WEB->mostrar_documento();
		die();
	break;
}
?>
