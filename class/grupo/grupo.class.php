<?
class Grupo extends Contenido_bbdd implements Cacheable, Propiedad_usuario
{
	const TABLA='cp_grupos';
	const ID='id_grupo';

	public function NOMBRE_CLASE() {return 'Grupo';}
	public function TABLA() {return self::TABLA;}
	public function ID() {return self::ID;}

	private static $diccionario=array(
		'id_grupo' => 'id_grupo',
		'id_usuario' => 'id_usuario',
		'id_curso' => 'id_curso',
		'fecha' => 'fecha',
		'hora' => 'hora',
		'titulo' => 'titulo',
		'trimestre_activo' => 'trimestre_activo',
		'porcentaje_comportamiento' => 'porcentaje_comportamiento',
		'max_comportamiento' => 'max_comportamiento',
		'inicio_comportamiento' => 'inicio_comportamiento',
		'color_grupo' => 'color_grupo',
		'borrado_logico' => 'borrado_logico'
	);

	protected $id_grupo=null;
	protected $id_usuario=null;
	protected $id_curso=null;
	protected $fecha=null;
	protected $hora=null;
	protected $titulo=null;
	protected $trimestre_activo=null;
	protected $porcentaje_comportamiento=null;
	protected $max_comportamiento=null;
	protected $inicio_comportamiento=null;
	protected $color_grupo=null;
	protected $borrado_logico=null;

	public function acc_id_grupo() {return $this->id_grupo;}
	public function acc_id_usuario() {return $this->id_usuario;}
	public function acc_id_curso() {return $this->id_curso;}
	public function acc_fecha() {return $this->fecha;}
	public function acc_hora() {return $this->hora;}
	public function acc_titulo() {return $this->titulo;}
	public function acc_trimestre_activo() {return $this->trimestre_activo;}
	public function acc_porcentaje_comportamiento() {return $this->porcentaje_comportamiento;}
	public function acc_max_comportamiento() {return $this->max_comportamiento;}
	public function acc_color_grupo() {return $this->color_grupo;}
	public function acc_inicio_comportamiento() {return $this->inicio_comportamiento;}
	
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
			$alumnos=Alumno::obtener_para_grupo($this);
			foreach($alumnos as $clave => &$valor) $valor->eliminar();

			$evaluables=Evaluable::obtener_para_grupo($this);
			foreach($evaluables as $clave => &$valor) $valor->eliminar();
		}

		return $resultado;
	}

	/**********************************************************************/

	public static function &obtener_para_usuario_y_curso(Usuario &$u, Curso &$c)
	{
		$sql=new Grupo_sql();
		$texto=$sql->obtener_para_usuario_y_curso($u->ID_INSTANCIA(), $c->ID_INSTANCIA());
		$ins=new Grupo();
		$resultado=$ins->obtener_array_objetos($texto);
		return $resultado;
	}

	//Devuelve true si hay en el grupo y trimestre al menos un evaluable
	//que contenga items evaluables.
	public function es_evaluable_en_trimestre($trimestre)
	{
		$sql=new Grupo_sql();
		$texto=$sql->es_evaluable_en_trimestre($this->ID_INSTANCIA(), $trimestre);
		
		try
		{
			$consulta=new Consulta_Mysql();
			$consulta->texto($texto);
			$consulta->consultar();
			$consulta->leer();
			$resultado=$consulta->filas();
			return $resultado;
		}
		catch(Excepcion_consulta_mysql $e)
		{
			return false;
		}
	}	

	/**********************************************************************/

	public function pertenece_a_y_es_valido(Usuario &$usuario)
	{
		return $this->id_grupo && !$this->borrado_logico && $this->id_usuario==$usuario->ID_INSTANCIA();
	}

	/**********************************************************************/

	public function &FACTORIA_CACHE($id)
	{
		$resultado=new Grupo($id);
		return $resultado;
	}
	public function INDICE_CACHE() {return 'grupo';}
}
?>
