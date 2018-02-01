<?
abstract class Evaluable_vista
{
	public static function generar_contenedor_form($trimestre)
	{
		$plegado=new IU_plegado(2, $trimestre);
		$plegado->establecer_clase('form_nuevo_evaluable');
		$plegado->establecer_valor_boton('+ Evaluable');
		return $plegado->mostrar();
	}

	public static function generar_form()
	{
		$clave_estado_logica=Maquina_web::CLAVE_ESTADO_LOGICA;

		return <<<R

		<form class="iu_form" method="post" action="" id="form_evaluable" onsubmit="return false;" >
			<input type="hidden" name="{$clave_estado_logica}" value="" />
			<input type="hidden" name="trimestre" value="" />

			<dl>
				<dt>Nombre:</dt>
				<dd><input type="text" name="titulo" value="" /></dd>
			</dl>
			
			<dl>
				<dt>Porcentaje:</dt>
				<dd><input type="text" name="porcentaje" value="" /></dd>
			</dl>

			<dl>
				<input type="button" name="btn_ok" value="Guardar" />
				<input type="button" name="btn_cancelar" value="Cancelar" />
			</dl>
		</form>
R;
	}

	public static function mostrar_como_listado(Evaluable &$e)
	{
		$trimestre=$e->acc_trimestre();
		$dg=new Grupo();
		$grupo=&Cache::obtener_de_cache($dg, $e->acc_id_grupo());
		$url_vista=Factoria_urls::vista_tabla_por_trimestre_y_evaluable($grupo, $e, $trimestre);
		
		$FORM_ITEM=Item_evaluable_vista::generar_contenedor_form($e);
		$LISTADO_ITEMS=null;
		$items=Item_evaluable::obtener_para_evaluable($e);
		if(count($items)) foreach($items as $clave => &$valor) $LISTADO_ITEMS.=Item_evaluable_vista::mostrar_como_listado($valor);

		$id=$e->ID_INSTANCIA();
	
		$onclick_actualizar=null;
		$onclick_eliminar=null;

		if($id)
		{
			$onclick_actualizar='actualizar_evaluable('.$id.', this)';
			$onclick_eliminar='eliminar_evaluable('.$id.', this)';
		}

		return <<<R
<li class="item_listado_evaluable">
	<span class="titulo">{$e->acc_titulo()} ({$e->acc_porcentaje()}%)</span>

	<div class="acciones">
		<a href="{$url_vista}" class="iu_enlace_acceso">Evaluar completo</a>
		<input type="button" onclick="{$onclick_actualizar}" class="iu_btn_generico" value="Actualizar" />
		<input type="button" onclick="{$onclick_eliminar}" class="iu_btn_eliminar" value="Eliminar" />
	</div>

	<ol>{$LISTADO_ITEMS}</ol>

	{$FORM_ITEM}
</li>
R;
	}

	public static function mostrar_como_xml(Evaluable &$e)
	{
		$dg=new Grupo();
		$grupo=&Cache::obtener_de_cache($dg, $e->acc_id_grupo());
		$url=Factoria_urls::vista_tabla_por_trimestre_y_evaluable($grupo, $e, $e->acc_trimestre());

		return <<<R
<a ide="{$e->ID_INSTANCIA()}" trimestre="{$e->acc_trimestre()}" porcentaje="{$e->acc_porcentaje()}" url="{$url}">
	<d n="titulo"><![CDATA[{$e->acc_titulo()}]]></d>
</a>
R;
	}
};
?>
