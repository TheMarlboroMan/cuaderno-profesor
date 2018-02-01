<?
class Dato_evaluacion_alumno_sql extends Base_textos_sql
{
	public function TABLA() {return Dato_evaluacion_alumno::TABLA;}
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
titulo CHAR(20) NOT NULL,
id_item_evaluable INT UNSIGNED NOT NULL,
id_alumno INT UNSIGNED NOT NULL,
id_usuario INT UNSIGNED NOT NULL,
valor FLOAT UNSIGNED NOT NULL,
borrado_logico BOOLEAN NOT NULL DEFAULT FALSE
);";

		return $resultado;
	}

	/**********************************************************************/

	public function obtener($id_usuario, $id_alumno, $id_item_evaluable)
	{
		$criterio="AND id_usuario='".$id_usuario."'
AND id_alumno='".$id_alumno."'
AND id_item_evaluable='".$id_item_evaluable."'";

		return $this->obtener_visible($criterio);
	}

	public function obtener_cuenta_para_item_evaluable($id_item_evaluable)
	{
		$criterio=$this->VER_VISIBLE()."
AND id_item_evaluable='".$id_item_evaluable."'";

		return "SELECT COUNT(id_entrada) AS total
FROM ".$this->TABLA()."
WHERE ".$criterio;
	}

	public function obtener_para_item_evaluable($id_item_evaluable)
	{
		$criterio="AND id_item_evaluable='".$id_item_evaluable."'";
		return $this->obtener_publico($criterio);	
	}

	public function obtener_para_alumno($id_alumno)
	{
		$criterio="AND id_alumno='".$id_alumno."'";
		return $this->obtener_publico($criterio);	
	}
}
?>
