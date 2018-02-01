<?
/*Grupo de métodos que necesitamos para trabajar con los items que van en 
el horario.*/

interface Horario
{
	public function &obtener_array(Curso &$c);
	public function obtener_posicion_horario();
	public function eliminar_horario();
	public function cambiar_posicion_horario($p, $t);
	public function crear_nueva_entrada_horario(Curso &$c);
}

/*Conjunto de operaciones a realizar sobre las entradas de horario...*/

class Herramientas_horario
{
	public static function eliminar_para_curso_desde_posicion(Horario &$i, Curso &$c, $posicion)
	{
		//Obtener el array de los que queremos...
		$array=$i->obtener_array($c);

		//Eliminarlos si la posición es mayor.
		foreach($array as $clave => &$valor) 
		{
			if($valor->obtener_posicion_horario() > $posicion) $valor->eliminar_horario();
		}

	}

	public static function crear_para_curso(Horario &$i, Curso &$c, $cantidad)
	{
		//Crear las nuevas en posiciones absurdamente altas.
		$it=0;
		while($it < $cantidad)
		{
			$i->crear_nueva_entrada_horario($c);
			++$it;
		}

		//Reordenarlas todas.		
		$it=1;
		$array=$i->obtener_array($c);
		foreach($array as $clave => &$valor) $valor->cambiar_posicion_horario($it++, $c->acc_franjas_horario());
		
	}
}
?>
