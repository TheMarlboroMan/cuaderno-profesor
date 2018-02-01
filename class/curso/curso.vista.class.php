<?
abstract class Curso_vista
{
	public static function generar_contenedor_form()
	{
		$plegado=new IU_plegado(1, null);
		$plegado->establecer_clase('form_nuevo_curso');
		$plegado->establecer_valor_boton('+ Curso');
		return $plegado->mostrar();
	}

	public static function generar_form()
	{
		$clave_estado_logica=Maquina_web::CLAVE_ESTADO_LOGICA;

		return <<<R
		<form class="iu_form" method="post" action="" id="form_curso" onsubmit="return false">
			<input type="hidden" name="{$clave_estado_logica}" value="" />			
		
			<dl>
				<dt>Curso:</dt>
				<dd><input type="text" name="nombre" value="" /></dd>
			</dl>
			<dl>
				<input type="button" name="btn_ok" value="Guardar" />
				<input type="button" name="btn_cancelar" value="Cancelar" />
			</dl>
		</form>
R;

		//<input type="hidden" name="idc" value="" />
	}

	public static function mostrar_como_listado(Curso &$c, array &$grupos)
	{	
		$VER_GRUPOS=null;
		foreach($grupos as $clave => &$valor) $VER_GRUPOS.=Grupo_vista::mostrar_como_listado($valor);
		$VER_FORM_GRUPOS=Grupo_vista::generar_contenedor_form($c);

		$click_actualizar=null;
		$click_eliminar=null;
		$url_horario=null;

		$id_item=$c->ID_INSTANCIA();

		if($id_item)
		{
			$click_actualizar='actualizar_curso('.$id_item.', this)';
			$click_eliminar='eliminar_curso('.$id_item.', this)';			
			$url_horario=Factoria_urls::vista_horario($c);
		}		

		return <<<R
<li class="item_listado_curso">
	<div>
		<span class="titulo">{$c->acc_titulo()}</span>
		<div class="acciones">
			<a href="{$url_horario}" class="iu_enlace_acceso">Horario</a>
			<input type="button" class="iu_btn_generico" value="Actualizar" onclick="{$click_actualizar}" />
			<input type="button" class="iu_btn_eliminar" value="Eliminar" onclick="{$click_eliminar}" />
		</div>
	</div>

	<ul>
		{$VER_GRUPOS}
	</ul>

	{$VER_FORM_GRUPOS}
</li>
R;
	}

	public static function mostrar_como_xml(Curso &$c)
	{
		$url=Factoria_urls::vista_horario($c);

		return <<<R
<c idc="{$c->ID_INSTANCIA()}" url="{$url}">
	<d n="nombre"><![CDATA[{$c->acc_titulo()}]]></d>
</c>
R;
	}
}
?>
