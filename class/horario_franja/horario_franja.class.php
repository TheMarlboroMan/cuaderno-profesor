<?
class Horario_franja extends Contenido_bbdd implements Propiedad_usuario, Horario
{
	const TABLA='cp_horarios_franjas';
	const ID='id_entrada';

	public function NOMBRE_CLASE() {return 'Horario_franja';}
	public function TABLA() {return self::TABLA;}
	public function ID() {return self::ID;}

	private static $diccionario=array(
		'id_entrada' => 'id_entrada',
		'fecha' => 'fecha',
		'hora' => 'hora',
		'id_usuario' => 'id_usuario',
		'id_curso' => 'id_curso',
		'posicion' => 'posicion',
		'titulo' => 'titulo'
	);

	protected $id_entrada=null;
	protected $fecha=null;
	protected $hora=null;
	protected $id_usuario=null;
	protected $id_curso=null;
	protected $posicion=null;
	protected $titulo=null;

	public function acc_id_entrada() {return $this->id_entrada;}
	public function acc_fecha() {return $this->fecha;}
	public function acc_hora() {return $this->hora;}
	public function acc_id_usuario() {return $this->id_usuario;}
	public function acc_id_curso() {return $this->id_curso;}
	public function acc_posicion() {return $this->posicion;}
	public function acc_titulo() {return $this->titulo;}

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
		$sql=new Horario_franja_sql();
		$texto=$sql->obtener_para_curso($c->ID_INSTANCIA());
		$ins=new Horario_franja();
		$resultado=$ins->obtener_array_objetos($texto);
		return $resultado;
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
		$datos_modificar=array('posicion' => $p);
		$this->modificar($datos_modificar);
	}

	public function crear_nueva_entrada_horario(Curso &$c)
	{
		$datos_crear=array(
		'id_usuario' => $c->acc_id_usuario(),
		'id_curso' => $c->ID_INSTANCIA(),
		'posicion' => 255,
		'titulo' => '???');

		$temp=new Horario_franja();
		$temp->crear($datos_crear);
	}
}
?>
