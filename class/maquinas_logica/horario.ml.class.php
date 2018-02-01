<?
class Plugin_logica_horario extends Plugin_logica_maquina_web
{
	private $usuario;
	private $curso=null;

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
			case 'configurar_horario':	
				$resultado=$this->configurar_horario($post);
			break;
				
			case 'actualizar_franja':
				$resultado=$this->actualizar_franja($post);
			break;

			case 'actualizar_contenido':
				$resultado=$this->actualizar_contenido($post);
			break;

			case 'iniciar_configuracion_horario':
				$resultado=$this->iniciar_configuracion_horario($post);
			break;

			case 'finalizar_configuracion_horario':
				$resultado=$this->finalizar_configuracion_horario($post);
			break;

			default:
				$resultado=new Resultado_logica_xml('defecto', 0, 'Error desconocido');
			break;
		}

		return $resultado;
	}

	public function componer_url(Resultado_logica_redireccion &$l)
	{
		$accion=$l->acc_accion();
		$resultado=$l->acc_resultado();

		switch($accion)
		{
			case 'finalizar_configuracion_horario': 
			case 'iniciar_configuracion_horario': 
			case 'configurar_horario': 
				default: return Factoria_urls::vista_horario($this->curso); break;
		}
	}

	/***************/

	private function &configurar_horario($post)
	{
		$id_curso=isset($post['idc']) ? $post['idc'] : 0;
		$franjas=isset($post['franjas']) ? $post['franjas'] : 0;
		$this->curso=new Curso($id_curso);

		//El valor de franjas lo calculamos para comprobarlo. Más abajo se extrae de nuevo.
		if(!is_numeric($franjas) || $franjas <= 0 || $franjas > 10)
		{
			$resultado=new Resultado_logica_redireccion('configurar_horario', 0);
		}
		else
		{
			$franjas_anteriores=$this->curso->acc_franjas_horario();
			$datos_modificar=Herramientas_logica::obtener_array_datos($post, array('franjas_horario' => 'franjas'));
			$resultado=Herramientas_logica::modificar_item($this->usuario, $this->curso, $datos_modificar, 'configurar_horario', false);

			if($resultado->acc_resultado())
			{
				$this->curso->actualizar_franjas_horario($franjas_anteriores);
			}

		}

		return $resultado;
	}
	
	private function &actualizar_franja($post)
	{
		$id_curso=isset($post['idc']) ? $post['idc'] : 0;
		$id_franja=isset($post['idf']) ? $post['idf'] : 0;

		$this->curso=new Curso($id_curso);
		$franja=new Horario_franja($id_franja);

		if(!$this->curso->pertenece_a_y_es_valido($this->usuario))
		{
			$resultado=new Resultado_logica_xml('actualizar_franja', 0, 'No se ha podido finalizar el proceso por un conflicto de propiedad');
		}
		else if($franja->acc_id_curso() != $this->curso->ID_INSTANCIA())
		{
			$resultado=new Resultado_logica_xml('actualizar_franja', 0, 'Los datos de entrada no son correctos');
		}
		else
		{
			$datos_modificar=Herramientas_logica::obtener_array_datos($post, array('titulo' => 'titulo'));
			$resultado=Herramientas_logica::modificar_item($this->usuario, $franja, $datos_modificar, 'actualizar_franja', true);

			if($resultado->acc_resultado())
			{
				$resultado->mut_datos($franja->acc_titulo());
			}
		}

		return $resultado;
	}
	
	private function &actualizar_contenido($post)
	{
		$id_curso=isset($post['idc']) ? $post['idc'] : 0;
		$id_contenido=isset($post['idcon']) ? $post['idcon'] : 0;

		$this->curso=new Curso($id_curso);
		$contenido=new Horario_contenido($id_contenido);

		if(!$this->curso->pertenece_a_y_es_valido($this->usuario))
		{
			$resultado=new Resultado_logica_xml('actualizar_contenido', 0, 'No se ha podido finalizar el proceso por un conflicto de propiedad');
		}
		else if($contenido->acc_id_curso() != $this->curso->ID_INSTANCIA())
		{
			$resultado=new Resultado_logica_xml('actualizar_contenido', 0, 'Los datos de entrada no son correctos');
		}
		else
		{
			$datos_modificar=Herramientas_logica::obtener_array_datos($post, array(
				'tipo' => 'tipcon',
				'id_contenido' => 'valcon'));
			$resultado=Herramientas_logica::modificar_item($this->usuario, $contenido, $datos_modificar, 'actualizar_contenido', true);

			if($resultado->acc_resultado())
			{	

				$tr=html_entity_decode($contenido->traducir());
				$ver_traduccion='<![CDATA['.$tr.']]>';

				$resultado->mut_att_datos('clr="'.$contenido->obtener_clase_color().'"');
				$resultado->mut_datos($ver_traduccion);
			}
		}

		return $resultado;
	}

	private function iniciar_configuracion_horario($post)
	{
		$id_curso=isset($post['idc']) ? $post['idc'] : 0;
		$this->curso=new Curso($id_curso);
		if(!$this->curso->pertenece_a_y_es_valido($this->usuario))
		{
			$resultado=new Resultado_logica_redireccion('iniciar_configuracion_horario', 0);
		}
		else
		{
			$resultado_accion=$this->curso->iniciar_configuracion_horario();
			$resultado=new Resultado_logica_redireccion('iniciar_configuracion_horario', $resultado_accion);
		}

		return $resultado;
	}

	private function finalizar_configuracion_horario($post)
	{
		$id_curso=isset($post['idc']) ? $post['idc'] : 0;
		$this->curso=new Curso($id_curso);
		if(!$this->curso->pertenece_a_y_es_valido($this->usuario))
		{
			$resultado=new Resultado_logica_redireccion('finalizar_configuracion_horario', 0);
		}
		else
		{
			$resultado_accion=$this->curso->finalizar_configuracion_horario();
			$resultado=new Resultado_logica_redireccion('finalizar_configuracion_horario', $resultado_accion);
		}

		return $resultado;
	}
};
?>
