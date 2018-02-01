<?
class Plugin_logica_grupo extends Plugin_logica_maquina_web
{
	private $grupo;
	private $usuario;

	public function preparar($get, $post, $files) 
	{
		$id_grupo=isset($get['id_grupo']) ? $get['id_grupo'] : 0;
		$this->grupo=new Grupo($id_grupo);
		$this->usuario=&$this->acc_maquina_web()->obtener_usuario();
	}

	public function &logica_control($modo, $get, $post, $files)
	{
		$clave_estado_logica=Maquina_web::CLAVE_ESTADO_LOGICA;

		//En primer lugar, validamos si el grupo es correcto o si estamos forjando información.
		if(!$this->grupo->pertenece_a_y_es_valido($this->usuario))
		{
			$resultado=new Resultado_logica_redireccion('validar', 0);
		}
		else
		{
			switch($modo)
			{
				///Alumnos ////

				case 'crear_alumno':	
					$resultado=$this->crear_alumno($post);
				break;

				case 'importar_alumnos_texto':
					$cadena=isset($post['texto']) ? $post['texto'] : '';
					$resultado_accion=$this->importar_alumnos_desde_cadena($cadena);
					$resultado=new Resultado_logica_redireccion($modo, $resultado_accion);
				break;

				case 'importar_alumnos_fichero':
					if($files['archivo']['error'] || !$files['archivo']['size'])
					{
						$resultado_accion=0;
					}
					else
					{
						$cadena_resultante=null;

						$archivo=fopen($files['archivo']['tmp_name'], 'r');
						if(!$archivo)
						{
							$resultado_accion=0;
						}
						else
						{
							do
							{
								$linea=fgets($archivo);
								if($linea) $cadena_resultante.=$linea;
							}while($linea);
							fclose($archivo);
						}

						$resultado_accion=self::importar_alumnos_desde_cadena($cadena_resultante);
					}

					$resultado=new Resultado_logica_redireccion($modo, $resultado_accion);
				break;

				case 'modificar_alumno':
					$datos_modificar=Herramientas_logica::obtener_array_datos($post, array(
						'nombre' => 'nombre',
						'apellidos' => 'apellidos',
						'texto' => 'texto'));

					$id_alumno=Herramientas_logica::obtener_id_modificar($post);
					$alumno=new Alumno($id_alumno);
					$resultado=Herramientas_logica::modificar_item($this->usuario, $alumno, $datos_modificar, $modo, true);
		
					if($resultado->acc_resultado())
					{
						$info_alumno=Alumno_vista::mostrar_como_xml($alumno);
						$resultado->mut_datos($info_alumno);
					}
					
				break;

				case 'eliminar_alumno':
					$id_alumno=Herramientas_logica::obtener_id_eliminar($post);
					$alumno=new Alumno($id_alumno);
					$resultado=Herramientas_logica::eliminar_item($this->usuario, $alumno, $modo, isset($get['rxml']));
				break;

				///Evaluables ////

				case 'crear_evaluable':	
					$evaluable=new Evaluable();
					$trimestre=isset($post['trimestre']) ? $post['trimestre'] : null;

					if(!$trimestre)
					{	
						//$resultado=new Resultado_logica_redireccion($modo, 0);
						new Resultado_logica_xml($modo, 0, 'Error de datos');
					}
					else
					{
						$datos_crear=Herramientas_logica::obtener_array_datos($post, array(
							'titulo' => 'titulo',
							'porcentaje' => 'porcentaje'));

						$datos_crear['trimestre']=$trimestre;
						$datos_crear['id_grupo']=$this->grupo->ID_INSTANCIA();
						$datos_crear['id_usuario']=$this->usuario->ID_INSTANCIA();

						$resultado=Herramientas_logica::crear_item($evaluable, $datos_crear, $modo, true);

						if($resultado->acc_resultado())
						{
							$porcentaje=Evaluable::obtener_porcentaje_total_para_grupo_y_trimestre(
								Cache::obtener_de_cache(new Grupo(), $evaluable->acc_id_grupo()),
								$evaluable->acc_trimestre());

							$resultado->mut_att_datos('p="'.$porcentaje.'"');
							$resultado->mut_datos(Evaluable_vista::mostrar_como_xml($evaluable));
						}
					}
				break;

				case 'eliminar_evaluable':
					$id_evaluable=Herramientas_logica::obtener_id_eliminar($post);
					$evaluable=new Evaluable($id_evaluable);
					$resultado=Herramientas_logica::eliminar_item($this->usuario, $evaluable, $modo, isset($get['rxml']));

					if($resultado->acc_resultado())
					{
						$trimestre=$evaluable->acc_trimestre();
						$porcentaje=Evaluable::obtener_porcentaje_total_para_grupo_y_trimestre(
							Cache::obtener_de_cache(new Grupo(), $evaluable->acc_id_grupo()),
							$trimestre);

						$resultado->mut_att_datos('p="'.$porcentaje.'" trimestre="'.$trimestre.'"');
					}
				break;

				case 'modificar_evaluable':
					$datos_modificar=Herramientas_logica::obtener_array_datos($post, array(
						'titulo' => 'titulo',
						'porcentaje' => 'porcentaje'));

					$id_evaluable=Herramientas_logica::obtener_id_modificar($post);
					$evaluable=new Evaluable($id_evaluable);
					
					$resultado=Herramientas_logica::modificar_item($this->usuario, $evaluable, $datos_modificar, $modo, true);
		
					if($resultado->acc_resultado())
					{
						$info=Evaluable_vista::mostrar_como_xml($evaluable);
						$porcentaje=Evaluable::obtener_porcentaje_total_para_grupo_y_trimestre(
							Cache::obtener_de_cache(new Grupo(), $evaluable->acc_id_grupo()),
							$evaluable->acc_trimestre());
						$resultado->mut_att_datos('p="'.$porcentaje.'"');
						$resultado->mut_datos($info);
					}					
				break;

				///Items evaluables ////

				case 'crear_item_evaluable':	
					$item_evaluable=new Item_evaluable();

					$id_evaluable=isset($post['id_evaluable']) ? $post['id_evaluable'] : 0;
					$ev=new Evaluable($id_evaluable);

					if(!$ev->pertenece_a_y_es_valido($this->usuario))
					{
						$resultado=new Resultado_logica_redireccion($modo, 0);
					}
					else
					{
						$datos_crear=Herramientas_logica::obtener_array_datos($post, array(
							'titulo' => 'nombre',
							'maximo_valor' => 'puntuacion'));

						$datos_crear['id_evaluable']=$ev->ID_INSTANCIA();
						$datos_crear['id_usuario']=$this->usuario->ID_INSTANCIA();

						$resultado=Herramientas_logica::crear_item($item_evaluable, $datos_crear, $modo, true);
						if($resultado->acc_resultado())
						{
							$resultado->mut_datos(Item_evaluable_vista::mostrar_como_xml($item_evaluable));
						}
					}
				break;

				case 'eliminar_item_evaluable':
					$id_item_evaluable=Herramientas_logica::obtener_id_eliminar($post);
					$item_evaluable=new Item_evaluable($id_item_evaluable);
					$resultado=Herramientas_logica::eliminar_item($this->usuario, $item_evaluable, $modo, isset($get['rxml']));
				break;

				case 'modificar_item_evaluable':

					$id_item_evaluable=Herramientas_logica::obtener_id_modificar($post);
					$item_evaluable=new Item_evaluable($id_item_evaluable);
	
					$datos_modificar=Herramientas_logica::obtener_array_datos($post, array('titulo' => 'nombre'));

					//Comprobar si la puntuación puede o no modificarse.
					if(isset($post['puntuacion']))
					{
						if(!$item_evaluable->obtener_cuenta_entradas())
						{
							$datos_modificar['maximo_valor']=$post['puntuacion'];
						}
					}

					$resultado=Herramientas_logica::modificar_item($this->usuario, $item_evaluable, $datos_modificar, $modo, true);

					if($resultado->acc_resultado())
					{
						if($resultado->acc_resultado())
						{
							$resultado->mut_datos(Item_evaluable_vista::mostrar_como_xml($item_evaluable));
						}
					}
				break;

				case 'actualizar_trimestre_grupo':
					//Ya se ha validado que el grupo es nuestro...
					$datos_modificar=Herramientas_logica::obtener_array_datos($post, array('trimestre_activo' => 'trimestre'));
					$resultado=Herramientas_logica::modificar_item($this->usuario, $this->grupo, $datos_modificar, $modo, true);
				break;

			}
		}

		return $resultado;
	}

	public function componer_url(Resultado_logica_redireccion &$l)
	{
		$accion=$l->acc_accion();
		$resultado=$l->acc_resultado();

		switch($accion)
		{
			case 'validar': return Factoria_urls::vista_curso_acceso_no_permitido_a_grupo(); break;
			case 'crear_alumno': return Factoria_urls::resultado_crear_alumno($this->grupo, $resultado); break;
			case 'eliminar_alumno': return Factoria_urls::resultado_eliminar_alumno($this->grupo, $resultado); break;
			case 'crear_evaluable': return Factoria_urls::resultado_crear_evaluable($this->grupo, $resultado); break;
			case 'eliminar_evaluable': return Factoria_urls::resultado_eliminar_evaluable($this->grupo, $resultado); break;
			case 'crear_item_evaluable': return Factoria_urls::resultado_crear_item_evaluable($this->grupo, $resultado); break;
			case 'eliminar_item_evaluable': return Factoria_urls::resultado_eliminar_item_evaluable($this->grupo, $resultado); break;
			default: return Factoria_urls::vista_grupo($this->grupo); break;
		}
	}

	//Este método tiene sentido para poder importar alumnos.
	private function &crear_alumno($datos)
	{
		$alumno=new Alumno();
		$datos_crear=array(
				'nombre' => $datos['nombre'],
				'apellidos' => $datos['apellidos'],
				'texto' => $datos['texto'],
				'id_grupo' => $this->grupo->ID_INSTANCIA(),
				'id_usuario' => $this->usuario->ID_INSTANCIA());

		$resultado=Herramientas_logica::crear_item($alumno, $datos_crear, 'crear_alumno', true);
		if($resultado)
		{
			$resultado->mut_datos(Alumno_vista::mostrar_como_xml($alumno));
		}
		return $resultado;
	}

	private function importar_alumnos_desde_cadena($cadena)
	{
		$lineas=explode("\n", $cadena);
		$total=0;

		foreach($lineas as $clave => $valor)
		{
			if(strpos($valor, ',')===false) continue;
			$valor=str_replace('"', '', $valor);
			$datos=explode(',', $valor);
			foreach($datos as $claved => &$valord) $valord=trim($valord);
		
			$crear_alumno=array(
				'nombre' => $datos[1],
				'apellidos' => $datos[0],
				'texto' => '');
	
			
			$resultado=$this->crear_alumno($crear_alumno);
			if($resultado->acc_resultado()) ++$total;
		}

		return $total;
	}
};
?>
