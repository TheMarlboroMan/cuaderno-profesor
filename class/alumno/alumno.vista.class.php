<?
abstract class Alumno_vista
{
	public static function generar_contenedor_form()
	{
		$plegado=new IU_plegado(1, null);
		$plegado->establecer_clase('form_nuevo_alumno');
		$plegado->establecer_valor_boton('+ Alumno');
		return $plegado->mostrar();
	}

	public static function generar_form()
	{
		$clave_estado_logica=Maquina_web::CLAVE_ESTADO_LOGICA;

		return <<<R

		<!--Form alumno -->
		<form class="iu_form" method="post" action="" id="form_alumno" onsubmit="return false;" >
			<input type="hidden" name="{$clave_estado_logica}" value="" />
	
			<dl>
				<dt>Nombre:</dt>
				<dd><input type="text" name="nombre" value="" /></dd>
			</dl>

			<dl>
				<dt>Apellidos:</dt> 
				<dd><input type="text" name="apellidos" value="" /></dd>
			</dl>
			
			<dl>
				<dt>Observaciones:</dt>
				<dd><textarea name="texto"/></textarea></dd>
			</dl>

			<dl>			
				<input type="button" name="btn_ok" value="Guardar" />
				<input type="button" name="btn_cancelar" value="Cancelar" />
			</dl>
		</form>
		<!--Fin form alumno -->

R;


		//			<input type="hidden" name="ida" value="" />
	}
	

	public static function generar_form_importar_alumnos()
	{
		$clave_estado_logica=Maquina_web::CLAVE_ESTADO_LOGICA;

		$contenido=<<<R
			<form class="iu_form" method="post" action="" id="form_alumnos_fichero" enctype="multipart/form-data">
				<input type="hidden" name="{$clave_estado_logica}" value="importar_alumnos_fichero" />
	
				<p>Adjuntar archivo CSV (apellidos, nombre) descargado de S&eacute;neca.</p>

				<dl>
					<dt>Archivo:</dt>
					<dd><input type="file" name="archivo" value="" /></dd>
				</dl>

				<dl>
					<input type="button" name="btn_enviar" value="Importar" />
				</dl>
			</form>

			<form class="iu_form" data-iuvisible="0" method="post" action="" id="form_alumnos_texto">
				<input type="hidden" name="{$clave_estado_logica}" value="importar_alumnos_texto" />
	
				<p>Insertar desde texto (un alumno por l&iacute;nea. Apellidos, nombre).</p>

				<dl>
					<dt>Archivo:</dt>
					<dd><textarea name="texto"></textarea></dd>
				</dl>

				<dl>
					<input type="button" name="btn_enviar" value="Importar" />
				</dl>
			</form>
R;

		$plegado=new IU_plegado();
		$plegado->establecer_valor_boton('Importar alumnos');
		$plegado->establecer_contenido($contenido);
		return $plegado->mostrar();
	}

	public static function mostrar_como_listado(Alumno &$a)
	{
		$id=$a->ID_INSTANCIA();

		$informe=null;
		$actualizar=null;
		$eliminar=null;

		if($id)
		{
			$informe='informe_alumno('.$id.', this);';
			$actualizar='actualizar_alumno('.$id.', this);';
			$eliminar='eliminar_alumno('.$id.', this);';
		}

		return <<<R

<li class="item_listado_alumno">
	<span class="titulo">{$a->acc_apellidos()}, {$a->acc_nombre()}</span>
	<div class="acciones">
		<input type="button" onclick="{$informe}" name="btn_informe" class="iu_btn_generico" value="Informe" />
		<input type="button" onclick="{$actualizar}" name="btn_actualizar" class="iu_btn_generico" value="Actualizar" />
		<input type="button" onclick="{$eliminar}" name="btn_eliminar" class="iu_btn_eliminar" value="Eliminar" />
	</div>
</li>
R;
	}

	public static function mostrar_como_xml(Alumno &$a)
	{	
		return <<<R
<a ida="{$a->ID_INSTANCIA()}">
	<d n="nombre"><![CDATA[{$a->acc_nombre()}]]></d>
	<d n="apellidos"><![CDATA[{$a->acc_apellidos()}]]></d>
	<d n="texto"><![CDATA[{$a->acc_texto()}]]></d>
</a>
R;
	}
};
?>
