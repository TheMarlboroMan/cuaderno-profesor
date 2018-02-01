<?
class Plugin_logica_tabla extends Plugin_logica_maquina_web
{
	private $usuario;

	public function preparar($get, $post, $files) 
	{
		$this->usuario=&$this->acc_maquina_web()->obtener_usuario();
	}

	//Los resultados enviados son para generar XML.

	public function &logica_control($modo, $get, $post, $files)
	{
		$clave_estado_logica=Maquina_web::CLAVE_ESTADO_LOGICA;
		switch($modo)
		{
			case 'guardar_datos':	

				$valor=isset($post['v']) ? $post['v'] : null;
				$tipo=isset($post['t']) ? $post['t'] : null;
				$id_alumno=isset($post['ida']) ? $post['ida'] : null;
				$id_item=isset($post['idi']) ? $post['idi'] : null;
				$id_dato=isset($post['idd']) ? $post['idd'] : null;

				switch($tipo)
				{
					case 'nuevo':
						if(is_null($valor) || !$id_alumno || !$id_item)
						{
							$resultado=new Resultado_logica_xml('crear_datos', 0, 'No se ha podido realizar el proceso');
						}
						else
						{
							$resultado=$this->crear_nueva_entrada($valor, $id_alumno, $id_item);
						}
					break;

					case 'actualizar':
						if(is_null($valor) || !$id_dato)
						{
							$resultado=new Resultado_logica_xml('guardar_datos', 0, 'No se ha podido realizar el proceso');
						}
						else
						{
							$resultado=$this->actualizar_entrada($valor, $id_dato);
						}
					break;

					case 'eliminar':
						if(!$id_dato)
						{
							$resultado=new Resultado_logica_xml('eliminar_datos', 0, 'No se ha podido realizar el proceso');
						}
						else
						{
							$resultado=$this->eliminar_entrada($id_dato);
						}
					break;
			
					default:
						$resultado=new Resultado_logica_xml('guardar_datos', 0, 'No se ha podido realizar el proceso');
					break;
				}
			break;

			case 'guardar_comportamiento':

				$id_comportamiento=isset($post['id_comportamiento']) ? $post['id_comportamiento'] : null;
				$multiplicador=isset($post['multiplicador']) ? $post['multiplicador'] : null;
				$valor=isset($post['valor']) ? $post['valor'] : null;
				$anotacion=isset($post['anotacion']) ? $post['anotacion'] : null;

				$resultado=$this->guardar_comportamiento($id_comportamiento, $multiplicador, $valor, $anotacion);
			break;

			case 'guardar_datos_evaluacion':

				$valor=isset($post['v']) ? $post['v'] : null;
				$tipo=isset($post['t']) ? $post['t'] : null;
				$id_alumno=isset($post['ida']) ? $post['ida'] : null;
				$id_entrada=isset($post['idn']) ? $post['idn'] : null;
				$trimestre=isset($post['tr']) ? $post['tr'] : null;

				switch($tipo)
				{
					case 'nuevo':
						if(is_null($valor) || !$id_alumno || !$trimestre)
						{
							$resultado=new Resultado_logica_xml('crear_datos', 0, 'No se ha podido realizar el proceso');
						}
						else
						{
							$resultado=$this->crear_nota_evaluacion($valor, $id_alumno, $trimestre);
						}
					break;

					case 'actualizar':
						if(is_null($valor) || !$id_entrada)
						{
							$resultado=new Resultado_logica_xml('guardar_datos', 0, 'No se ha podido realizar el proceso');
						}
						else
						{
							$resultado=$this->actualizar_nota_evaluacion($valor, $id_entrada);
						}
					break;					
				}
			break;		

			default:
				$resultado=new Resultado_logica_xml('defecto', 0, 'Error desconocido');
			break;
		}

		return $resultado;
	}

	/***************/

	private function &crear_nueva_entrada($valor, $id_alumno, $id_item)
 	{
		$alumno=new Alumno($id_alumno);
		$item=new Item_evaluable($id_item);
		if(!$alumno->pertenece_a_y_es_valido($this->usuario) || !$item->pertenece_a_y_es_valido($this->usuario))
		{
			$resultado=new Resultado_logica_xml('guardar_datos', 0, 'No se ha podido realizar el proceso por un conflicto de propiedad');
		}
		else
		{
			$datos_crear=array(
			'id_item_evaluable' => $item->ID_INSTANCIA(),
			'id_alumno' => $alumno->ID_INSTANCIA(),
			'id_usuario' => $this->usuario->ID_INSTANCIA(),
			'valor' => $valor,
			'activo' => true);

			$temp=new Dato_evaluacion_alumno();
			try
			{
				$temp->crear($datos_crear);
				$resultado=new Resultado_logica_xml('crear_datos', 1, 'nuevo', null, 'idd="'.$temp->ID_INSTANCIA().'"');

			}
			catch(Excepcion_consulta_mysql $e)
			{									
				$resultado=new Resultado_logica_xml('crear_datos', 0, 'No se ha podido realizar el proceso');
			}
		}

		return $resultado;
	}

	private function &actualizar_entrada($valor, $id_dato)
	{
		$dato=new Dato_evaluacion_alumno($id_dato);

		if(!$dato->pertenece_a_y_es_valido($this->usuario))
		{
			$resultado=new Resultado_logica_xml('guardar_datos', 0, 'No se ha podido realizar el proceso por un conflicto de propiedad');
		}
		else
		{
			$datos_modificar=array('valor' => $valor);
			try
			{
				$dato->modificar($datos_modificar);
				$resultado=new Resultado_logica_xml('guardar_datos', 1, 'dato', null, 'idd="'.$dato->ID_INSTANCIA().'"');
			}
			catch(Excepcion_consulta_mysql $e)
			{									
				$resultado=new Resultado_logica_xml('guardar_datos', 0, 'No se ha podido realizar el proceso');
			}
		}

		return $resultado;
	}

	private function &eliminar_entrada($id_dato)
	{
		$dato=new Dato_evaluacion_alumno($id_dato);

		if(!$dato->pertenece_a_y_es_valido($this->usuario))
		{
			$resultado=new Resultado_logica_xml('eliminar_datos', 0, 'No se ha podido realizar el proceso por un conflicto de propiedad');
		}
		else
		{
			try
			{
				$dato->eliminar($datos_modificar);
				$resultado=new Resultado_logica_xml('eliminar_datos', 1, 'nuevo');
			}
			catch(Excepcion_consulta_mysql $e)
			{									
				$resultado=new Resultado_logica_xml('eliminar_datos', 0, 'No se ha podido realizar el proceso');
			}
		}

		return $resultado;
	}

	private function guardar_comportamiento($id_comportamiento, $multiplicador, $valor, $anotacion)
	{
		$comportamiento=new Comportamiento_alumno($id_comportamiento);
		if(!$comportamiento->pertenece_a_y_es_valido($this->usuario) || is_null($multiplicador))
		{
			$resultado=new Resultado_logica_xml('guardar_comportamiento', 0, 'No se ha podido realizar el proceso');
		}
		else
		{
			$entrada=$comportamiento->crear_nueva_entrada($multiplicador, $valor, $anotacion);
			if(!$entrada)
			{	
				$resultado=new Resultado_logica_xml('guardar_comportamiento', 0, 'No se ha podido realizar el proceso');
			}
			else
			{
				if(!$comportamiento->recibir_entrada($entrada))
				{
					$resultado=new Resultado_logica_xml('guardar_comportamiento', 0, 'No se ha podido finalizar el proceso');
				}
				else
				{
					$nuevo_valor=$comportamiento->acc_valor();
					$nueva_media=$comportamiento->calcular_media();
					$resultado=new Resultado_logica_xml('guardar_comportamiento', 1, 'ok', null, 'val="'.$nuevo_valor.'" med="'.$nueva_media.'"');
				}
			}
		}

		return $resultado;
	}

	private function crear_nota_evaluacion($valor, $id_alumno, $trimestre)
	{
		$alumno=new Alumno($id_alumno);
		if(!$alumno->pertenece_a_y_es_valido($this->usuario))
		{
			$resultado=new Resultado_logica_xml('guardar_datos', 0, 'No se ha podido realizar el proceso por un conflicto de propiedad');
		}
		else if($trimestre < 1 || $trimestre > 3 || $valor < 1 || $valor > 10)
		{
			$resultado=new Resultado_logica_xml('guardar_datos', 0, 'La informacion de trimestre o nota recibida es incorrecta');
		}
		else
		{
			$datos_crear=array(
			'id_alumno' => $alumno->ID_INSTANCIA(),
			'id_usuario' => $this->usuario->ID_INSTANCIA(),
			'trimestre' => $trimestre,
			'valor' => $valor);

			$temp=new Nota_final_evaluacion_alumno();
			try
			{
				$temp->crear($datos_crear);
				$resultado=new Resultado_logica_xml('crear_datos', 1, 'nuevo', null, 'idn="'.$temp->ID_INSTANCIA().'"');

			}
			catch(Excepcion_consulta_mysql $e)
			{									
				$resultado=new Resultado_logica_xml('crear_datos', 0, 'No se ha podido realizar el proceso');
			}
		}

		return $resultado;
	}

	private function actualizar_nota_evaluacion($valor, $id_entrada)
	{
		$temp=new Nota_final_evaluacion_alumno($id_entrada);

		if(!$temp->pertenece_a_y_es_valido($this->usuario))
		{
			$resultado=new Resultado_logica_xml('actualizar_datos', 0, 'No se ha podido realizar el proceso por un conflicto de propiedad');
		}
		else if($valor < 1 || $valor > 10)
		{
			$resultado=new Resultado_logica_xml('actualizar_datos', 0, 'La informacion de nota recibida es incorrecta');
		}
		else
		{
			$datos_modificar=array('valor' => $valor);
			try
			{
				$temp->modificar($datos_modificar);
				$resultado=new Resultado_logica_xml('actualizar_datos', 1, 'actualizar', null, 'idn="'.$temp->ID_INSTANCIA().'"');

			}
			catch(Excepcion_consulta_mysql $e)
			{									
				$resultado=new Resultado_logica_xml('actualizar_datos', 0, 'No se ha podido realizar el proceso');
			}
		}

		return $resultado;
	}
};
?>
