<?
class Plugin_logica_curso extends Plugin_logica_maquina_web
{
	private $usuario=null;

	public function preparar($get, $post, $files) 
	{
		$this->usuario=&$this->acc_maquina_web()->obtener_usuario();
	}

	public function &logica_control($modo, $get, $post, $files)
	{
		$clave_estado_logica=Maquina_web::CLAVE_ESTADO_LOGICA;

		switch($modo)
		{
			//Cursos.

			case 'crear':	
				$curso=new Curso();

				$datos_crear=Herramientas_logica::obtener_array_datos($post, array('titulo' => 'nombre'));
				$datos_crear['id_usuario']=$this->usuario->ID_INSTANCIA();

				$resultado=Herramientas_logica::crear_item($curso, $datos_crear, $modo, true);
				if($resultado->acc_resultado())
				{
					$resultado->mut_datos(Curso_vista::mostrar_como_xml($curso));
				}
			break;

			case 'modificar':
				$datos_modificar=Herramientas_logica::obtener_array_datos($post, array('titulo' => 'nombre'));

				$id_curso=Herramientas_logica::obtener_id_modificar($post);
				$curso=new Curso($id_curso);

				$resultado=Herramientas_logica::modificar_item($this->usuario, $curso, $datos_modificar, $modo, true);

				if($resultado->acc_resultado())
				{
					$resultado->mut_datos(Curso_vista::mostrar_como_xml($curso));
				}				
			break;

			case 'eliminar':
				$id_curso=Herramientas_logica::obtener_id_eliminar($post);
				$curso=new Curso($id_curso);
				$resultado=Herramientas_logica::eliminar_item($this->usuario, $curso, $modo, isset($get['rxml']));
			break;


			//Grupos...

			case 'crear_grupo':
				$id_curso=isset($post['idc']) ? $post['idc'] : 0;
				$curso=new Curso($id_curso);
	
				//No se pueden crear grupos para cursos que no son de la propiedad.
				if(!$curso->pertenece_a_y_es_valido($this->usuario))
				{
					$resultado=new Resultado_logica_redireccion($modo, 0);
				}
				else
				{
					$datos_crear=Herramientas_logica::obtener_array_datos($post, array(
						'titulo' => 'nombre',
						'max_comportamiento' => 'max_comportamiento',
						'inicio_comportamiento' => 'inicio_comportamiento',
						'porcentaje_comportamiento' => 'porcentaje_comportamiento',
						'color_grupo' => 'color_grupo'));

					$datos_crear['id_curso']=$post['idc'];
					$datos_crear['id_usuario']=$this->usuario->ID_INSTANCIA();

					$grupo=new Grupo();
					$resultado=Herramientas_logica::crear_item($grupo, $datos_crear, $modo, true);

					if($resultado)
					{
						$resultado->mut_datos(Grupo_vista::mostrar_como_xml($grupo));
					}
				}
			break;

			case 'modificar_grupo':

				$datos_modificar=Herramientas_logica::obtener_array_datos($post, array(
					'titulo' => 'nombre',
					'max_comportamiento' => 'max_comportamiento',
					'inicio_comportamiento' => 'inicio_comportamiento',
					'porcentaje_comportamiento' => 'porcentaje_comportamiento',
					'color_grupo' => 'color_grupo'));

				$id_grupo=Herramientas_logica::obtener_id_modificar($post);
				$grupo=new Grupo($id_grupo);

				$resultado=Herramientas_logica::modificar_item($this->usuario, $grupo, $datos_modificar, $modo, true);

				if($resultado->acc_resultado())
				{
					if($resultado)
					{
						$resultado->mut_datos(Grupo_vista::mostrar_como_xml($grupo));
					}
				}
				
			break;

			case 'eliminar_grupo':
				$id_grupo=Herramientas_logica::obtener_id_eliminar($post);
				$grupo=new Grupo($id_grupo);
				$resultado=Herramientas_logica::eliminar_item($this->usuario, $grupo, $modo, isset($get['rxml']));
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
			case 'crear': return Factoria_urls::resultado_crear_curso($resultado); break;
			case 'eliminar': return Factoria_urls::resultado_eliminar_curso($resultado); break;
			case 'crear_grupo': return Factoria_urls::resultado_eliminar_grupo($resultado); break;
			case 'eliminar_grupo': return Factoria_urls::resultado_eliminar_grupo($resultado); break;
			default:
				return Factoria_urls::vista_cursos();
			break;
		}
	}
};
?>
