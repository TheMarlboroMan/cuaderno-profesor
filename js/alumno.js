/******************************************************************************/
// Alumnos

function Item_listado_alumno(v_dom)
{
	this.id_grupo=document.getElementById('idg').value;
	Item_listado_base.call(this, v_dom);

	this.url_obtener_datos_actualizar=function(vid) {return URL_WEB+'grupos/_/'+this.id_grupo+'/gestionar.html?pvc_modo_vista=1';}
	this.clonar_form=function() {return clonar_form_alumno();}
	this.clonar_item_listado=function() {return clonar_item_listado_alumno();} 
	this.obtener_listado=function(f) {return document.getElementById('listado_alumnos');}
	this.obtener_valor_logica_modificar=function() {return 'modificar_alumno';}
	this.obtener_valor_logica_crear=function() {return 'crear_alumno';}
	this.url_envio_crear=function() {return URL_WEB+'grupos/_/'+this.id_grupo+'/gestionar.html';}
	this.url_envio_actualizar=function() {return URL_WEB+'grupos/_/'+this.id_grupo+'/gestionar.html';}
	this.url_eliminar=function(vid) {return URL_WEB+'grupos/_/'+this.id_grupo+'/gestionar.html?mwlogica=eliminar_alumno';}
	this.validar_form=function(f) {return validar_form_alumno(f);}
	this.post_crear=function(f) {iu_despachar_click_plegado_desde_cuerpo(f.parentNode);}
	this.recibir_datos_xml=function(l, accion, atributos)
	{

		var id=l.atributo('ida');
		l.bajar(0);
		var nombre=l.obtener();
		var apellidos=l.obtener();
//		var texto=l.obtener();

		this.IC.DOM_item.querySelector('.titulo').textContent=apellidos+', '+nombre;

		if(accion=='crear_alumno')
		{
			var ca=this.IC.DOM_item.querySelector('.acciones');
			ca.children[0]
			var btn_informe=ca.children[0];
			var btn_actualizar=ca.children[1];
			var btn_eliminar=ca.children[2];

			registrar_evento(btn_informe, function() {informe_alumno(id, btn_informe);}, this, 'click');
			registrar_evento(btn_actualizar, function() {actualizar_alumno(id, btn_actualizar);}, this, 'click');
			registrar_evento(btn_eliminar, function() {eliminar_alumno(id, btn_eliminar);}, this, 'click');
		}
	}
}

Item_listado_alumno.prototype=Object.create(Item_listado_base.prototype);

/**************/

function clonar_form_alumno()
{
	var fa=document.getElementById('form_alumno');
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

function clonar_item_listado_alumno()
{
	var item_listado=document.getElementById('plantillas').querySelector('.item_listado_alumno');
	var il=item_listado.cloneNode(true);
	return il;
}

function validar_form_alumno(f)
{
	var errores=0;
	errores+=form_error(form_validar_cadena(f.nombre.value), f.nombre);
	errores+=form_error(form_validar_cadena(f.apellidos.value), f.apellidos);
	return !errores;
}

function callback_desplegar_form_alumno(visible, cabecera, cuerpo, params)
{
	if(!visible)
	{
		var old_f=cuerpo.children[0]	
		cuerpo.removeChild(old_f);
		delete old_f;
	}
	else
	{
		var f=clonar_form_alumno();
		f.btn_cancelar.parentNode.removeChild(f.btn_cancelar);
		f.className='iu_form'
		registrar_evento(f.btn_ok, function(){if(validar_form_alumno(f)) crear_dato(f, new Item_listado_alumno());}, this, 'click');

		cuerpo.appendChild(f);
	}
}

function alumno_listado_desde_enlace(enlace)
{
	return enlace.parentNode.parentNode;
}

function eliminar_alumno(vid, enlace){eliminar_dato(vid, alumno_listado_desde_enlace(enlace), new Item_listado_alumno());}
function actualizar_alumno(vid, enlace){actualizar_dato(vid, alumno_listado_desde_enlace(enlace), new Item_listado_alumno());}

registrar_callback_plegado_forms_ui(1, callback_desplegar_form_alumno);

/******************************************************************************/

function Informe_alumno()
{
	this.DOM_capa=document.getElementById('informe_alumno');
	this.DOM_capa.parentNode.removeChild(this.DOM_capa);
	this.DOM_capa.className='';

	this.btn=this.DOM_capa.getElementsByTagName('input')[0];
	registrar_evento(this.btn, this.desactivar, this, 'click');

	this.area=this.DOM_capa.getElementsByTagName('textarea')[0];
	registrar_evento(this.area, this.cambio_area, this, 'keydown');

	this.pre=this.DOM_capa.getElementsByTagName('pre')[0];

	this.texto=null;

	this.activo=false;
}

Informe_alumno.prototype.cambio_area=function()
{
	this.pre.innerHTML=this.texto+this.area.value;
}

Informe_alumno.prototype.solicitar=function(id_alumno)
{
	this.activo=true;
	BLOQUEADOR_UI.activar();

	var id_grupo=document.getElementById('idg').value;
	var url=URL_WEB+'grupos/_/'+id_grupo+'/gestionar.html?pvc_modo_vista=4&id_xml='+id_alumno;
	var xml=new Lector_XML();
	xml.crear(url, this.activar, this, false);
}

Informe_alumno.prototype.activar=function(v_xml)
{
	var r=preparar_respuesta_xml_standard(v_xml, false);

	if(!r.resultado)
	{
		alert(r.mensaje);
		this.desactivar();
	}
	else
	{
		this.texto=r.l.texto();
		this.pre.innerHTML=this.texto;
		document.body.appendChild(this.DOM_capa);
	}
}

Informe_alumno.prototype.desactivar=function()
{
	this.activo=false;
	BLOQUEADOR_UI.desactivar();
	document.body.removeChild(this.DOM_capa);
}

var INFORME_ALUMNO=new Informe_alumno();

/******************************************************************************/

function informe_alumno(id_alumno, enlace)
{
	if(!INFORME_ALUMNO.activo)
	{
		INFORME_ALUMNO.solicitar(id_alumno);
	}
}
