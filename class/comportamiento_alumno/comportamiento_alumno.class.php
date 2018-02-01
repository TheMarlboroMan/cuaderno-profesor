<?
class Comportamiento_alumno extends Contenido_bbdd
{
	const TABLA='cp_alumnos_comportamiento';
	const ID='id_entrada';

	public function NOMBRE_CLASE() {return 'Comportamiento_alumno';}
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

	public static function &obtener_para_alumno_y_trimestre(Alumno &$a, $trimestre)
	{
		$sql=new Comportamiento_alumno_sql();
		$texto=$sql->obtener_para_alumno_y_trimestre($a->ID_INSTANCIA(), $trimestre);
		$ins=new Comportamiento_alumno();
		$resultado=$ins->obtener_objeto_por_texto($texto);
		return $resultado;
	}

	public static function &generar_para_alumno_y_trimestre(Alumno &$a, $trimestre)
	{
		$usuario=new Usuario($a->acc_id_usuario());
		$grupo=new Grupo($a->acc_id_grupo());
		$valor=$grupo->acc_inicio_comportamiento();

		$datos_crear=array(
'id_usuario' => $usuario->ID_INSTANCIA(),
'id_alumno' => $a->ID_INSTANCIA(),
'trimestre' => $trimestre,
'valor' => $valor);

		try
		{
			$temp=new Comportamiento_alumno();
			$temp->crear($datos_crear);
			return $temp;
		}
		catch(Excepcion_consulta_mysql $e)
		{
			$resultado=null;
			return $resultado;
		}
	}

	public function pertenece_a_y_es_valido(Usuario &$usuario)
	{
		return $this->id_entrada && !$this->borrado_logico && $this->id_usuario==$usuario->ID_INSTANCIA();
	}

	public function crear_nueva_entrada($multiplicador, $valor, $anotacion)
	{
		try
		{
			$datos_crear=array(
'id_comportamiento' => $this->ID_INSTANCIA(),
'valor' => $valor * $multiplicador,
'valor_original' => $this->valor,
'texto' => $anotacion);

			$temp=new Comportamiento_alumno_entrada();
			$temp->crear($datos_crear);
			return $temp;
		}
		catch(Excepcion_consulta_mysql $e)
		{
			$resultado=null;
			return $resultado;
		}
	}

	public function recibir_entrada(Comportamiento_alumno_entrada &$e)
	{
		$valor_actual=$this->valor+$e->acc_valor();

		$da=new Alumno();
		$alumno=Cache::obtener_de_cache($da, $this->acc_id_alumno());

		$dg=new Grupo();		
		$grupo=Cache::obtener_de_cache($dg, $alumno->acc_id_grupo());

		if($valor_actual < 0) $valor_actual=0;
		else if($valor_actual > $grupo->acc_max_comportamiento()) $valor_actual=$grupo->acc_max_comportamiento();

		$datos_modificar=array('valor' => $valor_actual);

		try
		{
			return $this->modificar($datos_modificar);
		}
		catch(Excepcion_consulta_mysql $e)
		{
			return false;
		}
	}

	public function calcular_media()
	{
		$da=new Alumno();
		$alumno=Cache::obtener_de_cache($da, $this->acc_id_alumno());

		$dg=new Grupo();		
		$grupo=Cache::obtener_de_cache($dg, $alumno->acc_id_grupo());
		$valor_comportamiento=$grupo->acc_max_comportamiento();
		if(!$valor_comportamiento) {return 0;}
		else return ($this->valor * 10) / $valor_comportamiento;
	}
}
?>
