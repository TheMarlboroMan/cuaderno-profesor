<?
class Plugin_vista_horario extends Plugin_vista_maquina_web
{
	public function obtener_tipo_vista() {return Plugin_vista_maquina_web::TIPO_VISTA_HTML;}

	private $curso=null;
	private $usuario=null;

	public function mostrar_herramientas() {return null;}

	public function obtener_array_css()
	{
		$resultado=array('comunes', 'interfaz_usuario', 'horario', 'colores_horario');
		return $resultado;
	}

	public function obtener_array_js()
	{
		$resultado=array('lector_doc', 'lector_XML', 'interfaz_usuario', 'form', 'horario');
		return $resultado;
	}

	public function &logica_vista($get, $post)
	{
		$this->usuario=&$this->acc_maquina_web()->obtener_usuario();
		$id_curso=isset($get['id_curso']) ? $get['id_curso'] : 0;
		$this->curso=new Curso($id_curso);

		if(!$this->curso->pertenece_a_y_es_valido($this->usuario) || !$this->usuario->establecer_curso_actual($this->curso))
		{
			$resultado=new Resultado_logica_redireccion('defecto', 0);
		}
		else
		{
			
			$resultado=new Resultado_logica_redireccion('defecto', 1);
		}

		return $resultado;
	}

	public function componer_url(Resultado_logica_redireccion &$l)
	{
		return Factoria_urls::vista_horario_acceso_no_permitido();
	}


	public function generar_vista()
	{
		$url_volver=Factoria_urls::vista_cursos();
	
		$VER_TABLA=$this->generar_tabla();
		$VER_CONFIG=$this->generar_config();
		$VER_FIN_CONFIG=$this->generar_fin_config();

		$AVISO_CONFIGURACION=null;		
		if(!$this->curso->es_horario_configurado() && !$this->curso->es_configurando_horario())
		{
			$AVISO_CONFIGURACION=<<<R
	<div class="no_config">		
		A&uacute;n no has configurado tu horario para este curso.
		Para empezar, haz click en el bot&oacute;n 
		&quot;Iniciar configuraci&oacute;n&quot;.
	</div>
R;
		}

		return <<<R

<div id="horario">

	<a href="{$url_volver}" class="iu_enlace_volver">Ir a cursos</a>
	
	<h2>{$this->curso->acc_titulo()} :: Horarios</h2>

	{$AVISO_CONFIGURACION}

	{$VER_CONFIG}

	{$VER_TABLA}

	{$VER_FIN_CONFIG}
	

</div>
R;

//<input type="hidden" name="idc" value="{$this->curso->ID_INSTANCIA()}" />
	}

	public function generar_title(){return "Horario :: ".$this->curso->acc_titulo();}

	public function mostrar_plantillas()
	{
		$SELECTOR=$this->generar_selector_contenido();
		$FORM_FRANJAS=$this->generar_form_franjas();

		return <<<R

{$FORM_FRANJAS}
{$SELECTOR}

R;
	}

	/**********************************************************************/

	private function generar_config()
	{
		$clave_estado_logica=Maquina_web::CLAVE_ESTADO_LOGICA;

		if($this->curso->es_configurando_horario())
		{
			$franjas=$this->curso->acc_franjas_horario();

			if(!$franjas)
			{
				$VER_AVISO=<<<R
	<div class="no_config">		
		Para empezar a configurar tu horario debes indicar la cantidad
		de franjas horarias que desees. Rellena el formulario indicando
		las franjas que deseas y haz click en &quot;Guardar&quot;.
	</div>
R;
			}
			else
			{
				$VER_AVISO=<<<R
	<div class="no_config">		
		Haz click sobre una franja horaria para darle un nombre (por
		ejemplo &quot;8:30 - 9:30&quot;. Haz click sobre cualquier
		celda del horario para seleccionar el grupo o actividad
		que corresponda. Cuando hayas finalizado haz click en
		&quot;Finalizar configuraci&oacute;n&quot; en la parte inferior
		de la p&aacute;fina.
	</div>
R;
			}
			

			return <<<R

			{$VER_AVISO}

		<form class="iu_form" method="post" action="" id="form_config_horario" onsubmit="return false;" >
			<input type="hidden" name="{$clave_estado_logica}" value="configurar_horario" />
			<input type="hidden" name="idc" value="{$this->curso->ID_INSTANCIA()}" />
	
			<dl>
				<dt>Franjas en horario (max. 10):</dt>
				<dd><input type="text" name="franjas" value="{$franjas}" /></dd>
			</dl>

			<dl>			
				<input type="button" name="btn_ok" value="Guardar" />
			</dl>

			<p class="iu_aviso">
			Establecer una cantidad de franjas menor 
			que la actual causar&aacute; el borrado de la informaci&oacute;n
			de horario sobrante.
			</p>
		</form>
R;
		}
		else
		{
			return <<<R
		<form class="iu_form" method="post" action="" id="form_iniciar_config_horario" >
			<input type="hidden" name="{$clave_estado_logica}" value="iniciar_configuracion_horario" />
			<input type="hidden" name="idc" value="{$this->curso->ID_INSTANCIA()}" />
	
			<dl>			
				<input type="submit" name="btn_ok" value="Iniciar configuraci&oacute;n" />
			</dl>
		</form>
R;
		}
	}

	private function generar_tabla()
	{
		$total_franjas=$this->curso->acc_franjas_horario();

		if(!$total_franjas)
		{
			return null;
		}
		else
		{
			$franjas=Horario_franja::obtener_array_para_curso($this->curso);
			$contenidos=Horario_contenido::obtener_array_para_curso($this->curso);
		
			$DF=new Despachador_franjas($franjas);
			$DC=new Despachador_contenidos($contenidos);

			$configurando=$this->curso->es_configurando_horario();

			$CUERPO=null;

			$f=1;
			while($f <= $total_franjas)
			{
				$fila=null;
				$fila.=self::componer_franja($DF->obtener($f), $configurando);
				$d=Horario_contenido::LUNES;
				while($d <= Horario_contenido::VIERNES)
				{
					$contenido=$DC->obtener($d, $f);
					if(!$contenido)
					{
						die('ERROR EN '.$d.' '.$f);
					}	

					$fila.=self::componer_contenido($contenido, $configurando);
					++$d;
				}

				++$f;

				$CUERPO.=<<<R

			<tr>
{$fila}
			</tr>
R;
			}

			$horario_configurable=$this->curso->es_configurando_horario() ? '1' : '0';
			$clase_tabla=$this->curso->es_configurando_horario() ? 'configurable' : 'visitable';

			return <<<R
	<table id="tabla_horario" class="{$clase_tabla}" data-configurable="{$horario_configurable}">
		<thead>
			<th>Hora</th>
			<th>Lunes</th>
			<th>Martes</th>
			<th>Mi&eacute;rcoles</th>
			<th>Jueves</th>
			<th>Viernes</th>
		</thead>
		<tbody>
{$CUERPO}
		</tbody>
	</table>
R;
		}
	}

	private function componer_franja(Horario_franja &$f, $configurando)
	{
		if($configurando)
		{
			return <<<R

				<td class="franja" data-tipo="f" data-id="{$f->ID_INSTANCIA()}" >{$f->acc_titulo()}</td>
R;
		}
		else
		{
			return <<<R
				<td class="franja">{$f->acc_titulo()}</td>
R;
		}
	}

	private function componer_contenido(Horario_contenido &$c, $configurando)
	{
		$color=$c->obtener_clase_color();

		if($configurando)
		{
			return <<<R

				<td class="contenido {$color}" data-tipo="c" data-id="{$c->ID_INSTANCIA()}" >{$c->traducir()}</td>
R;
		}
		else
		{
			$url=$c->obtener_url();
			$click=strlen($url) ? "ir_a('".$url."')" : null;
			return <<<R
				<td class="contenido {$color}" onclick="{$click}" >{$c->traducir()}</td>
R;
		}
	}

	private function generar_selector_contenido()
	{
		$VER_ACTIVIDADES=null;
		$i=1;
		while($i < Horario_contenido::MAX_TIPO_COMUN)
		{
			$traduccion=Horario_contenido::traducir_tipo_comun($i);
	
			$VER_ACTIVIDADES.=<<<R

				<li class="color_actividad_{$i}" data-val="{$i}">{$traduccion}</li>
R;

			++$i;
		}

		$grupos=Grupo::obtener_para_usuario_y_curso($this->usuario, $this->curso);

		$VER_GRUPOS=null;
		foreach($grupos as $clave => &$valor)
		{
			$VER_GRUPOS.=<<<R
				
				<li class="color_grupo_{$valor->acc_color_grupo()}" data-val="{$valor->ID_INSTANCIA()}" >{$valor->acc_titulo()}</li>
R;
		}

		$clave_estado_logica=Maquina_web::CLAVE_ESTADO_LOGICA;

		return <<<R

	<div id="selector_contenido">

		<form id="form_selector_contenido" class="oculto" onsubmit="return false;">
			<input type="hidden" name="{$clave_estado_logica}" value="actualizar_contenido" />
			<input type="hidden" name="idcon" value="" />
			<input type="hidden" name="tipcon" value="" />
			<input type="hidden" name="valcon" value="" />
			<input type="hidden" name="idc" value="{$this->curso->ID_INSTANCIA()}" />
		</form>

		<p>Seleccionar un grupo o actividad</p>

		<div class="columnas">

			<div class="col1">
				<h3>Grupos</h3>
				<ul id="lista_grupos">
	{$VER_GRUPOS}
				</ul>
			</div>
			<div class="col2">
				<h3>Actividades</h3>
				<ul id="lista_actividades">
	{$VER_ACTIVIDADES}
				</ul>
			</div>

		</div>

		<input type="button" class="iu_input_defecto" value="Cerrar" />

	</div>
R;
	}

	private function generar_form_franjas()
	{
		$clave_estado_logica=Maquina_web::CLAVE_ESTADO_LOGICA;

		return <<<R
	<div id="contenedor_form_franja">

		<p>Nombre para la franja</p>

		<form id="form_franja" class="iu_form" onsubmit="return false;">
			<input type="hidden" name="{$clave_estado_logica}" value="actualizar_franja" />
			<input type="hidden" name="idf" value="" />
			<input type="hidden" name="idc" value="{$this->curso->ID_INSTANCIA()}" />

			<dl>
				<dt>Nombre:</dt>
				<dd><input type="text" name="titulo" value="" /></dd>
			</dl>

			<dl>
				<input type="button" name="btn_ok" value="Guardar" />
				<input type="button" name="btn_cancelar" value="Cancelar" />
			</dl>
		</form>
	</div>
R;
	}

	private function generar_fin_config()
	{
		if(!$this->curso->es_configurando_horario())
		{
			return null;
		}
		else
		{
			$clave_estado_logica=Maquina_web::CLAVE_ESTADO_LOGICA;

			return <<<R
		<form class="iu_form" method="post" action="" id="form_finalizar_config_horario" >
			<input type="hidden" name="{$clave_estado_logica}" value="finalizar_configuracion_horario" />
			<input type="hidden" name="idc" value="{$this->curso->ID_INSTANCIA()}" />
	
			<dl>			
				<input type="submit" name="btn_ok" value="Finalizar configuraci&oacute;n" />
			</dl>
		</form>
R;
		}
	}
};

class Despachador_franjas
{
	private $franjas=array();

	public function __construct(array $a)
	{
		foreach($a as $clave => &$valor) $this->franjas[$valor->acc_posicion()]=&$valor;
	}

	public function &obtener($f) {return $this->franjas[$f];}
}

class Despachador_contenidos
{
	private $contenidos=array();

	public function __construct(array $a)
	{
		foreach($a as $clave => &$valor) 
		{
			$dia=$valor->acc_dia();

			if(!isset($this->contenidos[$dia])) $this->contenidos[$dia]=array();
			$this->contenidos[$dia][$valor->acc_posicion()]=&$valor;
		}
	}

	public function &obtener($d, $f) {return $this->contenidos[$d][$f];}
}

?>
