<?
class Factoria_plugins
{
	public static function &generar_logica_para_maquina(Maquina_web& $m)
	{
		$resultado=null;

		switch($m->acc_tipo_maquina())
		{
			case 'curso': $resultado=new Plugin_logica_curso(); break;
			case 'grupo': $resultado=new Plugin_logica_grupo(); break;
			case 'tabla': $resultado=new Plugin_logica_tabla(); break;
			case 'login': $resultado=new Plugin_logica_login(); break;
			case 'logout': $resultado=new Plugin_logica_logout(); break;
			case 'horario': $resultado=new Plugin_logica_horario(); break;
			case 'final': $resultado=new Plugin_logica_final(); break;
		}

		return $resultado;
	}

	public static function &generar_vista_para_maquina(Maquina_web& $m)
	{
		$resultado=null;

		switch($m->acc_tipo_maquina())
		{
			case 'curso': $resultado=new Plugin_vista_curso(); break;
			case 'grupo': $resultado=new Plugin_vista_grupo(); break;
			case 'tabla': $resultado=new Plugin_vista_tabla(); break;
			case 'login': $resultado=new Plugin_vista_login(); break;
			case 'css': $resultado=new Plugin_vista_css(); break;
			case 'js': $resultado=new Plugin_vista_js(); break;
			case 'horario': $resultado=new Plugin_vista_horario(); break;
			case 'final': $resultado=new Plugin_vista_final(); break;
		}

		return $resultado;
	}	
}
?>
