<?
class Plugin_vista_js extends Plugin_vista_maquina_web
{
	const DIR_JS='js/';

	private $rutas_crudas=null;

	public function mostrar_plantillas() {return null;}
	public function obtener_tipo_vista() {return Plugin_vista_maquina_web::TIPO_VISTA_JS;}

	//No hay forma de que falle. Todo está ok.
	public function &logica_vista($get, $post)
	{
		$this->rutas_crudas=isset($get['scripts']) ? $get['scripts'] : null;
		$resultado=new Resultado_logica_redireccion('defecto', 1);
		return $resultado;
	}

	public function generar_vista()
	{
		$resultado=null;
		$scripts=explode('/', $this->rutas_crudas);

		foreach($scripts as $clave => $valor)
		{
			$ruta=self::comprobar_ruta($valor);
			if($ruta)
			{
				$resultado.=file_get_contents($ruta);
			}
		}

		return $resultado;
	}

	public static function comprobar_ruta($valor)
	{
		$ruta=Constantes::RUTA_SERVER.self::DIR_JS.$valor.'.js';

		if(file_exists($ruta) && is_file($ruta) && strpos($valor, '.')===false && strpos($valor, '/')===false)
		{
			return $ruta;
		}
		else
		{
			return null;
		}	
	}
};
?>
