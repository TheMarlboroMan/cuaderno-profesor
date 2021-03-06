<?
class Curso extends Contenido_bbdd implements Cacheable, Propiedad_usuario
{
	const TABLA='cp_cursos';
	const ID='id_curso';

	public function NOMBRE_CLASE() {return 'Curso';}
	public function TABLA() {return self::TABLA;}
	public function ID() {return self::ID;}

	private static $diccionario=array(
		'id_curso' => 'id_curso',
		'id_usuario' => 'id_usuario',
		'fecha' => 'fecha',
		'hora' => 'hora',
		'titulo' => 'titulo',
		'franjas_horario' => 'franjas_horario',
		'configurando_horario' => 'configurando_horario',
		'borrado_logico' => 'borrado_logico'
	);

	protected $id_curso=null;
	protected $id_usuario=null;
	protected $fecha=null;
	protected $hora=null;
	protected $titulo=null;
	protected $franjas_horario=null;
	protected $configurando_horario=null;
	protected $borrado_logico=null;

	public function acc_id_curso() {return $this->id_curso;}
	public function acc_id_usuario() {return $this->id_usuario;}
	public function acc_fecha() {return $this->fecha;}
	public function acc_hora() {return $this->hora;}
	public function acc_titulo() {return $this->titulo;}
	public function acc_franjas_horario() {return $this->franjas_horario;}
	public function es_configurando_horario() {return $this->configurando_horario;}
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
			$usuario=new Usuario($this->id_usuario);
			$grupos=Grupo::obtener_para_usuario_y_curso($usuario, $this);
			foreach($grupos as $clave => &$valor) $valor->eliminar();
		}

		return $resultado;
	}

	/**********************************************************************/

	public function es_horario_configurado() {return $this->franjas_horario;}

	public function iniciar_configuracion_horario()
	{
		$datos_modificar=array('configurando_horario' => 1);
		return $this->modificar($datos_modificar);
	}

	public function finalizar_configuracion_horario()
	{
		$datos_modificar=array('configurando_horario' => 0);
		return $this->modificar($datos_modificar);
	}

	public static function &obtener_para_usuario(Usuario &$u)
	{
		$sql=new Curso_sql();
		$ins=new Curso();
		$texto=$sql->obtener_para_usuario($u->ID_INSTANCIA());
		$resultado=$ins->obtener_array_objetos($texto);
		return $resultado;
	}

	public function actualizar_franjas_horario($anteriores)
	{
		//Si estamos reduciendo, borramos lo que sobra.
		if($anteriores > $this->franjas_horario)
		{
			$dummy_franja=new Horario_franja();
			$dummy_contenido=new Horario_contenido();
			$f=$this->franjas_horario;

			Herramientas_horario::eliminar_para_curso_desde_posicion($dummy_franja, $this, $f);
			Herramientas_horario::eliminar_para_curso_desde_posicion($dummy_contenido, $this, $f);
		}
		else if($anteriores < $this->franjas_horario)
		{
			$dummy_franja=new Horario_franja();
			$dummy_contenido=new Horario_contenido();
			$cantidad=$this->franjas_horario-$anteriores;

			Herramientas_horario::crear_para_curso($dummy_franja, $this, $cantidad);
			Herramientas_horario::crear_para_curso($dummy_contenido, $this, $cantidad);
		}
	}

	/**********************************************************************/

	public function pertenece_a_y_es_valido(Usuario &$usuario)
	{
		return $this->id_curso && !$this->borrado_logico && $this->id_usuario==$usuario->ID_INSTANCIA();
	}

	/**********************************************************************/

	public function &FACTORIA_CACHE($id) 
	{
		$resultado=new Curso($id);
		return $resultado;
	}
	public function INDICE_CACHE() {return 'curso';}
}
?>
