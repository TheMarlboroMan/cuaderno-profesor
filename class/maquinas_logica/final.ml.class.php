<?
/*
Lógica de la nota final...
*/

class Plugin_logica_final extends Plugin_logica_maquina_web
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
			case 'guardar_nota_final':

				$valor=isset($post['v']) ? $post['v'] : null;
				$tipo=isset($post['t']) ? $post['t'] : null;
				$id_alumno=isset($post['ida']) ? $post['ida'] : null;
				$id_entrada=isset($post['idn']) ? $post['idn'] : null;

				switch($tipo)
				{
					case 'nuevo':
						if(is_null($valor) || !$id_alumno)
						{
							$resultado=new Resultado_logica_xml('guardar_nota_final', 0, 'No se ha podido realizar el proceso');
						}
						else
						{
							$resultado=$this->crear_nota_final($valor, $id_alumno);
						}
					break;

					case 'actualizar':
						if(is_null($valor) || !$id_entrada)
						{
							$resultado=new Resultado_logica_xml('guardar_nota_final', 0, 'No se ha podido realizar el proceso');
						}
						else
						{
							$resultado=$this->actualizar_nota_final($valor, $id_entrada);
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

	
	private function crear_nota_final($valor, $id_alumno)
	{
		$alumno=new Alumno($id_alumno);
		if(!$alumno->pertenece_a_y_es_valido($this->usuario))
		{
			$resultado=new Resultado_logica_xml('guardar_nota_final', 0, 'No se ha podido realizar el proceso por un conflicto de propiedad');
		}
		else if($valor < 1 || $valor > 10)
		{
			$resultado=new Resultado_logica_xml('guardar_nota_final', 0, 'La informacion de nota recibida es incorrecta');
		}
		else
		{
			$datos_crear=array(
			'id_alumno' => $alumno->ID_INSTANCIA(),
			'id_usuario' => $this->usuario->ID_INSTANCIA(),
			'trimestre' => Nota_final_evaluacion_alumno::TRIMESTRE_FINAL,
			'valor' => $valor);

			$temp=new Nota_final_evaluacion_alumno();
			try
			{
				$temp->crear($datos_crear);
				$resultado=new Resultado_logica_xml('guardar_nota_final', 1, 'nuevo', null, 'idn="'.$temp->ID_INSTANCIA().'"');

			}
			catch(Excepcion_consulta_mysql $e)
			{									
				$resultado=new Resultado_logica_xml('guardar_nota_final', 0, 'No se ha podido realizar el proceso');
			}
		}

		return $resultado;
	}

	private function actualizar_nota_final($valor, $id_entrada)
	{
		$temp=new Nota_final_evaluacion_alumno($id_entrada);

		if(!$temp->pertenece_a_y_es_valido($this->usuario))
		{
			$resultado=new Resultado_logica_xml('guardar_nota_final', 0, 'No se ha podido realizar el proceso por un conflicto de propiedad');
		}
		else if($valor < 1 || $valor > 10)
		{
			$resultado=new Resultado_logica_xml('guardar_nota_final', 0, 'La informacion de nota recibida es incorrecta');
		}
		else
		{
			$datos_modificar=array('valor' => $valor);
			try
			{
				$temp->modificar($datos_modificar);
				$resultado=new Resultado_logica_xml('guardar_nota_final', 1, 'actualizar', null, 'idn="'.$temp->ID_INSTANCIA().'"');

			}
			catch(Excepcion_consulta_mysql $e)
			{									
				$resultado=new Resultado_logica_xml('guardar_nota_final', 0, 'No se ha podido realizar el proceso');
			}
		}

		return $resultado;
	}
};
?>
