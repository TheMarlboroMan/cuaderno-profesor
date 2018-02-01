<?
class Item_evaluable_sql extends Base_textos_sql
{
	public function TABLA() {return Item_evaluable::TABLA;}
	public function ORDEN_DEFECTO() {return 'id_item ASC';}
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
id_item INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
fecha DATE NOT NULL,
hora TIME NOT NULL,
id_usuario INT UNSIGNED NOT NULL,
id_evaluable INT UNSIGNED NOT NULL,
maximo_valor TINYINT UNSIGNED NOT NULL,
titulo VARCHAR(50) NOT NULL,
borrado_logico BOOLEAN NOT NULL DEFAULT FALSE
);";

		return $resultado;
	}

	/**********************************************************************/

	public function obtener_para_evaluable($id_evaluable)
	{
		$criterio="AND id_evaluable='".$id_evaluable."'";
		return $this->obtener_publico($criterio);
	}
}
?>
