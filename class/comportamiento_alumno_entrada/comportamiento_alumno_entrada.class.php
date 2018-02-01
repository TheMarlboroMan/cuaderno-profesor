<?
class Comportamiento_alumno_entrada extends Contenido_bbdd
{
	const TABLA='cp_alumnos_comportamiento_entradas';
	const ID='id_entrada';

	public function NOMBRE_CLASE() {return 'Comportamiento_alumno_entrada';}
	public function TABLA() {return self::TABLA;}
	public function ID() {return self::ID;}

	private static $diccionario=array(
		'id_entrada' => 'id_entrada',
		'id_comportamiento' => 'id_comportamiento',
		'fecha' => 'fecha',
		'hora' => 'hora',
		'valor' => 'valor',
		'valor_original' => 'valor_original',
		'texto' => 'texto',
		'borrado_logico' => 'borrado_logico'
	);

	protected $id_entrada=null;
	protected $id_comportamiento=null;
	protected $fecha=null;
	protected $hora=null;
	protected $valor=null;
	protected $valor_original=null;
	protected $texto=null;
	protected $borrado_logico=null;

	public function acc_id_entrada() {return $this->id_entrada;}
	public function acc_id_comportamiento() {return $this->id_comportamiento;}
	public function acc_fecha() {return $this->fecha;}
	public function acc_hora() {return $this->hora;}
	public function acc_valor() {return $this->valor;}
	public function acc_valor_original() {return $this->valor_original;}
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
		return $resultado;
	}
}
?>
