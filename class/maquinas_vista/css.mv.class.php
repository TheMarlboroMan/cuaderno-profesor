<?
class Plugin_vista_css extends Plugin_vista_maquina_web
{
	const DIR_CSS='css/';

	private $hojas_crudas=null;

	public function mostrar_plantillas() {return null;}
	public function obtener_tipo_vista() {return Plugin_vista_maquina_web::TIPO_VISTA_CSS;}

	//No hay forma de que falle. Todo está ok.
	public function &logica_vista($get, $post)
	{
		$this->hojas_crudas=isset($get['hojas']) ? $get['hojas'] : null;
		$resultado=new Resultado_logica_redireccion('defecto', 1);
		return $resultado;
	}

	public function generar_vista()
	{
		$buscar=array('#COLOR_PRINCIPAL#', 
			'#COLOR_FONDO#',
			'#COLOR_BTN_GENERICO#',
			'#COLOR_BTN_ELIMINAR#',
			'#COLOR_BTN_NUEVO#');
		$reemplazar=array(
			'#891812', 
			'#fff',
			'#6297AF',
			'#888', 
			'#891812');

		$resultado=null;
		$hojas=explode('/', $this->hojas_crudas);

		foreach($hojas as $clave => $valor)
		{
			$ruta=self::comprobar_ruta($valor);
			if($ruta)
			{
				$resultado.=str_replace($buscar, $reemplazar, file_get_contents($ruta));
			}
		}

		return $resultado;
	}

	public static function comprobar_ruta($valor)
	{
		$ruta=Constantes::RUTA_SERVER.self::DIR_CSS.$valor.'.css';
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
