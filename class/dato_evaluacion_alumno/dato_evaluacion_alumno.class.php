<?
class Dato_evaluacion_alumno extends Contenido_bbdd implements Cacheable
{
	const TABLA='cp_datos_evaluacion_alumnos';
	const ID='id_entrada';

	public function NOMBRE_CLASE() {return 'Dato_evaluacion_alumno';}
	public function TABLA() {return self::TABLA;}
	public function ID() {return self::ID;}

	public function &FACTORIA_CACHE($id) 
	{
		$resultado=new Dato_evaluacion_alumno($id);
		return $resultado;
	}
	public function INDICE_CACHE() {return 'dato_evaluacion_alumno';}

	private static $diccionario=array(
		'id_entrada' => 'id_entrada',
		'fecha' => 'fecha',
		'hora' => 'hora',
		'id_item_evaluable' => 'id_item_evaluable',
		'id_alumno' => 'id_alumno',
		'id_usuario' => 'id_usuario',
		'valor' => 'valor',
		'borrado_logico' => 'borrado_logico'
	);

	protected $id_entrada=null;
	protected $fecha=null;
	protected $hora=null;
	protected $id_item_evaluable=null;
	protected $id_alumno=null;
	protected $id_usuario=null;
	protected $valor=null;
	protected $borrado_logico=null;

	public function acc_id_entrada() {return $this->id_entrada;}
	public function acc_fecha() {return $this->fecha;}
	public function acc_hora() {return $this->hora;}
	public function acc_id_item_evaluable() {return $this->id_item_evaluable;}
	public function acc_id_alumno() {return $this->id_alumno;}
	public function acc_id_usuario() {return $this->id_usuario;}
	public function acc_valor() {return $this->valor;}
	public function es_borrado_logico() {return $this->borrado_logico;}

	public function __construct(&$datos=null)
	{
		parent::__construct($datos, self::$diccionario);
	}

	public function crear(&$datos=null)
	{
		$datos=self::transformar_datos($datos);
		$resultado=parent::base_crear($datos, 'fecha, hora', 'CURDATE(), CURTIME()');
		return $resultado;
	}

	public function modificar(&$datos=null)
	{
		$datos=self::transformar_datos($datos);
		$resultado=parent::base_modificar($datos);
		return $resultado;
	}

	public function eliminar(&$datos=null)
	{
		$resultado=parent::base_eliminar($datos);
		return $resultado;
	}

	/**********************************************************************/

	private static function transformar_datos($datos)
	{
		if(isset($datos['valor'])) $datos['valor']=str_replace(',', '.', $datos['valor']);
		return $datos;
	}

	/**********************************************************************/

	public static function &obtener(Usuario &$u, Alumno &$a, Item_evaluable &$i)
	{
		$sql=new Dato_evaluacion_alumno_sql();
		$ins=new Dato_evaluacion_alumno();
		$texto=$sql->obtener($u->ID_INSTANCIA(), $a->ID_INSTANCIA(), $i->ID_INSTANCIA());
		$resultado=$ins->obtener_objeto_por_texto($texto);
		return $resultado;
	}

	public static function &obtener_para_item_evaluable(Item_evaluable &$i)
	{
		$sql=new Dato_evaluacion_alumno_sql();
		$ins=new Dato_evaluacion_alumno();
		$texto=$sql->obtener_para_item_evaluable($i->ID_INSTANCIA());
		$resultado=$ins->obtener_array_objetos($texto);
		return $resultado;
	}

	public static function &obtener_para_alumno(Alumno &$a)
	{
		$sql=new Dato_evaluacion_alumno_sql();
		$ins=new Dato_evaluacion_alumno();
		$texto=$sql->obtener_para_alumno($a->ID_INSTANCIA());
		$resultado=$ins->obtener_array_objetos($texto);
		return $resultado;
	}

	public function obtener_cuenta_para_item_evaluable(Item_evaluable &$e)
	{
		$sql=new Dato_evaluacion_alumno_sql();
		try
		{
			$texto=$sql->obtener_cuenta_para_item_evaluable($e->ID_INSTANCIA());
			$consulta=new Consulta_mysql();
			$consulta->texto($texto);
			$consulta->consultar();
			$consulta->leer();
			return $consulta->resultados('total');
		}
		catch(Excepcion_consulta_mysql $e)
		{
			return 0;
		}
	}

	/**********************************************************************/

	public function pertenece_a_y_es_valido(Usuario &$usuario)
	{
		return $this->id_entrada && !$this->borrado_logico && $this->id_usuario==$usuario->ID_INSTANCIA();
	}
}
?>
