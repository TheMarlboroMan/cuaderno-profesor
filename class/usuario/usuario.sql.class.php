<?
class Usuario_sql extends Base_textos_sql
{
	public function TABLA() {return Usuario::TABLA;}
	public function ORDEN_DEFECTO() {return 'login ASC';}
	public function CRITERIO_DEFECTO() {return 'AND TRUE';}
	public function VER_TODO() {return "TRUE";}
	public function VER_VISIBLE() {return "NOT borrado_logico";}
	public function VER_PUBLICO() {return "NOT borrado_logico";}

	public function TEXTOS_CREAR_TABLAS()
	{	
		$TABLA=$this->TABLA();
		$resultado[]="DROP TABLE IF EXISTS ".$TABLA;
		$resultado[]="CREATE TABLE ".$TABLA."
(
id_usuario INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
fecha DATE NOT NULL,
hora TIME NOT NULL,
login CHAR(32) NOT NULL,
pass CHAR(32) NOT NULL,
nombre_completo VARCHAR(200) NOT NULL,
email VARCHAR(200) NOT NULL,
borrado_logico BOOLEAN NOT NULL DEFAULT FALSE
);";

		$resultado[]="ALTER TABLE ".$TABLA." ADD id_curso_actual INT UNSIGNED NOT NULL AFTER hora";

		return $resultado;
	}

	/**********************************************************************/

	public static function login($login, $pass)
	{
		$texto="
SELECT *
FROM ".self::TABLA()."
WHERE NOT borrado_logico
AND login='".$login."'
AND pass='".$pass."'";

		return $texto;
	}
}
?>
