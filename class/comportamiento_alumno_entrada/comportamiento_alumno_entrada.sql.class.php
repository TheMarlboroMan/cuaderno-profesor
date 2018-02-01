<?
class Comportamiento_alumno_entrada_sql extends Base_textos_sql
{
	public function TABLA() {return Comportamiento_alumno_entrada::TABLA;}
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
id_comportamiento INT UNSIGNED NOT NULL,
fecha DATE NOT NULL,
hora TIME NOT NULL,
valor SMALLINT NOT NULL,
valor_original TINYINT UNSIGNED NOT NULL,
texto VARCHAR(255) NOT NULL,
borrado_logico BOOLEAN NOT NULL DEFAULT FALSE
);";

		return $resultado;
	}
}
?>
