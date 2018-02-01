<?
class Item_evaluable extends Contenido_bbdd implements Propiedad_usuario
{
	const TABLA='cp_items_evaluables';
	const ID='id_item';

	public function NOMBRE_CLASE() {return 'Item_evaluable';}
	public function TABLA() {return self::TABLA;}
	public function ID() {return self::ID;}

	private static $diccionario=array(
		'id_item' => 'id_item',
		'fecha' => 'fecha',
		'hora' => 'hora',
		'id_usuario' => 'id_usuario',
		'id_evaluable' => 'id_evaluable',
		'maximo_valor' => 'maximo_valor',
		'titulo' => 'titulo',
		'borrado_logico' => 'borrado_logico'
	);

	protected $id_item=null;
	protected $fecha=null;
	protected $hora=null;
	protected $id_usuario=null;
	protected $id_evaluable=null;
	protected $maximo_valor=null;
	protected $titulo=null;
	protected $borrado_logico=null;

	public function acc_id_item() {return $this->id_item;}
	public function acc_fecha() {return $this->fecha;}
	public function acc_hora() {return $this->hora;}
	public function acc_id_usuario() {return $this->id_usuario;}
	public function acc_id_evaluable() {return $this->id_evaluable;}
	public function acc_maximo_valor() {return $this->maximo_valor;}
	public function acc_titulo() {return $this->titulo;}
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
			$entradas=Dato_evaluacion_alumno::obtener_para_item_evaluable($this);
			foreach($entradas as $clave => &$valor) $valor->eliminar();
		}

		return $resultado;
	}

	/**********************************************************************/

	public static function &obtener_para_evaluable(Evaluable &$e)
	{
		$sql=new Item_evaluable_sql();
		$texto=$sql->obtener_para_evaluable($e->ID_INSTANCIA());
		$ins=new Item_evaluable();
		$resultado=$ins->obtener_array_objetos($texto);		
		return $resultado;
	}

	//Indica cuantas veces se ha rellenado.
	public function obtener_cuenta_entradas()
	{
		return Dato_evaluacion_alumno::obtener_cuenta_para_item_evaluable($this);
	}

	/**********************************************************************/

	public function pertenece_a_y_es_valido(Usuario &$usuario)
	{
		return $this->id_item && !$this->borrado_logico && $this->id_usuario==$usuario->ID_INSTANCIA();
	}
}
?>
