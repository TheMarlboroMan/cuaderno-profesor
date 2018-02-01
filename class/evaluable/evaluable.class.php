<?
class Evaluable extends Contenido_bbdd implements Cacheable, Propiedad_usuario
{
	const TABLA='cp_evaluables';
	const ID='id_evaluable';

	public function NOMBRE_CLASE() {return 'Evaluable';}
	public function TABLA() {return self::TABLA;}
	public function ID() {return self::ID;}

	private static $diccionario=array(
		'id_evaluable' => 'id_evaluable',
		'fecha' => 'fecha',
		'hora' => 'hora',
		'id_usuario' => 'id_usuario',
		'id_grupo' => 'id_grupo',
		'trimestre' => 'trimestre',
		'titulo' => 'titulo',
		'porcentaje' => 'porcentaje',
		'borrado_logico' => 'borrado_logico'
	);

	protected $id_evaluable=null;
	protected $fecha=null;
	protected $hora=null;
	protected $id_usuario=null;
	protected $id_grupo=null;
	protected $trimestre=null;
	protected $titulo=null;
	protected $porcentaje=null;
	protected $borrado_logico=null;

	public function acc_id_evaluable() {return $this->id_evaluable;}
	public function acc_fecha() {return $this->fecha;}
	public function acc_hora() {return $this->hora;}
	public function acc_id_usuario() {return $this->id_usuario;}
	public function acc_id_grupo() {return $this->id_grupo;}	
	public function acc_trimestre() {return $this->trimestre;}
	public function acc_titulo() {return $this->titulo;}
	public function acc_porcentaje() {return $this->porcentaje;}
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
			$items=Item_evaluable::obtener_para_evaluable($this);
			foreach($items as $clave => &$valor) $valor->eliminar();
		}

		return $resultado;
	}

	/**********************************************************************/

	public static function &obtener_para_grupo(Grupo &$g)
	{
		$sql=new Evaluable_sql();
		$texto=$sql->obtener_para_grupo($g->ID_INSTANCIA());
		$ins=new Evaluable();
		$resultado=$ins->obtener_array_objetos($texto);
		return $resultado;
	}

	public static function &obtener_para_grupo_y_trimestre(Grupo &$g, $trimestre)
	{
		$sql=new Evaluable_sql();
		$texto=$sql->obtener_para_grupo_y_trimestre($g->ID_INSTANCIA(), $trimestre);
		$ins=new Evaluable();
		$resultado=$ins->obtener_array_objetos($texto);
		return $resultado;
	}

	public static function obtener_porcentaje_total_para_grupo_y_trimestre(Grupo &$g, $trimestre)
	{
		$items=self::obtener_para_grupo_y_trimestre($g, $trimestre);
		$resultado=$g->acc_porcentaje_comportamiento();
		foreach($items as $clave => &$valor) $resultado+=$valor->porcentaje;
		return $resultado;
	}

	/**********************************************************************/

	public function pertenece_a_y_es_valido(Usuario &$usuario)
	{
		return $this->id_evaluable && !$this->borrado_logico && $this->id_usuario==$usuario->ID_INSTANCIA();
	}

	/**********************************************************************/

	public function &FACTORIA_CACHE($id) 
	{
		$resultado=new Evaluable($id);
		return $resultado;
	}
	public function INDICE_CACHE() {return 'evaluable';}
}
?>
