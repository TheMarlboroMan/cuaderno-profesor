<?
/*Una máquina para hacer funcionar la web de forma estandarizada en lugar de
tener un montón de scripts para cada cosa. Funciona con dos clases base de
plugin, que haríamos específicas para cada sección que vaya a usar la máquina.*/

abstract class Plugin_maquina_web
{
	private $maquina_web;
	protected function &acc_maquina_web() {return $this->maquina_web;}
	public function imbuir(Maquina_web &$m){$this->maquina_web=&$m;}
	public function componer_url(Resultado_logica_redireccion &$r) {return null;}	//Componer una url según el Resultado_logica. Por defecto?. Nada.
	public final function componer_xml(Resultado_logica_xml &$r) {return Herramientas::respuesta_xml($r->acc_resultado(), $r->acc_mensaje(), $r->acc_datos(), $r->acc_att_resultado(), $r->acc_att_mensaje(), $r->acc_att_datos());}
}

abstract class Plugin_logica_maquina_web extends Plugin_maquina_web
{
	public abstract function preparar($get, $post, $files);	//Debe dejar el objeto en estado usable para la logica, como cargando propiedades y tal.
	public abstract function &logica_control($modo, $get, $post, $files);	//Debe devolver Resultado_logica.
};

abstract class Plugin_vista_maquina_web extends Plugin_maquina_web
{
	const TIPO_VISTA_HTML=0;
	const TIPO_VISTA_CSS=1;
	const TIPO_VISTA_XML=2;
	const TIPO_VISTA_JS=3;

	public abstract function &logica_vista($get, $post); //Debe devolver Resultado_logica.
	public function generar_title() {return null;}
	public function obtener_array_css() {return null;} //Un array de rutas de css.
	public function obtener_array_js() {return null;} //Un array de rutas de css.
	public function mostrar_herramientas() {return null;}

	//TODO TODO TODO
//	public abstract function &generar_mensajes(array $mensajes); 
	//TODO TODO TODO
	public abstract function generar_vista(); //Debe devolver el html.
	public abstract function mostrar_plantillas(); //Devolvería las plantillas de cosas que no son visibles.

	public abstract function obtener_tipo_vista(); //Según los tipos de vista HTML, CSS, XML... Para las cabeceras.
};

abstract class Resultado_logica
{
	private $resultado=0;
	private $accion=null;

	public function __construct($a, $r)
	{
		$this->accion=$a;
		$this->resultado=$r;
	}

	public function acc_resultado() {return $this->resultado;}
	public function acc_accion() {return $this->accion;}
}

class Resultado_logica_redireccion extends Resultado_logica
{
	public function __construct($a, $r)
	{
		parent::__construct($a, $r);
	}
};

class Resultado_logica_xml extends Resultado_logica
{
	private $mensaje;
	private $datos;
	private $att_resultado;
	private $att_mensaje;
	private $att_datos;

	public function __construct($a, $r, $m=null, $d=null, $ar=null, $am=null, $ad=null)
	{
		parent::__construct($a, $r);
		$this->mensaje=$m;
		$this->datos=$d;
		$this->att_resultado='accion="'.$this->acc_accion().'" '.$ar;
		$this->att_mensaje=$am;
		$this->att_datos=$ad;
	}

	public function acc_mensaje() {return $this->mensaje;}
	public function acc_datos() {return $this->datos;}
	public function acc_att_resultado() {return $this->att_resultado;}
	public function acc_att_mensaje() {return $this->att_mensaje;}
	public function acc_att_datos() {return $this->att_datos;}

	public function mut_datos($v) {$this->datos=$v;}
	public function mut_att_datos($v) {$this->att_datos=$v;}
}

class Maquina_web
{
	const ESTADO_VISTA='vista';
	const ESTADO_LOGICA='logica';
	const CLAVE_ESTADO_LOGICA='mwlogica';
	const CLAVE_MODO_MAQUINA='modomw';
	const CLAVE_MENSAJES='resmw';
	const MODO_LOGIN='login';
	const MODO_CSS='css';
	const MODO_JS='JS';

	const COMPRIMIR_JS=false;

	const TIPO_RUTA_JS=0;
	const TIPO_RUTA_CSS=1;

	const DIR_VISTA='vistas/';

	private $usuario=null;

	private $tipo_maquina=null;
	private $estado_maquina=null;
	
	private $get;
	private $post;
	private $files;
	private $sesion;

	public function acc_tipo_maquina() {return $this->tipo_maquina;}
	public function acc_estado_maquina() {return $this->estado_maquina;}
	public function &obtener_usuario() {return $this->usuario;}

	public function __construct($g, $p, $f, &$s)
	{
		$this->get=$g;
		$this->post=$p;
		$this->files=$f;
		$this->sesion=&$s;

		$id_usuario=isset($this->sesion['id_usuario']) ? $this->sesion['id_usuario'] : 0;
		$this->usuario=new Usuario($id_usuario);

		$this->tipo_maquina=isset($this->get[self::CLAVE_MODO_MAQUINA]) ? $this->get[self::CLAVE_MODO_MAQUINA] : null;

		if(!$this->tipo_maquina) 
		{
//			die('ERROR: Deteniendo maquina: no se detecta tipo');
			header('location: '.Constantes::URL_WEB.'portal.html');
			die();
		}

		if(isset($this->get[self::CLAVE_ESTADO_LOGICA]) || isset($this->post[self::CLAVE_ESTADO_LOGICA]))
		{
			$this->estado_maquina=self::ESTADO_LOGICA;
		}
		else
		{
			$this->estado_maquina=self::ESTADO_VISTA;
		}
	}

	/**********************************************************************/

	public function recibir_plugin_vista(Plugin_vista_maquina_web &$p)
	{
		$p->imbuir($this);
		$resultado=$p->logica_vista($this->get, $this->post);

		if(!$resultado instanceof Resultado_logica)
		{
			die('ERROR: El resultado de la logica de vista debe ser del tipo Resultado_logica');
		}
		else
		{
			if(!$resultado->acc_resultado()) //Acceso denegado!;
			{
				$url=$p->componer_url($resultado);
				$this->procesar_url_resultado($url);
			}
			else
			{
				//Separar las distintas cabeceras de vista que
				//se pueden generar. Siempre para vista, no como
				//posible resultado de una lógica.
				switch($p->obtener_tipo_vista())
				{
					case Plugin_vista_maquina_web::TIPO_VISTA_HTML:
						$URL_WEB=Constantes::URL_WEB;

						$RUTA_CSS=$this->generar_ruta_compuesta($p->obtener_array_css(), self::TIPO_RUTA_CSS);
						$VER_CSS=strlen($RUTA_CSS) ? '<link rel="stylesheet" type="text/css" href="'.$RUTA_CSS.'" media="screen">' : null;

						if(self::COMPRIMIR_JS)
						{
							$RUTA_JS=$this->generar_ruta_compuesta($p->obtener_array_js(), self::TIPO_RUTA_JS);						
							$VER_JS=strlen($RUTA_JS) ? '<script type="text/javascript" src="'.$RUTA_JS.'"></script>' : null;
						}
						else
						{
							$js=$p->obtener_array_js();
							$VER_JS=null;
							if(is_array($js)) foreach($js as $clave => $valor)
								$VER_JS.='<script type="text/javascript" src="'.Constantes::URL_WEB.'js/'.$valor.'.js"></script>';
	
						}

						$TITLE=$p->generar_title();
						$CONTENIDO_WEB=$p->generar_vista();
						$BIENVENIDA=$this->mostrar_bienvenida();
						$HERRAMIENTAS=$p->mostrar_herramientas();
						$PLANTILLAS=$p->mostrar_plantillas();

						$documento_final=include(Constantes::RUTA_SERVER.self::DIR_VISTA.'vista_web.vista.php');
						header('Content-type: text/html; charset=iso-8859-1');
						die($documento_final);						
					break;

					case Plugin_vista_maquina_web::TIPO_VISTA_CSS:
						header('Content-type: text/css; charset=iso-8859-1');
						die($p->generar_vista());
					break;

					case Plugin_vista_maquina_web::TIPO_VISTA_XML:
						header('Content-type: text/xml; charset=iso-8859-1');
						die($p->generar_vista());
					break;

					case Plugin_vista_maquina_web::TIPO_VISTA_JS:
						header('Content-type: text/javascript; charset=iso-8859-1');
						die($p->generar_vista());
					break;
			
					default:
						die('ERROR: Maquina_web no puede determinar el tipo de vista');
					break;
				}
			}
		}
	}

	/**********************************************************************/

	public function recibir_plugin_logica(Plugin_logica_maquina_web &$p)
	{
		$p->imbuir($this);
		$p->preparar($this->get, $this->post, $this->files);

		$modo=isset($this->get[self::CLAVE_ESTADO_LOGICA]) ? $this->get[self::CLAVE_ESTADO_LOGICA] : 
			(isset($this->post[self::CLAVE_ESTADO_LOGICA]) ? $this->post[self::CLAVE_ESTADO_LOGICA] : null);

		$resultado=$p->logica_control($modo, $this->get, $this->post, $this->files);

		if(!$resultado instanceof Resultado_logica)
		{
			die('ERROR: El resultado de la logica de control debe ser del tipo Resultado_logica');
		}
		else
		{
			$this->procesar_resultado($resultado, $p);
		}		
	}

	//Esto es un laberinto absurdo... Los plugins han generado el resultado y luego llamamos a sus métodos otra vez.
	private function procesar_resultado(Resultado_logica &$resultado, Plugin_maquina_web &$p)
	{
		$clase=get_class($resultado);		
		switch($clase)
		{
			case 'Resultado_logica_redireccion': 
				$url=$p->componer_url($resultado);
				$this->procesar_url_resultado($url); 
			break;
			case 'Resultado_logica_xml': $this->procesar_xml_resultado($resultado, $p); break;
			default: die('ERROR: Maquina_web no puede determinar tipo de resultado'); break;
		}
	}

	private function procesar_xml_resultado(Resultado_logica_xml &$resultado, Plugin_maquina_web &$p)
	{
		header('Content-type: text/xml; charset=iso-8859-1');
		die($p->componer_xml($resultado));
	}

	private function procesar_url_resultado($url) //Resultado_logica_redireccion &$resultado, Plugin_maquina_web &$p)
	{
//		$url=$p->componer_url($resultado);
		header('location: '.$url);
		die();
	}

	/**********************************************************************/

	//Lee los archivos SIEMPRE del directorio CSS, nunca de otro sitio.
	//Nunca se dice la extensión, se asume que es css.
	//Sería algo como css href="obtener_css/general/listado" donde los
	//archivos son "general.css" y "listado.css". Magia del htaccess, por
	//otro lado. No se permiten puntos ni barras en las rutas. También para
	//JS.

	public function generar_ruta_compuesta($array_rutas, $tipo)
	{
		if(!is_array($array_rutas))
		{
			return null;
		}
		else
		{
			$tiempo=0;
			$validos=array();
		
			foreach($array_rutas as $clave => $valor)
			{		
				switch($tipo)
				{
					case self::TIPO_RUTA_CSS: $ruta=Plugin_vista_css::comprobar_ruta($valor); break;
					case self::TIPO_RUTA_JS: $ruta=Plugin_vista_js::comprobar_ruta($valor); break;
					default: break;
				}

				if($ruta)
				{
					$t=filemtime($ruta);
					if($t > $tiempo) $tiempo=$t;

					$validos[]=$valor;
				}
			}

			if(!count($validos))
			{
				return null;
			}
			else
			{
				switch($tipo)
				{
					case self::TIPO_RUTA_CSS: $directorio='obtener_css'; break;
					case self::TIPO_RUTA_JS: $directorio='obtener_js'; break;
					default: return null; break;
				}
	
				return Constantes::URL_WEB.$directorio.'/'.implode('/', $validos).'?t='.$tiempo;
			}
		}
	}

	/**********************************************************************/

	public function validar_usuario()
	{
		$resultado=new Resultado_logica_redireccion('validar_usuario', $this->validar_acceso_usuario());

		//Los modos de login y css no necesitan validación.
		if(!$resultado->acc_resultado() && $this->tipo_maquina!=self::MODO_LOGIN && $this->tipo_maquina!=self::MODO_CSS && $this->tipo_maquina!=self::MODO_JS)
		{
			$url=Factoria_urls::vista_login();
			$this->procesar_url_resultado($url);
		}
	}

	private function validar_acceso_usuario()
	{
		return $this->usuario && $this->usuario->es_logueable();		
	}

	public function guardar_usuario_sesion(Usuario &$u)
	{
		$this->sesion['id_usuario']=$u->ID_INSTANCIA();
	}

	public function eliminar_usuario_sesion()
	{
		$this->sesion['id_usuario']=0;
	}

	private function mostrar_bienvenida()
	{
		if($this->validar_acceso_usuario())
		{
			$url_logout=Factoria_urls::vista_logout();

			return <<<R
Hola {$this->usuario->acc_nombre_completo()} <span class="logout"><a href="{$url_logout}">Logout</a></span>
R;
		}
		else
		{
			return null;
		}
	}
};
?>
