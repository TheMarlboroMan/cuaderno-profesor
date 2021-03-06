function validar_config_horario(f)
{	
	var errores=0;
	errores+=form_error(form_validar_entero(f.franjas.value, 1, 10), f.franjas);
	return !errores;
}

var f_config_horario=document.getElementById('form_config_horario');
if(f_config_horario)
{
	registrar_evento(f_config_horario.btn_ok, function(){if(validar_config_horario(f_config_horario)) f_config_horario.submit();}, this, 'click');
}

/*****************************************************************/

function Nombrador_franjas()
{
	this.DOM_contenedor=document.getElementById('contenedor_form_franja');
	this.DOM_form=document.getElementById('form_franja');
	this.DOM_input_idf=this.DOM_form.idf;
	this.DOM_input_titulo=this.DOM_form.titulo;
	this.DOM_btn_ok=this.DOM_form.btn_ok;
	this.DOM_btn_cancelar=this.DOM_form.btn_cancelar;

	this.ref_celda=null;

	registrar_evento(this.DOM_btn_ok, this.enviar_ok, this, 'click');
	registrar_evento(this.DOM_btn_cancelar, this.cerrar, this, 'click');
}

Nombrador_franjas.prototype.reiniciar_valores=function()
{
	this.DOM_input_idf.value='';
	this.DOM_input_titulo.value='';
}

Nombrador_franjas.prototype.retirar=function() {this.DOM_contenedor.parentNode.removeChild(this.DOM_contenedor);}
Nombrador_franjas.prototype.mostrar=function() {document.body.appendChild(this.DOM_contenedor);}

Nombrador_franjas.prototype.enviar_ok=function()
{
	this.retirar();
	var url=URL_WEB+'_/'+this.DOM_form.idc.value+'/horario.html';
	form_enviar_ajax_post(this.DOM_form, url, this.procesar_ok, this)
}

Nombrador_franjas.prototype.procesar_ok=function(v_xml)
{
	var r=preparar_respuesta_xml_standard(v_xml, false);

	if(!r.resultado)
	{
		alert(r.mensaje);
	}
	else
	{
		var texto=r.l.obtener();
		this.ref_celda.textContent=texto;
	}

	this.finalizar();
}

Nombrador_franjas.prototype.iniciar=function(celda, id)
{
	BLOQUEADOR_UI.activar();
	this.reiniciar_valores();
	this.ref_celda=celda;
	this.DOM_input_idf.value=id;
	this.DOM_input_titulo.value=celda.textContent;
	this.mostrar();
}

Nombrador_franjas.prototype.cerrar=function()
{
	this.retirar();
	this.finalizar();
}

Nombrador_franjas.prototype.finalizar=function()
{
	this.ref_celda=null;
	BLOQUEADOR_UI.desactivar();
}

/******************************************************************************/

function Selector_contenido()
{
	this.DOM_contenedor=document.getElementById('selector_contenido');
	this.DOM_lista_grupos=document.getElementById('lista_grupos');
	this.DOM_lista_actividades=document.getElementById('lista_actividades');
	this.DOM_btn_cerrar=this.DOM_contenedor.querySelector('.iu_input_defecto');
	this.DOM_form=document.getElementById('form_selector_contenido');

	var actividades=this.DOM_lista_actividades.getElementsByTagName('li');
	var grupos=this.DOM_lista_grupos.getElementsByTagName('li');

	this.preparar(actividades, 1);
	this.preparar(grupos, 2);

	this.ref_celda=null;

	registrar_evento(this.DOM_btn_cerrar, this.cerrar, this, 'click');
	this.retirar();
}

Selector_contenido.prototype.retirar=function() {this.DOM_contenedor.parentNode.removeChild(this.DOM_contenedor);}
Selector_contenido.prototype.mostrar=function() {document.body.appendChild(this.DOM_contenedor);}

Selector_contenido.prototype.reiniciar_valores=function()
{
	this.DOM_form.idcon.value='';
	this.DOM_form.tipcon.value='';
	this.DOM_form.valcon.value='';
}

Selector_contenido.prototype.registrar=function(li, tip)
{
	var aquello=this;
	var val=li.getAttribute('data-val');
	registrar_evento(li, function() {aquello.enviar_ok(tip, val);}, this, 'click');
}

Selector_contenido.prototype.preparar=function(lis, tip)
{
	var l=lis.length;
	var i=0;
	while(i < l) this.registrar(lis[i++], tip);
}

Selector_contenido.prototype.enviar_ok=function(tipcon, valcon)
{
	this.retirar();
	this.DOM_form.tipcon.value=tipcon;
	this.DOM_form.valcon.value=valcon;

	var url=URL_WEB+'_/'+this.DOM_form.idc.value+'/horario.html';
	form_enviar_ajax_post(this.DOM_form, url, this.procesar_ok, this)
}

Selector_contenido.prototype.procesar_ok=function(v_xml)
{
	var r=preparar_respuesta_xml_standard(v_xml, false);

	if(!r.resultado)
	{
		alert(r.mensaje);
	}
	else
	{
		var clase=r.l.atributo('clr');
		var texto=r.l.obtener();

		this.ref_celda.className=clase;
		this.ref_celda.textContent=texto;
	}

	this.finalizar();
}

Selector_contenido.prototype.iniciar=function(celda, id)
{
	this.reiniciar_valores();
	BLOQUEADOR_UI.activar();
	this.ref_celda=celda;
	this.DOM_form.idcon.value=id;
	this.mostrar();
}

Selector_contenido.prototype.cerrar=function()
{
	this.retirar();
	this.finalizar();
}

Selector_contenido.prototype.finalizar=function()
{
	this.ref_celda=null;
	BLOQUEADOR_UI.desactivar();
}

/******************************************************************************/

function Horario()
{
	this.DOM_tabla=document.getElementById('tabla_horario');

	if(!this.DOM_tabla) return;

	var configurable=parseInt(this.DOM_tabla.getAttribute('data-configurable'), 10);
	if(!configurable) 
	{
		return;
	}
	else
	{
		var celdas=this.DOM_tabla.getElementsByTagName('td');
		var l=celdas.length;
		var i=0;

		this.NOMBRADOR_FRANJAS=new Nombrador_franjas();
		this.SELECTOR_CONTENIDO=new Selector_contenido();

		while(i < l) this.procesar_celda(celdas[i++]);
	}
}

Horario.prototype.procesar_celda=function(celda)
{
	var tipo=celda.getAttribute('data-tipo');

	switch(tipo)
	{
		case 'f': this.configurar_celda_franja(celda); break;
		case 'c': this.configurar_celda_contenido(celda); break;
	}
}

Horario.prototype.configurar_celda_franja=function(celda)
{
	var id=parseInt(celda.getAttribute('data-id', 10));

	var aquello=this;
	registrar_evento(celda, function() {aquello.NOMBRADOR_FRANJAS.iniciar(celda, id);}, this, 'click');
}

Horario.prototype.configurar_celda_contenido=function(celda)
{
	var id=parseInt(celda.getAttribute('data-id', 10));

	var aquello=this;
	registrar_evento(celda, function() {aquello.SELECTOR_CONTENIDO.iniciar(celda, id);}, this, 'click');
}

var HORARIO=new Horario();

function ir_a(url)
{
	window.location.href=url;
}
