<?
abstract class Item_evaluable_vista
{
	public static function generar_contenedor_form(Evaluable &$e)
	{
		$id_evaluable=$e->ID_INSTANCIA();
		$plegado=new IU_plegado(3, $id_evaluable);
		$plegado->establecer_valor_boton('+ '.$e->acc_titulo());
		$plegado->establecer_clase('form_nuevo_item_evaluable');
		$plegado->establecer_auto_montado($id_evaluable);
		return $plegado->mostrar();
	}

	public static function generar_form()
	{
		$clave_estado_logica=Maquina_web::CLAVE_ESTADO_LOGICA;

		return <<<R

		<form class="iu_form" data-iuvisible="0" method="post" action="" id="form_item_evaluable" onsubmit="return false;">
			<input type="hidden" name="{$clave_estado_logica}" value="" />
			<input type="hidden" name="id_evaluable" value="" />

			<dl>
				<dt>Nombre:</dt>
				<dd><input type="text" name="nombre" value="" /></dd>
			</dl>

			<dl>
				<dt>Puntuaci&oacute;n:</dt>
				<dd>
					<input type="text" name="puntuacion" value="" />
					<p class="iu_aviso oculto">La puntuaci&oacute;n no puede cambiarse una vez se ha evaluado.</p>
				</dd>
				
			</dl>

			<dl>
				<input type="button" name="btn_ok" value="Guardar" />
				<input type="button" name="btn_cancelar" value="Cancelar" />
			</dl>

		</form>

R;
	}

	public static function mostrar_como_listado(Item_evaluable &$i)
	{
		$titulo=$i->acc_titulo();
		$valor=$i->acc_maximo_valor();

		$de=new Evaluable();
		$dg=new Grupo();

		$e=Cache::obtener_de_cache($de, $i->acc_id_evaluable());
		$g=Cache::obtener_de_cache($dg, $e->acc_id_grupo());
		$trimestre=$e->acc_trimestre();

		$url_ver=Factoria_urls::vista_tabla_por_trimestre_evaluable_e_item($g, $e, $i,  $trimestre);
		$id=$i->ID_INSTANCIA();

		$click_actualizar=null;	
		$click_eliminar=null;

		if($id)
		{
			$click_actualizar='actualizar_item_evaluable('.$id.', this)';
			$click_eliminar='eliminar_item_evaluable('.$id.', this)';
		}

		return <<<R
<li class="item_listado_item_evaluable">
	<span class="titulo">{$titulo} ({$valor})</span>
	<div class="acciones">
		<a href="{$url_ver}" class="iu_enlace_acceso">Evaluar</a>
		<input type="button" onclick="{$click_actualizar}" class="iu_btn_acceso" value="Actualizar" />
		<input type="button" onclick="{$click_eliminar}" class="iu_btn_eliminar" value="Eliminar" />
	</div>
</li>
R;
	}
	
	public static function mostrar_como_xml(Item_evaluable &$i)
	{
		//Ojo, los nombres de las tags están según el form, no la clase.
		$cuenta=$i->obtener_cuenta_entradas();

		$de=new Evaluable();
		$dg=new Grupo();

		$e=Cache::obtener_de_cache($de, $i->acc_id_evaluable());
		$g=Cache::obtener_de_cache($dg, $e->acc_id_grupo());
		$trimestre=$e->acc_trimestre();

		$url_ver=Factoria_urls::vista_tabla_por_trimestre_evaluable_e_item($g, $e, $i,  $trimestre);

		return <<<R
<a id="{$i->ID_INSTANCIA()}" puntuacion="{$i->acc_maximo_valor()}" cuenta="{$cuenta}" url="{$url_ver}" >
	<d n="nombre"><![CDATA[{$i->acc_titulo()}]]></d>
</a>
R;
	}
}
?>
