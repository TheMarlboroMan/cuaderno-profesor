<?
abstract class Herramientas_logica
{
	const ID_ELIMINAR='id_del';
	const ID_MODIFICAR='id_item_form';

	public static function obtener_id_eliminar($get)
	{
		$id=isset($get[self::ID_ELIMINAR]) ? $get[self::ID_ELIMINAR] : 0;
		return $id;
	}

	public static function obtener_id_modificar($get)
	{
		$id=isset($get[self::ID_MODIFICAR]) ? $get[self::ID_MODIFICAR] : 0;
		return $id;
	}

	public static function obtener_array_datos(array $datos, array $diccionario)
	{
		$resultado=array();

		foreach($diccionario as $clave => $valor)
		{
			if(isset($datos[$valor])) $resultado[$clave]=$datos[$valor];
		}

		return $resultado;
	}

	public static function &crear_item(Propiedad_usuario &$item, array $datos_crear, $modo, $xml=false)
	{
		$resultado_accion=0;
		$mensaje_accion='';

		try
		{
			$item->crear($datos_crear);
			$resultado_accion=1;
			$mensaje_accion='ok';
		}
		catch(Excepcion_consulta_mysql $e)
		{
			$mensaje_accion='Ha ocurrido un error en el proceso';
		}

		if($xml) $resultado=new Resultado_logica_xml($modo, $resultado_accion, $mensaje_accion);
		else $resultado=new Resultado_logica_redireccion($modo, $resultado_accion);

		return $resultado;
	}

	public static function &modificar_item(Usuario &$usuario, Propiedad_usuario &$item, array &$datos, $modo, $xml=false)
	{
		$resultado_accion=0;
		$mensaje_accion='';

		if(!$item->pertenece_a_y_es_valido($usuario))
		{
			$resultado_accion=0;
			$mensaje_accion='No se ha podido realizar el proceso por un conflicto de propiedad.';
		}
		else
		{
			try
			{
				if(!$item->modificar($datos))
				{
					$resultado_accion=0;
					$mensaje_accion='Ha ocurrido un error en el proceso de cambio.';
				}
				else
				{
					$resultado_accion=1;
					$mensaje_accion='ok';
				}
			}
			catch(Excepcion_consulta_sql $e)
			{
				$resultado_accion=0;
				$mensaje_accion='Ha ocurrido un error en el proceso.';
			}
		}

		if($xml) $resultado=new Resultado_logica_xml($modo, $resultado_accion, $mensaje_accion);
		else $resultado=new Resultado_logica_redireccion($modo, $resultado_accion);

		return $resultado;
	}

	public static function &eliminar_item(Usuario &$usuario, Propiedad_usuario &$item, $modo, $xml=false)
	{
		$resultado_accion=0;
		$mensaje_accion='';

		if(!$item->pertenece_a_y_es_valido($usuario))
		{
			$resultado_accion=0;
			$mensaje_accion='No se ha podido realizar el proceso por un conflicto de propiedad.';
		}
		else
		{
			try
			{
				if(!$item->eliminar())
				{
					$resultado_accion=0;
					$mensaje_accion='Ha ocurrido un error en el proceso de eliminado.';
				}
				else
				{
					$resultado_accion=1;
					$mensaje_accion='ok';
				}
			}
			catch(Excepcion_consulta_sql $e)
			{
				$resultado_accion=0;
				$mensaje_accion='Ha ocurrido un error en el proceso.';
			}
		}

		if($xml) $resultado=new Resultado_logica_xml($modo, $resultado_accion, $mensaje_accion);
		else $resultado=new Resultado_logica_redireccion($modo, $resultado_accion);

		return $resultado;
	}
}
?>
