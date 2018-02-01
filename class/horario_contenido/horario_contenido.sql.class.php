<?
class Horario_contenido_sql extends Base_textos_sql
{
	public function TABLA() {return Horario_contenido::TABLA;}
	public function ORDEN_DEFECTO() {return 'dia ASC';}
	public function CRITERIO_DEFECTO() {return 'AND TRUE';}
	public function VER_TODO() {return "TRUE";}
	public function VER_VISIBLE() {return "TRUE";}
	public function VER_PUBLICO() {return "TRUE";}

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
id_curso INT UNSIGNED NOT NULL,
dia TINYINT UNSIGNED NOT NULL,
posicion TINYINT UNSIGNED NOT NULL,
tipo TINYINT UNSIGNED NOT NULL,
id_contenido INT UNSIGNED NOT NULL
);";

		return $resultado;
	}

	/**********************************************************************/

	public function obtener_para_curso($id_curso)
	{
		$criterio="AND id_curso='".$id_curso."'";
		return $this->obtener_publico($criterio);
	}
}
?>
