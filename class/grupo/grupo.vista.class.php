<?
abstract class Grupo_vista
{
	public static function generar_contenedor_form(Curso &$c)
	{
		$id_curso=$c->ID_INSTANCIA();

		$plegado=new IU_plegado(2, $id_curso);
		$plegado->establecer_clase('form_grupo');
		$plegado->establecer_valor_boton('+ Grupo');
		$plegado->establecer_auto_montado($id_curso);
		return $plegado->mostrar();
	}

	public static function generar_form()
	{
		$clave_estado_logica=Maquina_web::CLAVE_ESTADO_LOGICA;

		$opciones_color=null;
		$i=1;
		while($i <= 8)
		{
			$opciones_color.='<option value="'.$i.'" class="color_grupo_'.$i.'">Esquema color '.$i.'</option>';
			++$i;
		}

		return <<<R
		<form class="iu_form" method="post" action="" id="form_grupo" onsubmit="return false">
			<input type="hidden" name="{$clave_estado_logica}" value="" />

			<input type="hidden" name="idc" value="" />

			<dl>
				<dt>Grupo:</dt>
				<dd><input type="text" name="nombre" value="" /></dd>
			</dl>
			<dl>
				<dt>Porcentaje comportamiento:</dt>
				<dd><input type="text" name="porcentaje_comportamiento" value="" /></dd>
			</dl>
			<dl>
				<dt>M&aacute;ximo comportamiento:</dt>
				<dd><input type="text" name="max_comportamiento" value="" /></dd>
			</dl>
			<dl>	
				<dt>Inicio comportamiento:</dt>	
				<dd><input type="text" name="inicio_comportamiento" value="" /></dd>
			</dl>
			<dl>	
				<dt>Color en horario:</dt>	
				<dd><select name="color_grupo">{$opciones_color}</select></dd>
			</dl>
			<dl>
				<input type="button" name="btn_ok" value="Guardar" />
				<input type="button" name="btn_cancelar" value="Cancelar" />
			</dl>
		</form>
R;

		//			<input type="hidden" name="idg" value="" />
	}

	public static function mostrar_como_listado(Grupo &$g)
	{
		$titulo=$g->acc_titulo();
		$id=$g->ID_INSTANCIA();

		$url_acceder=Factoria_urls::vista_grupo($g);

		$click_actualizar=null;
		$click_eliminar=null;
	
		if($id)
		{
			$click_actualizar='actualizar_grupo('.$id.', this)';
			$click_eliminar='eliminar_grupo('.$id.', this)';
		}

		return <<<R

	<li class="item_listado_grupo">
		<div class="caja_color color_grupo_{$g->acc_color_grupo()}"></div>
		<span class="titulo">{$titulo}</span>
		<div class="acciones">
			<a href="{$url_acceder}" class="iu_enlace_acceso">Acceder</a>
			<input type="button" onclick="{$click_actualizar}" class="iu_btn_generico" value="Actualizar" />
			<input type="button" onclick="{$click_eliminar}" class="iu_btn_eliminar" value="Eliminar" />
		</div>
	</li>
R;
	}

	public static function mostrar_como_xml(Grupo &$g)
	{
		$pc=$g->acc_porcentaje_comportamiento();
		$mc=$g->acc_max_comportamiento();
		$ic=$g->acc_inicio_comportamiento();
		$color=$g->acc_color_grupo();
		$url=Factoria_urls::vista_grupo($g);

		return <<<R
<g idg="{$g->ID_INSTANCIA()}" porcentaje_comportamiento="{$pc}" max_comportamiento="{$mc}" inicio_comportamiento="{$ic}" url="{$url}" clr="{$color}">
	<d n="nombre"><![CDATA[{$g->acc_titulo()}]]></d>
</g>
R;
	}
}
?>
