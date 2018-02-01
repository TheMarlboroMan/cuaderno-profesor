<?
/*Esta máquina se encarga de la pantalla de cursos y grupos...*/

class Plugin_vista_curso extends Plugin_vista_maquina_web
{
	const MODO_NORMAL=0;
	const MODO_XML_CURSO=1;
	const MODO_XML_GRUPO=2;

	private $modo=self::MODO_NORMAL;
	private $id_xml=0;

	public function obtener_tipo_vista() 
	{
		switch($this->modo)
		{
			case self::MODO_XML_CURSO:
			case self::MODO_XML_GRUPO:
				return Plugin_vista_maquina_web::TIPO_VISTA_XML;
			break;

			default:
				return Plugin_vista_maquina_web::TIPO_VISTA_HTML;
			break;
		}
	}

	public function obtener_array_css()
	{
		$resultado=array('comunes', 'interfaz_usuario', 'curso', 'colores_horario');
		return $resultado;
	}

	public function obtener_array_js()
	{
		$resultado=array('lector_XML', 'lector_doc', 'interfaz_usuario', /*'utilidades_dom',*/ 'form', 'item_listado', 'curso', 'grupo');
		return $resultado;
	}

	//No hay forma de que falle. Todo está ok.
	public function &logica_vista($get, $post)
	{
		if(isset($get['pvc_modo_vista'])) $this->modo=$get['pvc_modo_vista'];
		if(isset($get['id_xml'])) $this->id_xml=$get['id_xml'];

		$resultado=new Resultado_logica_redireccion('defecto', 1);
		return $resultado;
	}

	public function generar_vista()
	{
		switch($this->modo)
		{
			case self::MODO_XML_CURSO:
				return $this->generar_xml_curso();
			break;

			case self::MODO_XML_GRUPO:
				return $this->generar_xml_grupo();
			break;

			default:
				return $this->generar_vista_normal();
			break;
		}
	}

	public function generar_title(){return "Cursos y grupos";}

	/**********************************************************************/

	private function generar_xml_curso()
	{
		$usuario=$this->acc_maquina_web()->obtener_usuario();
		$curso=new Curso($this->id_xml);
		if(!$curso->pertenece_a_y_es_valido($usuario)) 
		{
			$curso=new Curso();
			$resultado=0;
			$mensaje='ERROR: El curso no es de propiedad del usuario';
		}
		else
		{
			$resultado=1;
			$mensaje='ok';
		}

		$datos=Curso_vista::mostrar_como_xml($curso);
		return Herramientas::respuesta_xml($resultado, $mensaje, $datos);
	}

	private function generar_xml_grupo()
	{
		$usuario=$this->acc_maquina_web()->obtener_usuario();
		$grupo=new Grupo($this->id_xml);
		if(!$grupo->pertenece_a_y_es_valido($usuario)) 
		{
			$grupo=new Grupo();
			$resultado=0;
			$mensaje='ERROR: El grupo no es de propiedad del usuario';
		}
		else
		{
			$resultado=1;
			$mensaje='ok';
		}

		$datos=Grupo_vista::mostrar_como_xml($grupo);
		return Herramientas::respuesta_xml($resultado, $mensaje, $datos);
	}

	private function generar_vista_normal()
	{
		$usuario=$this->acc_maquina_web()->obtener_usuario();
		$cursos=Curso::obtener_para_usuario($usuario);

		$CONTENEDOR_FORM_CURSOS=Curso_vista::generar_contenedor_form();

		$usuario=$this->acc_maquina_web()->obtener_usuario();

		$LISTADO_CURSOS=null;
		foreach($cursos as $clave => &$valor) 
		{

			$grupos=Grupo::obtener_para_usuario_y_curso($usuario, $valor);
			$LISTADO_CURSOS.=Curso_vista::mostrar_como_listado($valor, $grupos);
		}

		return <<<R

<div id="cursos">

	{$CONTENEDOR_FORM_CURSOS}

	<ul id="listado_cursos">
		{$LISTADO_CURSOS}
	</ul>

</div>
R;
	}

	public function mostrar_plantillas() 
	{
		$dummy_curso=new Curso();
		$dummy_grupo=new Grupo();
		$dummy_array=array();

		$FORM_CURSOS=Curso_vista::generar_form();
		$ITEM_LISTADO_CURSO=Curso_vista::mostrar_como_listado($dummy_curso, $dummy_array);

		$FORM_GRUPOS=Grupo_vista::generar_form();
		$ITEM_LISTADO_GRUPO=Grupo_vista::mostrar_como_listado($dummy_grupo);
		

		return <<<R
	{$FORM_CURSOS}
	{$ITEM_LISTADO_CURSO}

	{$FORM_GRUPOS}
	{$ITEM_LISTADO_GRUPO}
R;
	}
};
?>
