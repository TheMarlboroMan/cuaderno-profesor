<?
class Plugin_vista_tabla extends Plugin_vista_maquina_web
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
	private $array_evaluables=array();
	private $array_items_evaluables=array();

	public function mostrar_herramientas()
	{
		return <<<R
<li id="herramienta_del">Eliminar</li>
R;
	}

	public function obtener_array_css()
	{
		$resultado=array('comunes', 'interfaz_usuario', 'tabla');
		return $resultado;
	}

	public function obtener_array_js()
	{
		$resultado=array('lector_doc', 'lector_XML', 'interfaz_usuario', 'tabla_datos');
		return $resultado;
	}

	public function &logica_vista($get, $post)
	{
		$this->usuario=&$this->acc_maquina_web()->obtener_usuario();
		$this->trimestre=isset($get['trimestre']) ? $get['trimestre'] : 0;
		$id_grupo=isset($get['id_grupo']) ? $get['id_grupo'] : 0;
		$this->grupo=new Grupo($id_grupo);

		if($this->trimestre < 1 || $this->trimestre > 3)
		{
			$resultado=new Resultado_logica_redireccion('defecto', 0);
		}
		else if(!$this->grupo->es_evaluable_en_trimestre($this->trimestre))
		{
			$resultado=new Resultado_logica_redireccion('defecto', 0);
		}
		else
		{
			if(!$this->grupo->pertenece_a_y_es_valido($this->usuario))
			{
				$resultado=new Resultado_logica_redireccion('defecto', 0);
			}
			else
			{
				//Si traemos un tipo de evaluable se carga un array.		
				//Si no lo traemos se cargan todos. Lo mismo con el 
				//item evaluable per se.

				$id_evaluable=isset($get['id_evaluable']) ? $get['id_evaluable'] : null;
				$id_item_evaluable=isset($get['id_item_evaluable']) ? $get['id_item_evaluable'] : null;
					
				if($id_evaluable)
				{
					$this->evaluable=new Evaluable($id_evaluable);
					if(!$this->evaluable->pertenece_a_y_es_valido($this->usuario) || $this->evaluable->acc_trimestre()!=$this->trimestre)
					{
						$resultado=new Resultado_logica_redireccion('defecto', 0);
					}
					else
					{
						$this->tipo_vista=self::VISTA_EVALUABLE;
						$this->array_evaluables[]=&$this->evaluable;
					}
				}
				else
				{
					$this->tipo_vista=self::VISTA_COMPLETA;
					$this->array_evaluables=Evaluable::obtener_para_grupo_y_trimestre($this->grupo, $this->trimestre);				
				}


				if($id_item_evaluable && $this->evaluable)
				{
					$this->item_evaluable=new Item_evaluable($id_item_evaluable);

					if(!$this->item_evaluable->pertenece_a_y_es_valido($this->usuario) || $this->item_evaluable->acc_id_evaluable()!=$this->evaluable->ID_INSTANCIA())
					{
						$resultado=new Resultado_logica_redireccion('defecto', 0);
					}
					else
					{
						$this->tipo_vista=self::VISTA_ITEM;
						$this->array_items_evaluables[0][]=&$this->item_evaluable;
					}

				}
				else
				{
					foreach($this->array_evaluables as $clave => &$valor)
						$this->array_items_evaluables[$clave]=Item_evaluable::Obtener_para_evaluable($valor);
				}

				$c=new Curso();
				$id_curso=$this->grupo->acc_id_curso();
				$this->curso=Cache::obtener_de_cache($c, $id_curso);

				$this->array_alumnos=Alumno::obtener_para_grupo($this->grupo);
			
				$resultado=new Resultado_logica_redireccion('defecto', 1);
			}
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

		$otros_encabezados=null;
		if($this->evaluable) $otros_encabezados.=' :: '.$this->evaluable->acc_titulo();
		if($this->item_evaluable) $otros_encabezados.=' :: '.$this->item_evaluable->acc_titulo();

		return <<<R

<div id="evaluacion">

	<a href="{$url_volver}" class="iu_enlace_volver">Volver</a>
	
	<h2>{$this->curso->acc_titulo()} :: {$this->grupo->acc_titulo()} :: Trimestre {$this->trimestre}{$otros_encabezados}</h2>

	{$OTRAS_TABLAS}

	<table id="tabla_datos" data-trimestre="{$this->trimestre}">
		{$CABECERA}
		{$CUERPO}
	</table>

</div>
R;
	}

	public function generar_title(){return "Tabla :: ".$this->grupo->acc_titulo();}

	public function mostrar_plantillas()
	{
		return <<<R

	<form id="form_comportamiento" onsubmit="return false;">
		<p>Anotaci&oacute;n de comportamiento para <span id="form_comportamiento_nombre_alumno"></span></p>
		<input type="hidden" name="id_comportamiento" value="" />
		<input type="hidden" name="multiplicador" value="" />

		<dl>
			<dt>Puntos:</dt>
			<dd><input type="text" name="valor" value="1" /></dd>
		</dl>

		<dl>
			<dt>Motivo:</dt>
			<dd><textarea name="anotacion"></textarea></dd>
		</dl>

		<dl>
			<input type="button" name="btn_ok" value="Guardar" />
			<input type="button" name="btn_cancelar" value="Cancelar" />
		</dl>
	</form>
R;
	}

	/**********************************************************************/

	private function generar_cabeceras()
	{
		$CABECERA_ITEMS_EVALUABLES=null;
		$CABECERA_GRUPOS_EVALUABLES=null;

		foreach($this->array_evaluables as $clave => &$valor)
		{
			$total_items=count($this->array_items_evaluables[$clave]);
			if($total_items)
			{
				$colspan=$total_items+1;

				$CABECERA_GRUPOS_EVALUABLES.=<<<R

			<th colspan="{$colspan}">{$valor->acc_titulo()} ({$valor->acc_porcentaje()}%)</th>
R;

				foreach($this->array_items_evaluables[$clave] as $clave_it => &$item_evaluable)
				{
					$CABECERA_ITEMS_EVALUABLES.=<<<R

			<th>{$item_evaluable->acc_titulo()} ({$item_evaluable->acc_maximo_valor()})</th>
R;
				}

				if($this->tipo_vista!=self::VISTA_ITEM)
				{
					$CABECERA_ITEMS_EVALUABLES.=<<<R
			<th>MEDIA</th>
R;
				}
			}
		}

		$CABECERA_MEDIA_COMPLETA=null;
		if($this->tipo_vista==self::VISTA_COMPLETA)
		{
			$CABECERA_MEDIA_COMPLETA=<<<R
			<th rowspan="2">Totales</th>
			<th rowspan="2">Nota</th>
R;
		}

		return <<<R
	<thead>
		
		<tr>
			<th colspan="2">Informaci&oacute;n</th>
			<th rowspan="2">Comportamiento ({$this->grupo->acc_porcentaje_comportamiento()}%)</th>
			{$CABECERA_GRUPOS_EVALUABLES}
			{$CABECERA_MEDIA_COMPLETA}
		</tr>
		<tr>
			<th>Nombre</th>
			<th>Observaciones</th>
			{$CABECERA_ITEMS_EVALUABLES}
		</tr>

	</thead>
R;
	
	}

	private function generar_cuerpo()
	{
		$CUERPO=null;

		$x=0;
		$y=0;
		$total_alumnos=count($this->array_alumnos);

		foreach($this->array_alumnos as $clave => &$alumno)
		{
			$ficha_alumno=null;
			foreach($this->array_evaluables as $clave_ev => &$evaluable)
			{
				$total_items=count($this->array_items_evaluables[$clave_ev]);
				$calculo_media=0;

				if($total_items)
				{
					foreach($this->array_items_evaluables[$clave_ev] as $clave_it => &$item_evaluable)
					{
						$entrada=&Dato_evaluacion_alumno::obtener($this->usuario, $alumno, $item_evaluable, $this->trimestre);
						

						$tabindex=($x * $total_alumnos) + $y;

						$ficha_alumno.=!$entrada ? 
							$this->input_sin_contestar($alumno, $item_evaluable, $tabindex) : 
							$this->input_contestado($entrada, $item_evaluable->acc_maximo_valor(), $tabindex);


						$calculo_media+=!$entrada ? 0 : $entrada->acc_valor();

						++$x;
					}

					if($this->tipo_vista!=self::VISTA_ITEM)
					{
						$media=$calculo_media / $total_items;
						$ficha_alumno.=$this->media_grupo($media, $evaluable->acc_porcentaje());
					}
				}

			}
			
			$x=0;
			++$y;

			$CELDA_MEDIA_FINAL=$this->componer_celda_media_final($alumno);
			$ver_texto=$alumno->acc_texto();

			$comportamiento_alumno=null;

			try
			{
				$comportamiento_alumno=Comportamiento_alumno::obtener_para_alumno_y_trimestre($alumno, $this->trimestre);
				if(!$comportamiento_alumno) $comportamiento_alumno=Comportamiento_alumno::generar_para_alumno_y_trimestre($alumno, $this->trimestre);
			}
			catch(Excepcion_consulta_mysql $e) {}

			$comportamiento_actual=!$comportamiento_alumno ? 0 : $comportamiento_alumno->acc_valor();				
			$id_comportamiento_actual=!$comportamiento_alumno ? 0 : $comportamiento_alumno->ID_INSTANCIA();				
			$nombre_alumno=$alumno->acc_apellidos().', '.$alumno->acc_nombre();
			$porcentaje_comportamiento=$this->grupo->acc_porcentaje_comportamiento();
			$max_comportamiento=$this->grupo->acc_max_comportamiento();
			$media_comportamiento=$comportamiento_alumno ? $comportamiento_alumno->calcular_media() : 0;

//				<!--<span class="media_comportamiento">{$media_comportamiento}</span>-->

			$CUERPO.=<<<R
		<tr name="alumno" id="fila_alumno_{$alumno->ID_INSTANCIA()}">
			<td>{$nombre_alumno}</td>
			<td class="observaciones">{$ver_texto}</td>
			<td data-tipocelda="c" data-porcentaje="{$porcentaje_comportamiento}" data-id="{$id_comportamiento_actual}" data-nombrealumno="{$nombre_alumno}" data-valorinicial="{$comportamiento_actual}" >
				<span>{$comportamiento_actual}</span> / <span>{$max_comportamiento}</span> 
				<input type="button" class="btn_positivo" value="+" />
				<input type="button" class="btn_negativo" value="-" />
			</td>
			{$ficha_alumno}
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

	private function input_sin_contestar(Alumno &$a, Item_evaluable &$i, $ti)
	{
		$max_valoracion=$i->acc_maximo_valor();

		return <<<R

			<td data-tipocelda="g">
				<input class="input_nota nuevo" type="text" tabindex="{$ti}" name="dato" data-t="nuevo" data-ida="{$a->ID_INSTANCIA()}" data-idi="{$i->ID_INSTANCIA()}" data-maxvaloracion="{$max_valoracion}" />
			</td>
R;
	}

	private function input_contestado(Dato_evaluacion_alumno &$d, $max_valoracion, $ti)
	{
		return <<<R
			
			<td data-tipocelda="g">
				<input class="input_nota" type="text" tabindex="{$ti}" name="dato" data-t="actualizar" data-idd="{$d->ID_INSTANCIA()}" data-maxvaloracion="{$max_valoracion}" value="{$d->acc_valor()}"/>
			</td>
R;
	}

	private function media_grupo($media, $porcentaje)
	{
		return <<<R

			<td data-tipocelda="m" data-porcentaje="{$porcentaje}">
				<span name="media">{$media}</span>
			</td>
R;
	}

	private function generar_enlaces_otros_trimestres()
	{
		$enlaces_otras_tablas=null;

		for($i=1; $i<=3; ++$i)
		{
			if($i!=$this->trimestre)
			{
				if($this->grupo->es_evaluable_en_trimestre($i))
				{
					$url=Factoria_urls::vista_tabla_por_trimestre($this->grupo, $i);
					$enlaces_otras_tablas.=<<<R

	<li><a href="{$url}" class="enlace_acceso">Trimestre {$i}</a></li>
R;
				}
			}
		}

		$url=Factoria_urls::vista_final($this->grupo);

		$enlaces_otras_tablas.=<<<R

	<li><a href="{$url}" class="enlace_acceso">Final</a></li>
R;


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
		$resultado=null;
		if($this->tipo_vista==self::VISTA_COMPLETA)
		{
			$n=Nota_final_evaluacion_alumno::obtener_para_alumno_y_trimestre($alumno, $this->trimestre);
			$ti=999999;
	
			$ver_input=$n ? <<<R

	<input class="input_nota" type="text" tabindex="{$ti}" name="nota_final" data-t="actualizar" data-idn="{$n->ID_INSTANCIA()}" value="{$n->acc_valor()}"/>
R
: <<<R

	<input class="input_nota nuevo" type="text" tabindex="{$ti}" name="nota_final" data-t="nuevo" data-ida="{$alumno->ID_INSTANCIA()}" data-tr="{$this->trimestre}" />
R;


			$resultado=<<<R
			<td data-tipocelda="mf"> - </td>
			<td data-tipocelda="ms">{$ver_input}</td>
R;
		}

		return $resultado;
	}
};
?>
