<?
class Comportamiento_alumno_sql extends Base_textos_sql
{
	public function TABLA() {return Comportamiento_alumno::TABLA;}
	public function ORDEN_DEFECTO() {return 'id_entrada ASC';}
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
id_entrada INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
fecha DATE NOT NULL,
hora TIME NOT NULL,
id_usuario INT UNSIGNED NOT NULL,
id_alumno INT UNSIGNED NOT NULL,
trimestre TINYINT UNSIGNED NOT NULL,
valor TINYINT UNSIGNED NOT NULL,
borrado_logico BOOLEAN NOT NULL DEFAULT FALSE
);";

		return $resultado;
	}

	/**********************************************************************/

	public function obtener_para_alumno_y_trimestre($id_alumno, $trimestre)
	{
		$criterio="AND id_alumno='".$id_alumno."'
AND trimestre='".$trimestre."'";
		return $this->obtener_publico($criterio);
	}
}
?>
