<?
class Alumno extends Contenido_bbdd implements Cacheable, Propiedad_usuario
{
	const TABLA='cp_alumnos';
	const ID='id_alumno';

	public function NOMBRE_CLASE() {return 'Alumno';}
	public function TABLA() {return self::TABLA;}
	public function ID() {return self::ID;}

	private static $diccionario=array(
		'id_alumno' => 'id_alumno',
		'fecha' => 'fecha',
		'hora' => 'hora',
		'id_usuario' => 'id_usuario',
		'id_grupo' => 'id_grupo',
		'nombre' => 'nombre',
		'apellidos' => 'apellidos',
		'texto' => 'texto',
		'borrado_logico' => 'borrado_logico'
	);

	protected $id_alumno=null;
	protected $fecha=null;
	protected $hora=null;
	protected $id_usuario=null;
	protected $id_grupo=null;
	protected $nombre=null;
	protected $apellidos=null;
	protected $texto=null;
	protected $borrado_logico=null;

	public function acc_id_alumno() {return $this->id_alumno;}
	public function acc_fecha() {return $this->fecha;}
	public function acc_hora() {return $this->hora;}
	public function acc_id_usuario() {return $this->id_usuario;}
	public function acc_id_grupo() {return $this->id_grupo;}	
	public function acc_nombre() {return $this->nombre;}
	public function acc_apellidos() {return $this->apellidos;}
	public function acc_texto() {return $this->texto;}
	public function es_borrado_logico() {return $this->borrado_logico;}

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
		$resultado=parent::base_eliminar($datos);

		if($resultado)
		{
			$entradas=Dato_evaluacion_alumno::obtener_para_alumno($this);
			foreach($entradas as $clave => &$valor) $valor->eliminar();
		}

		return $resultado;
	}

	/**********************************************************************/

	public static function &obtener_para_grupo(Grupo &$g)
	{
		$sql=new Alumno_sql();
		$texto=$sql->obtener_para_grupo($g->ID_INSTANCIA());
		$ins=new Alumno();
		$resultado=$ins->obtener_array_objetos($texto);
		return $resultado;
	}

	/**********************************************************************/

	public function pertenece_a_y_es_valido(Usuario &$usuario)
	{
		return $this->id_alumno && !$this->borrado_logico && $this->id_usuario==$usuario->ID_INSTANCIA();
	}

	/**********************************************************************/

	public function &FACTORIA_CACHE($id) 
	{
		$resultado=new Alumno($id);
		return $resultado;
	}
	public function INDICE_CACHE() {return 'alumno';}
}
?>
