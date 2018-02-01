<?
class Evaluable_sql extends Base_textos_sql
{
	public function TABLA() {return Evaluable::TABLA;}
	public function ORDEN_DEFECTO() {return 'titulo';}
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
id_evaluable INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
fecha DATE NOT NULL,
hora TIME NOT NULL,
id_usuario INT UNSIGNED NOT NULL,
id_grupo INT UNSIGNED NOT NULL,
trimestre TINYINT UNSIGNED NOT NULL,
titulo VARCHAR(50) NOT NULL,
porcentaje TINYINT UNSIGNED NOT NULL,
borrado_logico BOOLEAN NOT NULL DEFAULT FALSE
);";

		return $resultado;
	}

	/**********************************************************************/

	public function obtener_para_grupo($id_grupo)
	{
		$criterio="AND id_grupo='".$id_grupo."'";
		return $this->obtener_publico($criterio);
	}

	public function obtener_para_grupo_y_trimestre($id_grupo, $trimestre)
	{
		$criterio="AND id_grupo='".$id_grupo."'
AND trimestre='".$trimestre."'";
		return $this->obtener_publico($criterio);
	}

}
?>
