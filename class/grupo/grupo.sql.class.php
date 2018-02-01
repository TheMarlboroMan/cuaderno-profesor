<?
class Grupo_sql extends Base_textos_sql
{
	public function TABLA() {return Grupo::TABLA;}
	public function ORDEN_DEFECTO() {return 'titulo ASC';}
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
id_grupo INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
fecha DATE NOT NULL,
hora TIME NOT NULL,
id_usuario INT UNSIGNED NOT NULL,
id_curso INT UNSIGNED NOT NULL,
titulo CHAR(20) NOT NULL,
trimestre_activo TINYINT UNSIGNED NOT NULL DEFAULT 1,
porcentaje_comportamiento TINYINT UNSIGNED NOT NULL,
max_comportamiento TINYINT UNSIGNED NOT NULL,
inicio_comportamiento TINYINT UNSIGNED NOT NULL,
borrado_logico BOOLEAN NOT NULL DEFAULT FALSE
);";

		$resultado[]="ALTER TABLE ".$tabla." ADD color_grupo TINYINT UNSIGNED NOT NULL AFTER inicio_comportamiento";
		return $resultado;
	}

	/**********************************************************************/

	public function obtener_para_usuario_y_curso($id_usuario, $id_curso)
	{
		$criterio="
AND id_usuario='".$id_usuario."'
AND id_curso='".$id_curso."'";

		return $this->obtener_publico($criterio);	
	}

	public function es_evaluable_en_trimestre($id_grupo, $trimestre)
	{
		return "
SELECT ie.*
FROM ".Item_evaluable::TABLA." ie
LEFT JOIN ".Evaluable::TABLA." e ON ie.id_evaluable=e.id_evaluable
LEFT JOIN ".Grupo::TABLA." g ON g.id_grupo=e.id_grupo
WHERE e.trimestre='".$trimestre."'
AND g.id_grupo='".$id_grupo."'";
	}
}
?>
