/******************************************************************************/
// Item_evaluables

function Item_listado_item_evaluable(v_dom)
{
	this.cuenta=0;
	this.puntuacion_original=0;
	this.id_grupo=document.getElementById('idg').value;
	Item_listado_base.call(this, v_dom);

	this.url_obtener_datos_actualizar=function(vid) {return URL_WEB+'grupos/_/'+this.id_grupo+'/gestionar.html?pvc_modo_vista=3';}
	this.clonar_form=function() {return clonar_form_item_evaluable();}
	this.clonar_item_listado=function() {return clonar_item_listado_item_evaluable();} 
	this.obtener_listado=function(f) {return f.parentNode.parentNode.parentNode.querySelector('ol');}
	this.obtener_valor_logica_crear=function() {return 'crear_item_evaluable';}
	this.obtener_valor_logica_modificar=function() {return 'modificar_item_evaluable';}
	this.url_envio_crear=function() {return URL_WEB+'grupos/_/'+this.id_grupo+'/gestionar.html';}
	this.url_envio_actualizar=function() {return URL_WEB+'grupos/_/'+this.id_grupo+'/gestionar.html';}
	this.url_eliminar=function(vid) {return URL_WEB+'grupos/_/'+this.id_grupo+'/gestionar.html?mwlogica=eliminar_item_evaluable';}
	this.validar_form=function(f) {return validar_form_item_evaluable(f, this.cuenta, this.p_original);}
	this.post_crear=function(f) {iu_despachar_click_plegado_desde_cuerpo(f.parentNode);}

	//Si sólo quedan dentro espacios en blanco los eliminamos para que selector css haga su efecto y no se muestre.
	this.post_eliminar=function(ol, l, att){if(!ol.children.length) ol.innerHTML='';}

	this.recibir_datos_xml=function(l, accion, atributos)
	{
		var id=l.atributo('id');
		var puntuacion=l.atributo('puntuacion');
		var url=l.atributo('url');
		l.bajar(0);
		var titulo=l.obtener();

		if(accion=='crear_item_evaluable')
		{
			var ca=this.IC.DOM_item.querySelector('.acciones');

			var btn_actualizar=ca.children[1];
			btn_actualizar.onclick=function() {actualizar_item_evaluable(id, btn_actualizar);}

			var btn_eliminar=ca.children[2];
			btn_eliminar.onclick=function() {eliminar_item_evaluable(id, btn_actualizar);}
		}	

		this.IC.DOM_item.querySelector('.iu_enlace_acceso').href=url;
		this.IC.DOM_item.querySelector('.titulo').textContent=titulo+' ('+puntuacion+')';		
	}

	//Cuando se reciben los datos actuales los procesamos antes de mostrar el formulario.
	this.recepcion_datos_modificar=function(f, l)
	{
		l.bajar(0);
		this.cuenta=parseInt(l.atributo('cuenta'), 10);
		if(this.cuenta)	//Bloquear puntuación si se ha modificado ya.
		{
			f.puntuacion.disabled=true;
			var resultados=f.querySelectorAll('dd > p.oculto');	//Q-q-q-query selectors :D!.
			if(resultados.length) resultados[0].className='aviso';
		}
	}
}

Item_listado_item_evaluable.prototype=Object.create(Item_listado_base.prototype);

/**************/

function clonar_form_item_evaluable()
{
	var fa=document.getElementById('form_item_evaluable');
	if(fa) 
	{
		var f=fa.cloneNode(true);
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

function clonar_item_listado_item_evaluable()
{
	var item_listado=document.getElementById('plantillas').querySelector('.item_listado_item_evaluable');
	var il=item_listado.cloneNode(true);
	return il;
}

function validar_form_item_evaluable(f, cuenta, p_original)
{
	var errores=0;
	errores+=form_error(form_validar_cadena(f.nombre.value), f.nombre);
	errores+=form_error(form_validar_entero(f.puntuacion.value, 1, 100), f.puntuacion);

	//TODO TODO TODO TODO
/*
	if(this.cuenta)
	{
		comparar p_original con la nueva p y advertir del posible problema!!!!
	}
*/	
	//TODO TODO TODO TODO

	return !errores;
}

function callback_desplegar_form_item_evaluable(visible, cabecera, cuerpo, params)
{
	if(!visible)
	{
		var old_f=cuerpo.children[0]	
		cuerpo.removeChild(old_f);
		delete old_f;
	}
	else
	{
		var id_evaluable=parseInt(params, 10);

		var f=clonar_form_item_evaluable();
		f.btn_cancelar.parentNode.removeChild(f.btn_cancelar);
		f.className='iu_form'
		f.id_evaluable.value=id_evaluable;
		registrar_evento(f.btn_ok, function(){if(validar_form_item_evaluable(f)) crear_dato(f, new Item_listado_item_evaluable());}, this, 'click');
		cuerpo.appendChild(f);
	}
}

function item_evaluable_listado_desde_enlace(enlace){return enlace.parentNode.parentNode;}

function eliminar_item_evaluable(vid, enlace){eliminar_dato(vid, item_evaluable_listado_desde_enlace(enlace), new Item_listado_item_evaluable());}
function actualizar_item_evaluable(vid, enlace){actualizar_dato(vid, item_evaluable_listado_desde_enlace(enlace), new Item_listado_item_evaluable());}
registrar_callback_plegado_forms_ui(3, callback_desplegar_form_item_evaluable);
