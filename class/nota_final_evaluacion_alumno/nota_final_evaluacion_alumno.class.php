<?
class Nota_final_evaluacion_alumno extends Contenido_bbdd
{
	const TABLA='cp_notas_finales_alumnos';
	const ID='id_entrada';
	const TRIMESTRE_FINAL=100;

	public function NOMBRE_CLASE() {return 'Nota_final_evaluacion_alumno';}
	public function TABLA() {return self::TABLA;}
	public function ID() {return self::ID;}

	private static $diccionario=array(
		'id_entrada' => 'id_entrada',
		'fecha' => 'fecha',
		'hora' => 'hora',
		'id_usuario' => 'id_usuario',
		'id_alumno' => 'id_alumno',
		'trimestre' => 'trimestre',
		'valor' => 'valor',
		'borrado_logico' => 'borrado_logico'
	);

	protected $id_entrada=null;
	protected $fecha=null;
	protected $hora=null;
	protected $id_usuario=null;
	protected $id_alumno=null;
	protected $trimestre=null;
	protected $valor=null;
	protected $borrado_logico=null;

	public function acc_id_entrada() {return $this->id_entrada;}
	public function acc_fecha() {return $this->fecha;}
	public function acc_hora() {return $this->hora;}
	public function acc_id_usuario() {return $this->id_usuario;}
	public function acc_id_alumno() {return $this->id_alumno;}
	public function acc_trimestre() {return $this->trimestre;}
	public function acc_valor() {return $this->valor;}
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

	/**********************************************************************/

	public static function obtener_para_alumno_final(Alumno &$a)
	{
		$sql=new Nota_final_evaluacion_alumno_sql();
		$texto=$sql->obtener_para_alumno_y_trimestre($a->ID_INSTANCIA(), self::TRIMESTRE_FINAL);

		$ins=new Nota_final_evaluacion_alumno();
		$resultado=$ins->obtener_objeto_por_texto($texto);

		return $resultado;
	}

	public static function obtener_para_alumno_y_trimestre(Alumno &$a, $trimestre)
	{
		if($trimestre < 1 || $trimestre > 3)
		{
			die('ERROR: Nota_final_evaluacion_alumno::obtener_para_alumno_y_trimestre : trimestre debe estar comprendido entre 1 y 3');
		}

		$sql=new Nota_final_evaluacion_alumno_sql();
		$texto=$sql->obtener_para_alumno_y_trimestre($a->ID_INSTANCIA(), $trimestre);

		$ins=new Nota_final_evaluacion_alumno();
		$resultado=$ins->obtener_objeto_por_texto($texto);

		return $resultado;
	}

	public function pertenece_a_y_es_valido(Usuario &$usuario)
	{
		return $this->id_entrada && !$this->borrado_logico && $this->id_usuario==$usuario->ID_INSTANCIA();
	}

}
?>
