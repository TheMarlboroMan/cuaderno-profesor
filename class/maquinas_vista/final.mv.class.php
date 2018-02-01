<?
/*Esta máquina se encarga de la pantalla de la nota final de un grupo, 
con las notas finales acumuladas y la posibilidad de poner la última nota.
...*/

class Plugin_vista_final extends Plugin_vista_maquina_web
{
	public function obtener_tipo_vista() {return Plugin_vista_maquina_web::TIPO_VISTA_HTML;}

	const VISTA_COMPLETA=1;
	const VISTA_EVALUABLE=2;
	const VISTA_ITEM=3;

	private $grupo=null;
	private $curso=null;
	private $usuario=null;
	private $evaluable=null;
	private $item_evaluable=null;
	private $trimestre;
	private $tipo_vista=0;
	
	private $array_alumnos=array();
//	private $array_evaluables=array();
//	private $array_items_evaluables=array();

/*	public function mostrar_herramientas()
	{
		return <<<R
<li id="herramienta_del">Eliminar</li>
R;
	}
*/
	public function obtener_array_css()
	{
		$resultado=array('comunes', 'interfaz_usuario', 'final');
		return $resultado;
	}

	public function obtener_array_js()
	{
		$resultado=array('lector_doc', 'lector_XML', 'interfaz_usuario', 'final');
		return $resultado;
	}

	public function &logica_vista($get, $post)
	{
		$this->usuario=&$this->acc_maquina_web()->obtener_usuario();
		$id_grupo=isset($get['id_grupo']) ? $get['id_grupo'] : 0;
		$this->grupo=new Grupo($id_grupo);
	
		if(!$this->grupo->pertenece_a_y_es_valido($this->usuario))
		{
			$resultado=new Resultado_logica_redireccion('defecto', 0);
		}
		else
		{
			$this->tipo_vista=self::VISTA_COMPLETA;

			$c=new Curso();
			$id_curso=$this->grupo->acc_id_curso();
			$this->curso=Cache::obtener_de_cache($c, $id_curso);

			$this->array_alumnos=Alumno::obtener_para_grupo($this->grupo);
	
			$resultado=new Resultado_logica_redireccion('defecto', 1);
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
				return Factoria_urls::vista_tabla_acceso_no_permitido($this->grupo);
			break;
		}
	}

	public function generar_vista()
	{
		$url_volver=Factoria_urls::vista_grupo($this->grupo);

		$CABECERA=$this->generar_cabeceras();
		$CUERPO=$this->generar_cuerpo();
		$OTRAS_TABLAS=$this->tipo_vista==self::VISTA_COMPLETA ? $this->generar_enlaces_otros_trimestres() : null;

		return <<<R

<div id="evaluacion">

	<a href="{$url_volver}" class="iu_enlace_volver">Volver</a>
	
	<h2>{$this->curso->acc_titulo()} :: {$this->grupo->acc_titulo()} :: Nota final</h2>

	{$OTRAS_TABLAS}

	<table id="tabla_final">
		{$CABECERA}
		{$CUERPO}
	</table>

</div>
R;
	}

	public function generar_title(){return "Nota final :: ".$this->grupo->acc_titulo();}

	public function mostrar_plantillas()
	{
		return null;
	}

	/**********************************************************************/

	private function generar_cabeceras()
	{
		return <<<R
	<thead>
		
		<tr>
			<th colspan="2">Informaci&oacute;n</th>
			<th rowspan="2">T1</th>
			<th rowspan="2">T2</th>
			<th rowspan="2">T3</th>
			<th rowspan="2">C&aacute;lculo</th>
			<th rowspan="2">Final</th>
		</tr>
		<tr>
			<th>Nombre</th>
			<th>Observaciones</th>
		</tr>

	</thead>
R;
	
	}

	private function generar_cuerpo()
	{
		$CUERPO=null;

		foreach($this->array_alumnos as $clave => &$alumno)
		{
			$nombre_alumno=$alumno->acc_apellidos().', '.$alumno->acc_nombre();
			$ver_texto=$alumno->acc_texto();

			$VER_NOTAS=null;
			$NOTAS=array();
			$suma_notas=0;
			$i=1;
			while($i <= 3)
			{
				$temp=Nota_final_evaluacion_alumno::obtener_para_alumno_y_trimestre($alumno, $i++);
				if($temp)
				{
					$suma_notas+=$temp->acc_valor();
					$NOTAS[]=$temp;
					$VER_NOTAS.=<<<R
			
			<td>{$temp->acc_valor()}</td>
R;
				}
				else
				{
					$VER_NOTAS.=<<<R
			
			<td class="rojo">?</td>
R;
				}								
			}

			$NOTA_CALCULADA=round($suma_notas / 3, 2);
		
			$CELDA_MEDIA_FINAL=$this->componer_celda_media_final($alumno);

			$CUERPO.=<<<R
		<tr name="alumno" id="fila_alumno_{$alumno->ID_INSTANCIA()}">
			<td>{$nombre_alumno}</td>
			<td class="observaciones">{$ver_texto}</td>
			{$VER_NOTAS}

			<td>{$NOTA_CALCULADA}</td>
			{$CELDA_MEDIA_FINAL}
		</tr>
R;
		}

		return <<<R

	<tbody>
{$CUERPO}
	</tbody>
R;
	}

	private function generar_enlaces_otros_trimestres()
	{
		$enlaces_otras_tablas=null;

		for($i=1; $i<=3; ++$i)
		{
			if($this->grupo->es_evaluable_en_trimestre($i))
			{
				$url=Factoria_urls::vista_tabla_por_trimestre($this->grupo, $i);
				$enlaces_otras_tablas.=<<<R

	<li><a href="{$url}" class="enlace_acceso">Trimestre {$i}</a></li>
R;
			}
		}

		if(strlen($enlaces_otras_tablas))
		{
			return <<<R
<ul class="otras">
	{$enlaces_otras_tablas}
</ul>
R;
		}
		else
		{
			return null;
		}
	}


	private function componer_celda_media_final(Alumno &$alumno)
	{
		$n=Nota_final_evaluacion_alumno::obtener_para_alumno_final($alumno);
		$ti=999999;
	
		$ver_input=$n ? <<<R

	<input class="input_nota" type="text" tabindex="{$ti}" name="nota_final" data-t="actualizar" data-idn="{$n->ID_INSTANCIA()}" value="{$n->acc_valor()}"/>
R
: <<<R

	<input class="input_nota nuevo" type="text" tabindex="{$ti}" name="nota_final" data-t="nuevo" data-ida="{$alumno->ID_INSTANCIA()}" />
R;


			$resultado=<<<R
			<td data-tipocelda="nf">{$ver_input}</td>
R;

		return $resultado;
	}

};
?>
