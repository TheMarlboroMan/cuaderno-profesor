<?
class Horario_contenido extends Contenido_bbdd implements Propiedad_usuario, Horario
{
	const TABLA='cp_horarios_contenidos';
	const ID='id_entrada';

	const TIPO_COMUN=1;
	const TIPO_GRUPO=2;

	const ID_CONTENIDO_GUARDIA=1;
	const ID_CONTENIDO_RECESO=2;
	const ID_CONTENIDO_LIBRE=3;
	const ID_CONTENIDO_REUNION=4;
	const MAX_TIPO_COMUN=5;

	const LUNES=1;
	const MARTES=2;
	const MIERCOLES=3;
	const JUEVES=4;
	const VIERNES=5;

	public function NOMBRE_CLASE() {return 'Horario_contenido';}
	public function TABLA() {return self::TABLA;}
	public function ID() {return self::ID;}

	private static $diccionario=array(
		'id_entrada' => 'id_entrada',
		'fecha' => 'fecha',
		'hora' => 'hora',
		'id_usuario' => 'id_usuario',
		'id_curso' => 'id_curso',
		'dia' => 'dia',
		'posicion' => 'posicion',
		'tipo' => 'tipo',
		'id_contenido' => 'id_contenido'
	);

	protected $id_entrada=null;
	protected $fecha=null;
	protected $hora=null;
	protected $id_usuario=null;
	protected $id_curso=null;
	protected $dia=null;
	protected $posicion=null;
	protected $tipo=null;
	protected $id_contenido=null;

	public function acc_id_entrada() {return $this->id_entrada;}
	public function acc_fecha() {return $this->fecha;}
	public function acc_hora() {return $this->hora;}
	public function acc_id_usuario() {return $this->id_usuario;}
	public function acc_id_curso() {return $this->id_curso;}
	public function acc_dia() {return $this->dia;}
	public function acc_posicion() {return $this->posicion;}
	public function acc_tipo() {return $this->tipo;}
	public function acc_id_contenido() {return $this->id_contenido;}

	public function __construct(&$datos=null)
	{
		parent::__construct($datos, self::$diccionario);
	}

	public function crear(&$datos=null)
	{
		$resultado=parent::base_crear($datos, 'fecha, hora', 'CURDATE(), CURTIME()');
		return $resultado;
	}

	public function modificar(&$datos=null)
	{
		$resultado=parent::base_modificar($datos);
		return $resultado;
	}

	public function eliminar(&$datos=null)
	{
		$resultado=parent::base_eliminar_fisico($datos);
		return $resultado;
	}

	/**********************************************************************/

	public static function &obtener_array_para_curso(Curso &$c)
	{
		$sql=new Horario_contenido_sql();
		$texto=$sql->obtener_para_curso($c->ID_INSTANCIA());
		$ins=new Horario_contenido();
		$resultado=$ins->obtener_array_objetos($texto);
		return $resultado;
	}

	public function traducir()
	{
		$dg=new Grupo();

		switch($this->tipo)
		{
			case self::TIPO_COMUN:
				return self::traducir_tipo_comun($this->id_contenido);
			break;

			case self::TIPO_GRUPO:
				return Cache::obtener_de_cache($dg, $this->id_contenido)->acc_titulo();
			break;

			default: return '????'; break;
		}
	
	}

	public static function traducir_tipo_comun($t)
	{
		switch($t)
		{
			case self::ID_CONTENIDO_GUARDIA: return 'Guardia'; break;
			case self::ID_CONTENIDO_RECESO: return 'Receso'; break;
			case self::ID_CONTENIDO_LIBRE: return 'Libre'; break;
			case self::ID_CONTENIDO_REUNION: return 'Reuni&oacute;n'; break;
			default: return '????'; break;
		}
	}

	public function obtener_clase_color()
	{
		switch($this->tipo)
		{
			case self::TIPO_COMUN: return 'color_actividad_'.$this->id_contenido; break;
			case self::TIPO_GRUPO:
				$dg=new Grupo();
				$g=Cache::obtener_de_cache($dg, $this->id_contenido);
				return 'color_grupo_'.$g->acc_color_grupo();
			break;

			default: return null; break;
		}
	}

	public function obtener_url()
	{
		switch($this->tipo)
		{
			case self::TIPO_COMUN: return null; break;
			case self::TIPO_GRUPO:
				$dg=new Grupo();
				$g=Cache::obtener_de_cache($dg, $this->id_contenido);
				return Factoria_urls::vista_grupo($g);
			break;

			default: return null; break;
		}
	}

	/**********************************************************************/

	public function pertenece_a_y_es_valido(Usuario &$usuario)
	{
		return $this->id_entrada && $this->id_usuario==$usuario->ID_INSTANCIA();
	}

	/**********************************************************************/

	public function &obtener_array(Curso &$c)
	{
		$resultado=$this->obtener_array_para_curso($c);
		return $resultado;
	}

	public function obtener_posicion_horario() {return $this->posicion;}
	public function eliminar_horario() {$this->eliminar();}

	public function cambiar_posicion_horario($p, $total)
	{
		$pos=($p % $total)+1;
		$datos_modificar=array('posicion' => $pos);
		$this->modificar($datos_modificar);
	}

	public function crear_nueva_entrada_horario(Curso &$c)
	{
		$dia=self::LUNES;
		
		while($dia <= self::VIERNES)
		{
			$datos_crear=array(
			'id_usuario' => $c->acc_id_usuario(),
			'id_curso' => $c->ID_INSTANCIA(),
			'dia' => $dia,
			'posicion' => 255,
			'tipo' => self::TIPO_COMUN,
			'id_contenido' => self::ID_CONTENIDO_LIBRE);

			$temp=new Horario_contenido();
			$temp->crear($datos_crear);
			++$dia;
		}
	}
}
?>
