<?
class Plugin_logica_login extends Plugin_logica_maquina_web
{
	public function preparar($get, $post, $files) {}
	
	public function &logica_control($modo, $get, $post, $files)
	{
		$clave_estado_logica=Maquina_web::CLAVE_ESTADO_LOGICA;

		switch($modo)
		{
			case 'acceso':
				$login=isset($post['login']) ? $post['login'] : null;
				$pass=isset($post['pass']) ? $post['pass'] : null;

				try
				{
					$usuario=Usuario::login($login, $pass);
					if($usuario) 
					{
						$this->acc_maquina_web()->guardar_usuario_sesion($usuario);
						$resultado=new Resultado_logica_redireccion($modo, 1);
					}
					else
					{
						$resultado=new Resultado_logica_redireccion($modo, 0);
					}
				}
				catch(Excepcion_consulta_mysql $e)
				{
					$resultado=new Resultado_logica_redireccion($modo, 0);
				}

			break;

			default: $resultado=new Resultado_logica_redireccion('acceso', 0); break;
		}

		return $resultado;
	}

	public function componer_url(Resultado_logica_redireccion &$l)
	{
		$accion=$l->acc_accion();
		$resultado=$l->acc_resultado();

		switch($accion)
		{
			case 'acceso': return Factoria_urls::resultado_acceso($resultado); break;
			default: return Factoria_urls::vista_login(); break;
		}
	}
};
?>
