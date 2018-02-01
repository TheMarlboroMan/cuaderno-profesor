<?
interface Cacheable
{
	public function &FACTORIA_CACHE($id);
	public function INDICE_CACHE();
}

abstract class Cache
{
	private static $CACHE=array();

	public static function &obtener_de_cache(Cacheable $cacheable, $id)
	{
		$clave=$cacheable->INDICE_CACHE();

		if(!isset(self::$CACHE[$clave])) self::$CACHE[$clave]=array();
		if(!isset(self::$CACHE[$clave][$id])) self::$CACHE[$clave][$id]=&$cacheable->FACTORIA_CACHE($id);
		return self::$CACHE[$clave][$id];
	}
}
?>
