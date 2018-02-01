<?
class Plugin_logica_logout extends Plugin_logica_maquina_web
{
	public function preparar($get, $post, $files) {}
	
	public function &logica_control($modo, $get, $post, $files)
	{
		$clave_estado_logica=Maquina_web::CLAVE_ESTADO_LOGICA;

		switch($modo)
		{
			case 'logout':
				$this->acc_maquina_web()->eliminar_usuario_sesion();
				$resultado=new Resultado_logica_redireccion($modo, 1);
			break;

			default: $resultado=new Resultado_logica_redireccion('logout', 0); break;
		}

		return $resultado;
	}

	public function componer_url(Resultado_logica_redireccion &$l)
	{
		return Factoria_urls::vista_cursos(); //Si el logout ha salido bien, ya te mandará fuera.
	}
};
?>
