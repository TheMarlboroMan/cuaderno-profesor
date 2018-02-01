<?
class Usuario extends Contenido_bbdd
{
	const TABLA='cp_usuarios';
	const ID='id_usuario';
	const CLAVE_SESION='usuario';

	public function NOMBRE_CLASE() {return 'Usuario';}
	public function TABLA() {return self::TABLA;}
	public function ID() {return self::ID;}

	private static $diccionario=array(
		'id_usuario' => 'id_usuario',
		'fecha' => 'fecha',
		'hora' => 'hora',
		'id_curso_actual' => 'id_curso_actual',
		'login' => 'login',
		'pass' => 'pass',
		'nombre_completo' => 'nombre_completo',
		'email' => 'email',
		'borrado_logico' => 'borrado_logico'
	);

	protected $id_usuario=null;
	protected $fecha=null;
	protected $hora=null;
	protected $id_curso_actual=null;
	protected $login=null;
	protected $pass=null;
	protected $nombre_completo=null;
	protected $email=null;
	protected $borrado_logico=null;

	public function acc_id_usuario() {return $this->id_usuario;}
	public function acc_fecha() {return $this->fecha;}
	public function acc_hora() {return $this->hora;}
	public function acc_id_curso_actual() {return $this->id_curso_actual;}
	public function acc_login() {return $this->login;}
	public function acc_pass() {return $this->pass;}
	public function acc_email() {return $this->email;}
	public function acc_nombre_completo() {return $this->nombre_completo;}
	public function es_borrado_logico() {return $this->borrado_logico;}

	public function __construct(&$datos=null)
	{
		parent::__construct($datos, self::$diccionario);
	}

	public function crear(&$datos=null)
	{
		$datos['pass']=isset($datos['pass']) ? md5($datos['pass']) : null;
		$resultado=parent::base_crear($datos, 'fecha, hora', 'CURDATE(), CURTIME()');
		return $resultado;
	}

	public function modificar(&$datos=null)
	{
		$datos['pass']=isset($datos['pass']) ? md5($datos['pass']) : null;
		$resultado=parent::base_modificar($datos);
		return $resultado;
	}

	public function eliminar(&$datos=null)
	{
		$resultado=parent::base_eliminar($datos);
		return $resultado;
	}

	public function es_logueable()
	{
		return $this->ID_INSTANCIA() && !$this->borrado_logico;
	}

	public function establecer_curso_actual(Curso &$c)
	{
		if($c->pertenece_a_y_es_valido($this))
		{
			$datos_modificar=array('id_curso_actual' => $c->ID_INSTANCIA());
			return $this->modificar($datos_modificar);
		}
		else
		{
			return false;
		}
	}

	public static function &login($login, $pass)
	{
		$pass=md5($pass);
		$texto=Usuario_sql::login($login, $pass);
		$consulta=new Consulta_mysql;
		$consulta->consultar($texto);
		if(!$consulta->filas()) 
		{
			$resultado=false;
		}
		else
		{
			$consulta->leer();
			$datos=$consulta->resultados();
			$resultado=new Usuario($datos);
		}

		return $resultado;		
	}

	public function logout(&$sesion=null)
	{
		if(!$sesion) global $sesion;
		unset($sesion[self::CLAVE_SESION]);
	}
}
?>
