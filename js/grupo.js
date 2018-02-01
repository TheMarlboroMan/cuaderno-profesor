/******************************************************************************/
// Grupos

function Item_listado_grupo(v_dom)
{
	Item_listado_base.call(this, v_dom);

	this.url_obtener_datos_actualizar=function(vid) {return URL_WEB+'portal.html?pvc_modo_vista=2';}
	this.clonar_form=function() {return clonar_form_grupo();}
	this.clonar_item_listado=function() {return clonar_item_listado_grupo();} 
	this.obtener_listado=function(f)
	{
		var l=f.parentNode.parentNode.parentNode.querySelector('ul');
		return l;
	}
	this.obtener_valor_logica_crear=function() {return 'crear_grupo';}
	this.obtener_valor_logica_modificar=function() {return 'modificar_grupo';}
	this.url_envio_crear=function() {return URL_WEB+'portal.html?';}
	this.url_envio_actualizar=function() {return URL_WEB+'portal.html?';}
	this.url_eliminar=function(vid) {return URL_WEB+'portal.html?mwlogica=eliminar_grupo';}
	this.validar_form=function(f) {return validar_form_grupo(f);}
	this.post_crear=function(f) {iu_despachar_click_plegado_desde_cuerpo(f.parentNode);}
	this.recibir_datos_xml=function(l, accion, atributos)
	{
		var id=l.atributo('idg');
		var color=l.atributo('clr');
		var url=l.atributo('url');
		var txt=l.texto();

		if(accion=='crear_grupo')
		{
			var btn_actualizar=this.IC.DOM_item.querySelector('.iu_btn_generico');
			registrar_evento(btn_actualizar, function() {actualizar_grupo(id, btn_actualizar);}, this, 'click');

			var btn_eliminar=this.IC.DOM_item.querySelector('.iu_btn_eliminar');
			registrar_evento(btn_eliminar, function() {eliminar_grupo(id, btn_actualizar);}, this, 'click');
		}

		this.IC.DOM_item.getElementsByTagName('div')[0].className='caja_color color_grupo_'+color;
		this.IC.DOM_item.querySelector('.titulo').textContent=txt;
		this.IC.DOM_item.querySelector('.iu_enlace_acceso').href=url;

	}
}
Item_listado_grupo.prototype = Object.create(Item_listado_base.prototype);

/*****/

function clonar_form_grupo()
{
	var form_nuevo_grupo=document.getElementById('form_grupo');
	if(form_nuevo_grupo) 
	{
		var f=form_nuevo_grupo.cloneNode(true);
		f.removeAttribute('id');
		f.onsubmit=function(){return false;}
		f.reset();
		return f;
	}
	else
	{
		return null;
	}
}

function clonar_item_listado_grupo()
{
	var item_listado=document.getElementById('plantillas').querySelectorAll('.item_listado_grupo')[0];
	var il=item_listado.cloneNode(true);
	return il;
}

function validar_form_grupo(f)
{
	var errores=0;

	errores+=form_error(form_validar_cadena(f.nombre.value), f.nombre);
	errores+=form_error(form_validar_entero(f.porcentaje_comportamiento.value, 0, 255), f.porcentaje_comportamiento);
	errores+=form_error(form_validar_entero(f.max_comportamiento.value, 0, 255), f.max_comportamiento);
	errores+=form_error(form_validar_entero(f.inicio_comportamiento.value, 0, 255), f.inicio_comportamiento);

	return !errores;
}

function callback_desplegar_form_grupo(visible, cabecera, cuerpo, params)
{
	if(!visible)
	{
		var old_f=cuerpo.children[0]	
		cuerpo.removeChild(old_f);
		delete old_f;
	}
	else
	{
		var idc=parseInt(params, 10);

		var f=clonar_form_grupo();
		f.btn_cancelar.parentNode.removeChild(f.btn_cancelar);
		f.className='iu_form'
		f.idc.value=idc;
		registrar_evento(f.btn_ok, function(){if(validar_form_curso(f)) crear_dato(f, new Item_listado_grupo());}, this, 'click');
		cuerpo.appendChild(f);
	}	
}

function grupo_desde_enlace(enlace) {return enlace.parentNode.parentNode;}
function eliminar_grupo(vid, enlace) {eliminar_dato(vid, grupo_desde_enlace(enlace), new Item_listado_grupo());}
function actualizar_grupo(vid, enlace) {actualizar_dato(vid, grupo_desde_enlace(enlace), new Item_listado_grupo());}

registrar_callback_plegado_forms_ui(2, callback_desplegar_form_grupo);
