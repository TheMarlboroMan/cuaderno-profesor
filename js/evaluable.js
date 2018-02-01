/******************************************************************************/
// Evaluables

function Item_listado_evaluable(v_dom)
{
	this.id_grupo=document.getElementById('idg').value; 
	Item_listado_base.call(this, v_dom);
	this.url_obtener_datos_actualizar=function(vid) {return URL_WEB+'grupos/_/'+this.id_grupo+'/gestionar.html?pvc_modo_vista=2';}
	this.clonar_form=function() {return clonar_form_evaluable();}
	this.clonar_item_listado=function() {return clonar_item_listado_evaluable();} 
	this.obtener_listado=function(f) 
	{
		var l=f.parentNode.parentNode.parentNode.querySelector('ul');
		return l;
	}
	this.obtener_valor_logica_crear=function() {return 'crear_evaluable';}
	this.obtener_valor_logica_modificar=function() {return 'modificar_evaluable';}
	this.url_envio_crear=function() {return URL_WEB+'grupos/_/'+this.id_grupo+'/gestionar.html';}
	this.url_envio_actualizar=function() {return URL_WEB+'grupos/_/'+this.id_grupo+'/gestionar.html';}
	this.url_eliminar=function(vid) {return URL_WEB+'grupos/_/'+this.id_grupo+'/gestionar.html?mwlogica=eliminar_evaluable';}
	this.validar_form=function(f) {return validar_form_evaluable(f);}
	this.post_crear=function(f) {iu_despachar_click_plegado_desde_cuerpo(f.parentNode);}
	this.post_eliminar=function(c, l, att)
	{
		var porcentaje_total=att['p'];
		var trimestre=att['trimestre'];
		establecer_porcentaje_total_para_trimestre(porcentaje_total, trimestre);
	}

	this.recibir_datos_xml=function(l, accion, atributos)
	{
		var porcentaje_total=atributos['p'];
		var porcentaje=l.atributo('porcentaje');
		var id=l.atributo('ide');
		var trimestre=l.atributo('trimestre');
		var url=l.atributo('url');
		
		l.bajar(0);

		var titulo=l.texto();
		var ca=this.IC.DOM_item.querySelector('.acciones');

		if(accion=='crear_evaluable')
		{
			var btn_actualizar=ca.children[1];
			btn_actualizar.onclick=function() {actualizar_evaluable(id, btn_actualizar);}

			var btn_eliminar=ca.children[2];
			btn_eliminar.onclick=function() {eliminar_evaluable(id, btn_actualizar);}

			var g=this.IC.DOM_item.querySelector('.iu_grupo_form');
			g.setAttribute('data-iucallbackparams', id);
			var gp=new Grupo_plegado(g);
		}

		this.IC.DOM_item.querySelector('.titulo').textContent=titulo+ '('+porcentaje+'%)';
		this.IC.DOM_item.querySelector('.iu_grupo_form .iu_btn_form').value='+ '+titulo;
		ca.children[0].href=url;
		
		establecer_porcentaje_total_para_trimestre(porcentaje_total, trimestre);
	}
}

Item_listado_evaluable.prototype=Object.create(Item_listado_base.prototype);

/**************/

function establecer_porcentaje_total_para_trimestre(porcentaje, trimestre)
{
	var capa_t=document.getElementById('total_'+trimestre);
	var contenedor=capa_t.parentNode;
	if(capa_t) 
	{
		capa_t.innerHTML=porcentaje;	
		contenedor.className=porcentaje == '100' ? 'verde' : 'rojo';
	}	
}	

function clonar_form_evaluable()
{
	var fa=document.getElementById('form_evaluable');
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

function clonar_item_listado_evaluable()
{
	var item_listado=document.getElementById('plantillas').querySelector('.item_listado_evaluable');
	var il=item_listado.cloneNode(true);
	return il;
}

function validar_form_evaluable(f)
{
	var errores=0;
	errores+=form_error(form_validar_cadena(f.titulo.value), f.titulo);
	errores+=form_error(form_validar_entero(f.porcentaje.value, 1, 100), f.porcentaje);
	return !errores;
}

function callback_desplegar_form_evaluable(visible, cabecera, cuerpo, params)
{
	if(!visible)
	{
		var old_f=cuerpo.children[0]	
		cuerpo.removeChild(old_f);
		delete old_f;
	}
	else
	{
		var trimestre=parseInt(params, 10);

		var f=clonar_form_evaluable();
		f.btn_cancelar.parentNode.removeChild(f.btn_cancelar);
		f.mwlogica.value='crear_evaluable';
		f.className='iu_form'
		f.trimestre.value=trimestre;
//		registrar_evento(f.btn_ok, function(){if(validar_form_evaluable(f)) f.submit();}, this, 'click');
		registrar_evento(f.btn_ok, function(){if(validar_form_evaluable(f)) crear_dato(f, new Item_listado_evaluable());}, this, 'click');

		cuerpo.appendChild(f);
	}
}

function evaluable_listado_desde_enlace(enlace)
{
	return enlace.parentNode.parentNode;
}

function eliminar_evaluable(vid, enlace){eliminar_dato(vid, evaluable_listado_desde_enlace(enlace), new Item_listado_evaluable());}
function actualizar_evaluable(vid, enlace){actualizar_dato(vid, evaluable_listado_desde_enlace(enlace), new Item_listado_evaluable());}
registrar_callback_plegado_forms_ui(2, callback_desplegar_form_evaluable);
