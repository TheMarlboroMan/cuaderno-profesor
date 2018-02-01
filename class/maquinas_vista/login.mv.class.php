<?
class Plugin_vista_login extends Plugin_vista_maquina_web
{
	private $usuario;

	public function mostrar_plantillas() {return null;}
	public function obtener_tipo_vista() {return Plugin_vista_maquina_web::TIPO_VISTA_HTML;}

	public function obtener_array_css()
	{
		$resultado=array('comunes', 'login');
		return $resultado;
	}

	public function &logica_vista($get, $post)
	{
		$this->usuario=&$this->acc_maquina_web()->obtener_usuario();
		$resultado=new Resultado_logica_redireccion('login', !$this->usuario->es_logueable());
		return $resultado;
	}

	public function componer_url(Resultado_logica_redireccion &$l)
	{
		$accion=$l->acc_accion();

		switch($accion)
		{
			case 'login': 
			default:
				return Factoria_urls::vista_cursos(); 	//Acceso ilegal a la pantalla (acceso a login cuando estás logueado).
			break;
		}
	}

	public function generar_vista()
	{
		$clave_estado_logica=Maquina_web::CLAVE_ESTADO_LOGICA;

		return <<<R
<div class="login">
	<h1>Acceso a la aplicaci&oacute;n</h1>

	<form method="post" action="">
		<input type="hidden" name="{$clave_estado_logica}" value="acceso" />
		<p>Usuario: <input type="text" name="login" value="" /></p>
		<p>Clave: <input type="password" name="pass" value="" /></p>
		<p><input type="submit" value="Acceder" /></p>
	</form>
</div>
R;
	}

	public function generar_title(){return "Acceso aplicaci&oacute;n";}
};
?>
