<?
/*
Todos aquellos items que se relacionen de forma m a n con otros y que tengan
un identificador en una tabla (por ejemplo, noticias y eventos que puedan ser
comentados) tienen cabida aquí.
*/

class Relaciones
{
	private static $iniciado=false;
	private static $relaciones=array();

	public static function configurar(&$array_relaciones)
	{
		/*El array de relaciones se expresa simplemente como
		id_en_tabla => 'Nombre_clase',
		1 => 'Noticia',
		2 => 'Evento*/

		self::$relaciones=$array_relaciones;
		self::$iniciado=true;
	}

	private static function comprobar()
	{		
		if(!self::$iniciado) 
		{
			die('RELACIONES_ABSTRACT_CLASS_PHP No se ha iniciado el controlador de relaciones');
		}
	}		

	public static function obtener_clase_por_id($id)
	{
		self::comprobar();

		if(isset(self::$relaciones[$id])) return self::$relaciones[$id];
		else return false;
	}

	public static function obtener_id_por_clase($nombre_clase)
	{
		self::comprobar();

		$clave=array_search($nombre_clase, self::$relaciones);

		if($clave===false) return false;
		else return $clave;
	}
}

/*
El módulo de relaciones lo extendemos como nos vaya sirviendo, lo configuramos
y lo instanciamos como parte (o no) de la clase "Noticia-evento" que tenga 
varios "Comentarios" con la finalidad de hacer de puente entre las tablas
de relación y demás... Con cada extensión haremos también el modelado del item
de la tabla de relación.
*/

abstract class Modulo_relaciones_motor
{
	protected $item_base=null;
	protected $clase_entrada_tabla=null;
	protected $clase_relacionada=null;

	public function __construct(&$item, $clase_entrada_tabla, $clase_relacionada)
	{
		$this->item_base=&$item;
		$this->clase_entrada_tabla=$clase_entrada_tabla;
		$this->clase_relacionada=$clase_relacionada;
	}

	public final function relacionar_item(&$item_relacionar, &$datos_relacion=null)
	{
		//A partir del item que hemos recibido tenemos que crear una
		//entrada en la tabla de relación.

		$tupla_relacion=new $this->clase_entrada_tabla;

		//Estos son los datos que necesitamos para la tabla "noticia".
		$nombre_clase=get_class($this->item_base);
		$id_tipo_relacion=Relaciones::obtener_id_por_clase($nombre_clase);
		$id_elemento=$this->item_base->ID_INSTANCIA();

		if(!$id_tipo_relacion || !$id_elemento)
		{
			die('RELACIONES_ABSTRACT_CLASS_PHP El item no esta especificado como relacionable');
		}
		else 
		{
			$tupla_relacion->cargar_datos_item_base($id_tipo_relacion, $id_elemento);
		}

		//Estos son los de la tabla "comentario"...
		$tupla_relacion->cargar_datos_item_relacionado($item_relacionar);
		if($datos_relacion) $tupla_relacion->cargar_datos_extra_relacion($datos_relacion);

		$array_crear=$tupla_relacion->generar_array_crear($tupla_relacion);
		return $tupla_relacion->crear($array_crear);
	}

	private function obtener_criterio_relacion()
	{
		$nombre_clase=get_class($this->item_base);
		$id_tipo_relacion=Relaciones::obtener_id_por_clase($nombre_clase);
		$id_elemento=$this->item_base->ID_INSTANCIA();

		$temp=new $this->clase_relacionada;		
		$campo_id_primario=$temp->ID();
		unset($temp);

		$tupla_relacion=new $this->clase_entrada_tabla();
		$criterio=$tupla_relacion->generar_criterio($campo_id_primario, $id_tipo_relacion, $id_elemento);

		return $criterio;
	}

	public function &obtener_todo($criterio=null, $orden=null, $cantidad=null, $desplazamiento=0)
	{
		$criterio_relacion=$this->obtener_criterio_relacion();
		$tupla_relacion=new $this->clase_entrada_tabla();
		$texto=$tupla_relacion->obtener_todo($criterio.$criterio_relacion, $orden, $cantidad, $desplazamiento);
		$resultado=Contenido_bbdd::obtener_array($this->clase_relacionada, $texto);
		return $resultado;
	}

	public function &obtener_visible($criterio=null, $orden=null, $cantidad=null, $desplazamiento=0)
	{
		$criterio_relacion=$this->obtener_criterio_relacion();
		$tupla_relacion=new $this->clase_entrada_tabla();
		$texto=$tupla_relacion->obtener_visible($criterio.$criterio_relacion, $orden, $cantidad, $desplazamiento);
		$resultado=Contenido_bbdd::obtener_array($this->clase_relacionada, $texto);
		return $resultado;
	}

	public function &obtener_publico($criterio=null, $orden=null, $cantidad=null, $desplazamiento=0)
	{
		$criterio_relacion=$this->obtener_criterio_relacion();
		$tupla_relacion=new $this->clase_entrada_tabla();
		$texto=$tupla_relacion->obtener_publico($criterio.$criterio_relacion, $orden, $cantidad, $desplazamiento);
		$resultado=Contenido_bbdd::obtener_array($this->clase_relacionada, $texto);
		return $resultado;
	}

	public function &consulta_todo($criterio=null, $orden=null, $cantidad=null, $desplazamiento=0)
	{
		$criterio_relacion=$this->obtener_criterio_relacion();
		$tupla_relacion=new $this->clase_entrada_tabla();
		$texto=$tupla_relacion->obtener_todo($criterio.$criterio_relacion, $orden, $cantidad, $desplazamiento);
		return Contenido_bbdd::consulta($texto);
	}

	public function &consulta_visible($criterio=null, $orden=null, $cantidad=null, $desplazamiento=0)
	{
		$criterio_relacion=$this->obtener_criterio_relacion();
		$tupla_relacion=new $this->clase_entrada_tabla();
		$texto=$tupla_relacion->obtener_visible($criterio.$criterio_relacion, $orden, $cantidad, $desplazamiento);
		return Contenido_bbdd::consulta($texto);
	}

	public function &consulta_publico($criterio=null, $orden=null, $cantidad=null, $desplazamiento=0)
	{
		$criterio_relacion=$this->obtener_criterio_relacion();
		$tupla_relacion=new $this->clase_entrada_tabla();
		$texto=$tupla_relacion->obtener_publico($criterio.$criterio_relacion, $orden, $cantidad, $desplazamiento);
		return Contenido_bbdd::consulta($texto);
	}
}

//Ojo con esto... Por un "fallo" de diseño en el motor, los campos deben 
//llamarse EXACTAMENTE IGUAL en base de datos y como propiedades, siendo la
//tarea del diccionario establecer las equivalencias desde los arrays de datos
//que se pasen... Todo lo que usemos tendrá que tener los tres campos del 
//diccionario tal cual.

abstract class Modulo_relaciones_motor_entrada_tabla extends Contenido_bbdd
{
	private static $diccionario=array(
	'id_entrada' => 'id_entrada',	
	'id_tipo' => 'id_tipo',
	'id_elemento' => 'id_elemento');

	protected $id_entrada;
	protected $id_tipo;
	protected $id_elemento;

	public function __construct(&$datos=null, &$otro_diccionario, $tabla, $id)
	{
		$diccionario_final=array_merge(self::$diccionario, $otro_diccionario);
		parent::__construct($datos, $diccionario_final, $tabla, $id);
	}

	public final function cargar_datos_item_base($a, $b)
	{
		$this->id_tipo=$a;
		$this->id_elemento=$b;
	}

	public final function &generar_array_crear(&$item)
	{
		$resultado=array();
		$diccionario=$item::DICCIONARIO();

		foreach($diccionario as $clave => $valor)
		{
			if(property_exists($item, $clave) && !is_object($item->$clave) && strlen($item->$clave))
				$resultado[$valor]=$item->$clave;
		}

		return $resultado;
	}	

	public final function generar_criterio($campo_id_primario, $id_tipo, $id_elemento)
	{
		$tabla=$this->TABLA();

		return "
AND ".$campo_id_primario." IN
(
	SELECT ".$campo_id_primario."
	FROM ".$tabla."
	WHERE 
		id_tipo='".$id_tipo."'
		AND id_elemento='".$id_elemento."'
)";
	}	

	public function crear(&$datos=null){return parent::base_crear($datos);}
	public function modificar(&$datos=null) {return parent::base_modificar($datos);}
	public function eliminar(&$datos=null) {return parent::base_eliminar($datos);}

	public abstract function cargar_datos_item_relacionado(&$a);
	public abstract function obtener_todo($criterio=null, $orden=null, $cantidad=null, $desplazamiento=0);
	public abstract function obtener_visible($criterio=null, $orden=null, $cantidad=null, $desplazamiento=0);
	public abstract function obtener_publico($criterio=null, $orden=null, $cantidad=null, $desplazamiento=0);
}

/*
Ejemplo...

DROP TABLE IF EXISTS app_notas_relacion;
CREATE TABLE app_notas_relacion
(
id_entrada INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
id_tipo INT UNSIGNED NOT NULL,
id_elemento INT UNSIGNED NOT NULL,
id_nota INT UNSIGNED NOT NULL
)ENGINE=MYISAM;

class Modulo_notas extends Modulo_relaciones_motor
{
	const CLASE_RELACIONADA='Nota';
	const CLASE_ENTRADA_TABLA='Modulo_notas_entrada_tabla';

	public function __construct(&$item)
	{
		parent::__construct($item, self::CLASE_ENTRADA_TABLA, self::CLASE_RELACIONADA);
	}
}

class Modulo_notas_entrada_tabla extends Modulo_relaciones_motor_entrada_tabla
{
	//Esto es la representación de una tupla en la tabla...
	const TABLA='app_notas_relacion';
	const ID='id_entrada';

	protected static $diccionario=array('id_nota' => 'id_nota');
	protected $id_nota=false;
	public function acc_id_nota() {return $this->id_nota;}

	public function __construct(&$datos=null)
	{
		parent::__construct($datos, self::$diccionario, self::TABLA, self::ID);
	}

	public function cargar_datos_item_relacionado(&$nota)
	{
		$this->id_nota=$nota->ID_INSTANCIA();
	}

	public function cargar_datos_extra_relacion(&$datos){}

	public function obtener_todo($criterio=null, $orden=null, $cantidad=null, $desplazamiento=0) {return Nota_sql::obtener_todo($criterio, $orden, $cantidad, $desplazamiento);}
	public function obtener_visible($criterio=null, $orden=null, $cantidad=null, $desplazamiento=0) {return Nota_sql::obtener_visible($criterio, $orden, $cantidad, $desplazamiento);}
	public function obtener_publico($criterio=null, $orden=null, $cantidad=null, $desplazamiento=0) {return Nota_sql::obtener_publico($criterio, $orden, $cantidad, $desplazamiento);}
}
*/
?>
