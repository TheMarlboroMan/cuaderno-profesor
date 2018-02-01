<?
class Curso_sql extends Base_textos_sql
{
	public function TABLA() {return Curso::TABLA;}
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
id_curso INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
id_usuario INT UNSIGNED NOT NULL,
fecha DATE NOT NULL,
hora TIME NOT NULL,
titulo CHAR(20) NOT NULL,
borrado_logico BOOLEAN NOT NULL DEFAULT FALSE
);";

		$resultado[]="ALTER TABLE ".$TABLA." ADD franjas_horario TINYINT UNSIGNED NOT NULL AFTER titulo";
		$resultado[]="ALTER TABLE ".$TABLA." ADD configurando_horario BOOLEAN NOT NULL DEFAULT FALSE AFTER franjas_horario";

		return $resultado;
	}

	/**********************************************************************/

	public function obtener_para_usuario($id_usuario)
	{
		$criterio="AND id_usuario='".$id_usuario."'";
		return $this->obtener_publico($criterio);
	}
}
?>
