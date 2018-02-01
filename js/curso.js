/******************************************************************************/
// Cursos

function Item_listado_curso(v_dom)
{
	Item_listado_base.call(this, v_dom);

	this.url_obtener_datos_actualizar=function(vid) {return URL_WEB+'portal.html?pvc_modo_vista=1';}
	this.clonar_form=function() {return clonar_form_curso();}
	this.clonar_item_listado=function() {return clonar_item_listado_curso();} 
	this.obtener_listado=function(f) {return document.getElementById('listado_cursos');}
	this.obtener_valor_logica_crear=function() {return 'crear';}
	this.obtener_valor_logica_modificar=function() {return 'modificar';}
	this.url_envio_crear=function() {return URL_WEB+'portal.html?';}
	this.url_envio_actualizar=function() {return URL_WEB+'portal.html?';}
	this.url_eliminar=function(vid) {return URL_WEB+'portal.html?mwlogica=eliminar';}
	this.validar_form=function(f) {return validar_form_curso(f);}
	this.post_crear=function(f) {iu_despachar_click_plegado_desde_cuerpo(f.parentNode);}

	this.recibir_datos_xml=function(l, accion, atributos)
	{
		var idc=l.atributo('idc');
		var url=l.atributo('url');
		var txt=l.obtener();

		var ca=this.IC.DOM_item.querySelector('.acciones');

		if(accion=='crear')
		{
			var g=this.IC.DOM_item.querySelector('.iu_grupo_form');
			g.setAttribute('data-iucallbackparams', idc);
					
			var gp=new Grupo_plegado(g);

			var btn_actualizar=ca.children[1];
			registrar_evento(btn_actualizar, function() {actualizar_curso(idc, btn_actualizar);}, this, 'click');

			var btn_eliminar=ca.children[2];
			registrar_evento(btn_eliminar, function() {eliminar_curso(idc, btn_actualizar);}, this, 'click');
		}

		ca.children[0].href=url;

		this.IC.DOM_item.querySelector('.titulo').textContent=txt;
	}
}

Item_listado_curso.prototype=Object.create(Item_listado_base.prototype);

/**************/

function clonar_form_curso()
{
	var form_nuevo_curso=document.getElementById('form_curso');
	if(form_nuevo_curso) 
	{
		//form_nuevo_curso.onsubmit=function(){return false;} //Ojo, esta propiedad no se copia...
		var f=form_nuevo_curso.cloneNode(true);
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

function clonar_item_listado_curso()
{
	var item_listado=document.getElementById('plantillas').querySelectorAll('.item_listado_curso')[0];
	var il=item_listado.cloneNode(true);
	return il;
}

function validar_form_curso(f)
{
	var errores=0;
	errores+=form_error(form_validar_cadena(f.nombre.value), f.nombre);
	return !errores;
}

function callback_desplegar_form_curso(visible, cabecera, cuerpo, params)
{
	if(!visible)
	{
		var old_f=cuerpo.children[0]	
		cuerpo.removeChild(old_f);
		delete old_f;
	}
	else
	{
		var f=clonar_form_curso();
		f.btn_cancelar.parentNode.removeChild(f.btn_cancelar);
		f.className='iu_form'
		registrar_evento(f.btn_ok, function(){if(validar_form_curso(f)) crear_dato(f, new Item_listado_curso());}, this, 'click');

		cuerpo.appendChild(f);
	}
}

function curso_desde_enlace(enlace){return enlace.parentNode.parentNode;}
function eliminar_curso(vid, enlace) {eliminar_dato(vid, curso_desde_enlace(enlace), new Item_listado_curso());}
function actualizar_curso(vid, enlace){actualizar_dato(vid, curso_desde_enlace(enlace), new Item_listado_curso());}

registrar_callback_plegado_forms_ui(1, callback_desplegar_form_curso);
