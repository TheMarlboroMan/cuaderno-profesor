<?
/*Esta máquina se encarga de la pantalla de un grupo, con alumnos y evaluables...*/

class Plugin_vista_grupo extends Plugin_vista_maquina_web
{
	const MODO_NORMAL=0;
	const MODO_XML_ALUMNO=1;
	const MODO_XML_EVALUABLE=2;
	const MODO_XML_ITEM_EVALUABLE=3;
	const MODO_XML_INFORME_ALUMNO=4;

	private $modo=self::MODO_NORMAL;
	private $id_xml=0;

	private $grupo;
	private $curso;
	private $usuario;

	public function obtener_tipo_vista() 
	{
		switch($this->modo)
		{
			case self::MODO_NORMAL: return Plugin_vista_maquina_web::TIPO_VISTA_HTML; break;
			case self::MODO_XML_ALUMNO: 
			case self::MODO_XML_EVALUABLE: 
			case self::MODO_XML_ITEM_EVALUABLE:
			case self::MODO_XML_INFORME_ALUMNO:  
				return Plugin_vista_maquina_web::TIPO_VISTA_XML; break;
		}
	}

	public function generar_title(){return "Grupo :: ".$this->grupo->acc_titulo();}

	public function obtener_array_css()
	{
		$resultado=array('comunes', 'interfaz_usuario', 'grupo');
		return $resultado;
	}

	public function obtener_array_js()
	{
		$resultado=array('lector_doc', 'lector_XML', 'interfaz_usuario', 'form', 'importar_alumnos', 'item_listado', 'alumno', 'evaluable', 'item_evaluable', 'evaluacion');
		return $resultado;
	}

	public function &logica_vista($get, $post)
	{
		if(isset($get['pvc_modo_vista'])) $this->modo=$get['pvc_modo_vista'];
		if(isset($get['id_xml'])) $this->id_xml=$get['id_xml'];

		$id_grupo=isset($get['id_grupo']) ? $get['id_grupo'] : 0;
		$this->grupo=new Grupo($id_grupo);

		$c=new Curso();
		$id_curso=$this->grupo->acc_id_curso();
		$this->curso=Cache::obtener_de_cache($c, $id_curso);

		$this->usuario=&$this->acc_maquina_web()->obtener_usuario();

		$resultado=new Resultado_logica_redireccion(
			'defecto', 
			$this->grupo->pertenece_a_y_es_valido($this->usuario)
		);

		if($resultado->acc_resultado())
		{
			$this->usuario->establecer_curso_actual($this->curso);
		}

		return $resultado;
	}

	public function componer_url(Resultado_logica_redireccion &$l)
	{
		$accion=$l->acc_accion();

		switch($accion)
		{
			case 'defecto': 
			default:
				return Factoria_urls::vista_curso_acceso_no_permitido_a_grupo(); 
			break;
		}
	}

	public function generar_vista()
	{
		switch($this->modo)
		{
			case self::MODO_NORMAL: return $this->generar_vista_html(); break;
			case self::MODO_XML_ALUMNO: return $this->generar_xml_alumno(); break;
			case self::MODO_XML_EVALUABLE: return $this->generar_xml_evaluable(); break;
			case self::MODO_XML_ITEM_EVALUABLE: return $this->generar_xml_item_evaluable(); break;
			case self::MODO_XML_INFORME_ALUMNO: return $this->generar_xml_informe_alumno(); break;
		}
	}

	private function generar_xml_alumno()
	{
		$usuario=$this->acc_maquina_web()->obtener_usuario();
		$alumno=new Alumno($this->id_xml);
		if(!$alumno->pertenece_a_y_es_valido($usuario)) 
		{
			$alumno=new Alumno();
			$resultado=0;
			$mensaje='ERROR: El elemento no pertenece al usuario';
		}
		else
		{
			$resultado=1;
			$mensaje='ok';
		}

		$datos=Alumno_vista::mostrar_como_xml($alumno);
		return Herramientas::respuesta_xml($resultado, $mensaje, $datos);
	}

	private function generar_xml_informe_alumno()
	{
		$usuario=$this->acc_maquina_web()->obtener_usuario();
		$alumno=new Alumno($this->id_xml);
		if(!$alumno->pertenece_a_y_es_valido($usuario)) 
		{
			$resultado=0;
			$mensaje='ERROR: El elemento no pertenece al usuario';
			$contenido=null;
		}
		else
		{

			$informe=new Informe_alumno($this->usuario, $alumno, $this->grupo->acc_trimestre_activo());
			$contenido=$informe->generar_texto();			
	
			$resultado=1;
			$mensaje='ok';
		}

		$resultado=Herramientas::respuesta_xml($resultado, $mensaje, '<![CDATA['.$contenido.']]>');
		return $resultado;
	}
	
	private function generar_xml_evaluable()
	{
		$usuario=$this->acc_maquina_web()->obtener_usuario();
		$evaluable=new Evaluable($this->id_xml);
		if(!$evaluable->pertenece_a_y_es_valido($usuario)) 
		{
			$evaluable=new Evaluable();
			$resultado=0;
			$mensaje='ERROR: El elemento no pertenece al usuario';
		}
		else
		{
			$resultado=1;
			$mensaje='ok';
		}

		$datos=Evaluable_vista::mostrar_como_xml($evaluable);
		return Herramientas::respuesta_xml($resultado, $mensaje, $datos);
	}
	
	private function generar_xml_item_evaluable()
	{
		$usuario=$this->acc_maquina_web()->obtener_usuario();
		$item_evaluable=new Item_evaluable($this->id_xml);
		if(!$item_evaluable->pertenece_a_y_es_valido($usuario)) 
		{
			$item_evaluable=new Item_valuable();
			$resultado=0;
			$mensaje='ERROR: El elemento no pertenece al usuario';
		}
		else
		{
			$resultado=1;
			$mensaje='ok';
		}

		$datos=Item_evaluable_vista::mostrar_como_xml($item_evaluable);
		return Herramientas::respuesta_xml($resultado, $mensaje, $datos);
	}

	private function generar_vista_html()
	{
		$url_volver=Factoria_urls::vista_cursos();

		$CONTENEDOR_FORM_ALUMNOS=Alumno_vista::generar_contenedor_form();
		$FORM_IMPORTAR_ALUMNOS=Alumno_vista::generar_form_importar_alumnos();

		$LISTADO_ALUMNOS=null;
		$alumnos=Alumno::obtener_para_grupo($this->grupo);
		$total_alumnos=count($alumnos);
		foreach($alumnos as $clave => &$valor) $LISTADO_ALUMNOS.=Alumno_vista::mostrar_como_listado($valor);

		$i=1;
		$VER_TABS_TRIMESTRES=null;
		$LISTADOS_EVALUABLES=null;
		while($i <= 3)
		{
			$LISTADOS_EVALUABLES.=$this->generar_evaluable_trimestre($i);

			$marca=$this->grupo->acc_trimestre_activo()==$i ? 'data-uitabactual="1"' : null;
			$VER_TABS_TRIMESTRES.=<<<R

					<li {$marca}>Trimestre {$i}</li>
R;
			++$i;
		}

		return <<<R

<!-- Inicio grupo -->
<div id="grupo">

	<a href="{$url_volver}" class="iu_enlace_volver">Volver</a>

	<h2>{$this->grupo->acc_titulo()} :: [{$this->curso->acc_titulo()}]</h2>

	<div class="columnas">

		<!-- Inicio col1 -->
		<div class="col1">

			{$FORM_IMPORTAR_ALUMNOS}
			
			{$CONTENEDOR_FORM_ALUMNOS}

			<h3>Alumnos ({$total_alumnos})</h3>

			<ol id="listado_alumnos">{$LISTADO_ALUMNOS}</ol>

		</div>
		<!-- Fin col1 -->
	
		<!-- Inicio col2 -->
		<div class="col2">

			<!-- Inicio tabs -->
			<div class="iu_tabs" data-callback="click_tab_evaluacion" >

				<ul class="iu_tabs_encabezados">
					{$VER_TABS_TRIMESTRES}
				</ul>

				<!-- Inicio cuerpo tabs -->
				<div class="iu_tabs_cuerpos">

{$LISTADOS_EVALUABLES}

				</div>
				<!-- Fin cuerpo tabs -->
			</div>
			<!-- Fin tabs -->

		</div>
		<!-- Fin col2 -->
	</div>

</div>
<!-- Fin grupo -->

<input type="hidden" id="idg" value="{$this->grupo->ID_INSTANCIA()}" />
R;
	}

	/**********************************************************************/

	public function mostrar_plantillas() 
	{
		$FORM_ALUMNOS=Alumno_vista::generar_form();
		$FORM_EVALUABLES=Evaluable_vista::generar_form();
		$FORM_ITEMS_EVALUABLES=Item_evaluable_vista::generar_form();

		$dummy_alumno=new Alumno();
		$dummy_evaluable=new Evaluable();
		$dummy_item_evaluable=new Item_evaluable();

		$ITEM_LISTADO_ALUMNO=Alumno_vista::mostrar_como_listado($dummy_alumno);
		$ITEM_LISTADO_EVALUABLE=Evaluable_vista::mostrar_como_listado($dummy_evaluable);
		$ITEM_LISTADO_ITEM_EVALUABLE=Item_evaluable_vista::mostrar_como_listado($dummy_item_evaluable);

		return <<<R
<div id="informe_alumno" class="oculto">
	<p>Informe de alumno</p>
	<pre></pre>
	<textarea></textarea>
	<input type="button" class="iu_input_defecto" value="Cerrar" />
</div>

	{$FORM_ALUMNOS}
	{$FORM_EVALUABLES}
	{$FORM_ITEMS_EVALUABLES}

	{$ITEM_LISTADO_ALUMNO}	
	{$ITEM_LISTADO_EVALUABLE}
	{$ITEM_LISTADO_ITEM_EVALUABLE}
R;
	}


	private function generar_evaluable_trimestre($trimestre)
	{
		$CONTENEDOR_FORM_EVALUABLES=Evaluable_vista::generar_contenedor_form($trimestre);

		$LISTADO_EVALUABLES=null;
		$evaluables=Evaluable::obtener_para_grupo_y_trimestre($this->grupo, $trimestre);
		$porcentaje_total=0;

		foreach($evaluables as $clave => &$valor) 
		{
			$LISTADO_EVALUABLES.=Evaluable_vista::mostrar_como_listado($valor);
			$porcentaje_total+=$valor->acc_porcentaje();
		}

		$porcentaje_comportamiento=$this->grupo->acc_porcentaje_comportamiento();
		$max_comportamiento=$this->grupo->acc_max_comportamiento();
		$inicio_comportamiento=$this->grupo->acc_inicio_comportamiento();
		$porcentaje_total+=$porcentaje_comportamiento;

		//La ficha de acceso sólo aparecerá cuando haya al menos un item
		//que evaluar.

		$ENLACE_FICHA=null;

		if($this->grupo->es_evaluable_en_trimestre($trimestre))
		{
			$url_ficha=Factoria_urls::vista_tabla_por_trimestre($this->grupo, $trimestre);
			$ENLACE_FICHA=<<<R
<a class="enlace_evaluacion" href="{$url_ficha}">Evaluar trimestre</a>
R;
		}

		$clase_total=$porcentaje_total==100 ? 'verde' : 'rojo';

		return <<<R

		<!--trimestre-->
		<div class="trimestre">

			{$ENLACE_FICHA}

			{$CONTENEDOR_FORM_EVALUABLES}

			<ul>
				{$LISTADO_EVALUABLES}
				<li class="comportamiento">Comportamiento ({$porcentaje_comportamiento}%, [{$inicio_comportamiento} / {$max_comportamiento}])</li>
			</ul>

			<b class="total {$clase_total}">Total: <span id="total_{$trimestre}">{$porcentaje_total}</span>%</b>
		</div>
		<!--Fin trimestre-->
R;
	}
};
?>
