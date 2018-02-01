//TODO TODO TODO
function Herramienta_eliminar()
{
	this.DOM_li=document.getElementById('herramienta_del');
	this.activa=false;

	registrar_evento(this.DOM_li, this.click_li, this, 'click');
}

Herramienta_eliminar.prototype.click_li=function()
{
	this.intercambiar();
}

Herramienta_eliminar.prototype.intercambiar=function() 
{
	this.activa=!this.activa;
	this.DOM_li.className=this.activa ? 'activa' : '';
}

Herramienta_eliminar.prototype.es_activa=function() {return this.activa;}

var HERRAMIENTA_ELIMINAR=new Herramienta_eliminar();

/******************************************************************************/

function Celda_comportamiento(td)
{
	var spans=td.getElementsByTagName('span');
	this.span_actual=spans[0];
	this.span_maximo=spans[1];

	this.media=parseInt(td.getAttribute('data-valorinicial'), 10);

	this.porcentaje=parseInt(td.getAttribute('data-porcentaje'), 10);
	this.valor_maximo=parseInt(this.span_maximo.innerHTML, 10);
	this.valor_actual=parseInt(this.span_actual.innerHTML, 10);
	this.id_comportamiento=parseInt(td.getAttribute('data-id'), 10);
	this.nombre_alumno=td.getAttribute('data-nombrealumno'), 10;

	var inputs=td.getElementsByTagName('input');
	var aquello=this;

	this.input_sumar=inputs[0];
	this.input_restar=inputs[1];

	registrar_evento(this.input_sumar, function() {invocar_comportamiento(aquello, 1, inputs[0]);}, this, 'click'); 
	registrar_evento(this.input_restar, function() {invocar_comportamiento(aquello, -1, inputs[1]);}, this, 'click');

	this.REF_FILA=null;
}

Celda_comportamiento.prototype.calcular_media=function()
{
	var puntos_por_max_comportamiento=this.porcentaje / 10;
	var calculo=(this.media / this.valor_maximo);
	var resultado=(calculo * puntos_por_max_comportamiento) / 1;

	return resultado;
}

Celda_comportamiento.prototype.recargar=function(nuevo_val, nueva_media)
{
	this.span_actual.innerHTML=nuevo_val;
	this.media=nueva_media;
//	this.span_media.innerHTML=nueva_media;

	//TODO TODO TODO TODO
	//Estoger uno en particular.
	this.input_sumar.focus();
	//TODO TODO TODO TODO

	this.REF_FILA.calcular_media();

}

/******************************************************************************/

function Grupo_calculo()
{
	this.inputs=Array();
	this.span=null;
	this.media=0.0;
	this.porcentaje=0;
	
	this.REF_FILA=null;
}

Grupo_calculo.prototype.asignar_input=function(c)
{
	var i=c.getElementsByTagName('input')[0];
	this.configurar_input(i);
	this.inputs.push(i);
}

Grupo_calculo.prototype.asignar_span=function(c)
{
	this.span=c.getElementsByTagName('span')[0];
	this.porcentaje=parseInt(c.getAttribute('data-porcentaje'));
}

Grupo_calculo.prototype.calcular_media=function()
{
	var resultado=(this.media * this.porcentaje) / 100;
	return resultado;
}

Grupo_calculo.prototype.recalcular_media=function(calcular)
{
	var t=0;
	var l=this.inputs.length;
	var i=0;
	var total_activos=0;

	while(i < l)
	{
//		var mm=parseInt(this.inputs[i].getAttribute("data-maxvaloracion"), 10);
//		var mm=this.inputs[i].mm;
		var input=this.inputs[i];
		var tipo=input.getAttribute('data-t');

		if(tipo=='actualizar')
		{
			var mm=parseInt(input.getAttribute("data-maxvaloracion"), 10);
			var val=this.valor_input(input);

			//Calcular media...
			var media=(val * 10) / mm;
			t+=media;
			++total_activos;
		}

		++i;
	}

	this.media=total_activos ? (t / total_activos) : 0;
	this.span.innerHTML=this.media.toFixed(2);
	if(calcular) this.REF_FILA.calcular_media();
}

Grupo_calculo.prototype.valor_input=function(input)
{
	var val=parseFloat(input.value, 10);
	if(isNaN(val)) val=0;
	return val;
}

Grupo_calculo.prototype.validar_input=function(input)
{
	var mm=parseInt(input.getAttribute("data-maxvaloracion"), 10);
	var val=this.valor_input(input);

	if(isNaN(parseInt(input.value, 10) ))
	{
		input.value=0;
	}
	else if(val > mm) 
	{
		input.value=mm;
	}
}

Grupo_calculo.prototype.configurar_input=function(input)
{
	var aquello=this;

	function post_grabar_input(v_xml)
	{
		var r=preparar_respuesta_xml_standard(v_xml, false);
		switch(r.resultado)
		{
			case 0: alert(r.mensaje); break;
			case 1: 
				
				var id_dato=r.resultado_att['idd'];
				input.setAttribute('data-idd', id_dato);
				input.setAttribute('data-t', 'actualizar');

				if(aquello.REF_FILA) aquello.recalcular_media(true);

				input.disabled=false;
				input.className='input_nota';
			break;
		}
	}

	function post_eliminar_input(v_xml)
	{
		var r=preparar_respuesta_xml_standard(v_xml, false);
		switch(r.resultado)
		{
			case 0: alert(r.mensaje); break;
			case 1: 
				input.setAttribute('data-t', 'nuevo');
//				input.removeAttribute('data-idd');

				if(aquello.REF_FILA) aquello.recalcular_media(true);

				input.value='';
				input.disabled=false;
				input.className='input_nota nuevo';
			break;
		}
	}

	function cambio()
	{
		aquello.validar_input(input);

		input.disabled=true;
		input.className='oculto';

		var url=URL_WEB+'guardar_datos_alumno.html?';
		var datos_post='';

		var tipo=input.getAttribute('data-t');

		switch(tipo)
		{
			case 'nuevo': datos_post+='&t=nuevo&ida='+input.getAttribute('data-ida')+'&idi='+input.getAttribute('data-idi'); break;
			case 'actualizar': datos_post+='&t=actualizar&idd='+input.getAttribute('data-idd'); break;
		}

		datos_post+='&v='+input.value;
		var xml=new Lector_XML();
		xml.pasar_a_post(datos_post);
		xml.crear(url, post_grabar_input, this, false); //Ojo al ámbito...
	}

	function click_input_nota()
	{
		if(HERRAMIENTA_ELIMINAR.es_activa())
		{
			var tipo=input.getAttribute('data-t');
	
			if(tipo=='actualizar')
			{
				input.disabled=true;
				input.className='oculto';

				var url=URL_WEB+'guardar_datos_alumno.html?';
				var datos_post='&t=eliminar&idd='+input.getAttribute('data-idd');

				var xml=new Lector_XML();
				xml.pasar_a_post(datos_post);
				xml.crear(url, post_eliminar_input, this, false); //Ojo al ámbito...
			}

			HERRAMIENTA_ELIMINAR.intercambiar();
		}
	}


	registrar_evento(input, cambio, this, 'change');
	registrar_evento(input, click_input_nota, this, 'click');

	//var mm=parseInt(input.getAttribute("data-maxvaloracion"), 10);
	//input.mm=mm;
}

/******************************************************************************/

function Fila_calculo()
{
	this.grupos=Array();
	this.CELDA_COMPORTAMIENTO=null;
	this.celda_media_final=null;
	this.celda_media_seneca=null;
}

Fila_calculo.prototype.insertar_grupo=function(gc)
{
	this.grupos.push(gc);
	gc.REF_FILA=this;
}

Fila_calculo.prototype.asignar_celda_media=function(c)
{
	this.celda_media_final=c;	
}

Fila_calculo.prototype.asignar_celda_media_seneca=function(c)
{
	this.celda_media_seneca=c.querySelector('input');
	registrar_evento(this.celda_media_seneca, this.cambio_media_seneca, this, 'change');
}

Fila_calculo.prototype.asignar_celda_comportamiento=function(c)
{
	this.CELDA_COMPORTAMIENTO=c;
	c.REF_FILA=this;
}

Fila_calculo.prototype.finalizar_configuracion=function()
{
	var l=this.grupos.length;
	var i=0;
	while(i<l) this.grupos[i++].recalcular_media(false);
	this.calcular_media();	
}

Fila_calculo.prototype.calcular_media=function()
{
	if(this.celda_media_final)
	{
		var l=this.grupos.length;
		var i=0;
		var t=0.0;
	
		//Calcular según porcentajes reales.
		while(i<l)
		{
			var m=this.grupos[i].calcular_media();
			t+=m;
			++i;
		}

		if(this.CELDA_COMPORTAMIENTO)
		{
			var m=this.CELDA_COMPORTAMIENTO.calcular_media();
			t+=m;
		}
		else
		{
			alert('no hay celda comportamiento');
		}

		this.celda_media_final.innerHTML=t.toFixed(2);
//		this.celda_media_seneca.innerHTML=Math.round(t);
	}	
}

Fila_calculo.prototype.cambio_media_seneca=function()
{
	this.celda_media_seneca.disabled=true;
	this.celda_media_seneca.className='oculto';

	var valor=parseInt(this.celda_media_seneca.value, 10);

	//Truncar a entero, comprobar que está entre 1 y 10. Si no es así, aproximar.
	if(isNaN(valor)) valor=0;
	else if(valor < 0) valor=0;
	else if(valor > 10) valor=10;

	this.celda_media_seneca.value=valor;

	//Realizar solicitud en función de los valores que tenga el input.
	var url=URL_WEB+'guardar_datos_evaluacion.html?';
	var datos_post='';

	var tipo=this.celda_media_seneca.getAttribute('data-t');

	switch(tipo)
	{
		case 'nuevo': datos_post+='&t=nuevo&ida='+this.celda_media_seneca.getAttribute('data-ida')+'&tr='+this.celda_media_seneca.getAttribute('data-tr'); break;
		case 'actualizar': datos_post+='&t=actualizar&idn='+this.celda_media_seneca.getAttribute('data-idn'); break;
	}

	//Recoger solicitud, actualizar propiedades si es necesario.
	datos_post+='&v='+valor;
	var xml=new Lector_XML();
	xml.pasar_a_post(datos_post);
	xml.crear(url, this.post_grabar_media_seneca, this, false); //Ojo al ámbito...
}

Fila_calculo.prototype.post_grabar_media_seneca=function(v_xml)
{
	var r=preparar_respuesta_xml_standard(v_xml, false);
	switch(r.resultado)
	{
		case 0: alert(r.mensaje); break;
		case 1: 			
			var id_dato=r.resultado_att['idn'];
			this.celda_media_seneca.setAttribute('data-idn', id_dato);
			this.celda_media_seneca.setAttribute('data-t', 'actualizar');
			this.celda_media_seneca.disabled=false;
			this.celda_media_seneca.className='input_nota';
		break;
	}
}

/******************************************************************************/

function iniciar_tabla_datos(tabla)
{
	var obj_filas=Array();

	function generar_grupo_calculo()
	{
		var g=new Grupo_calculo();
		return g;
	}

	function procesar_fila(f)
	{
		var FC=new Fila_calculo();
		obj_filas.push(FC);

		var celdas=f.getElementsByTagName('td');
		var l=celdas.length;
		var i=2; //Las dos primeras son nombre e info.

		var G=generar_grupo_calculo();

		while(i < l)
		{
			switch(celdas[i].getAttribute("data-tipocelda"))
			{
				case 'g':
					G.asignar_input(celdas[i]);
				break;

				case 'm':
					G.asignar_span(celdas[i]);
					FC.insertar_grupo(G);
					G=null;
					G=generar_grupo_calculo();
				break;

				case 'mf':
					FC.asignar_celda_media(celdas[i]);
				break;

				case 'ms':
					FC.asignar_celda_media_seneca(celdas[i]);
				break;

				case 'c':
				{
					var CC=new Celda_comportamiento(celdas[i]);
					FC.asignar_celda_comportamiento(CC);					
				}
				break;
			}
			++i;		
		}

		FC.finalizar_configuracion();
	}

	var cuerpo=tabla.getElementsByTagName('tbody')[0];

	if(cuerpo)
	{
		var filas=cuerpo.getElementsByTagName('tr');
		var l=filas.length;
		var i=0;

		while(i<l)
		{
			procesar_fila(filas[i]);
			++i;
		}

	}
}

/******************************************************************************/

var FORM_COMPORTAMIENTO=null;
function Form_comportamiento()
{
	this.DOM_form=document.getElementById('form_comportamiento');
	this.DOM_nombre_alumno=document.getElementById('form_comportamiento_nombre_alumno');

	this.DOM_form.parentNode.removeChild(this.DOM_form);
	this.DOM_form.className='oculto';
	document.body.appendChild(this.DOM_form);

	this.CELDA_COMPORTAMIENTO_VINCULADA=null;

	this.ocupado=false;

	var aquello=this;

	registrar_evento(this.DOM_form.btn_ok, this.enviar, this, 'click');
	registrar_evento(this.DOM_form.btn_cancelar, this.cancelar, this, 'click');
}

Form_comportamiento.prototype.es_ocupado=function() {return this.ocupado;}

Form_comportamiento.prototype.cancelar=function()
{
	this.ocupado=false;
	this.DOM_form.reset();
	this.DOM_form.className='oculto';
}

Form_comportamiento.prototype.procesar_enviar=function(v_xml)
{
	var r=preparar_respuesta_xml_standard(v_xml, false);
	switch(r.resultado)
	{
		case 0: alert(r.mensaje); break;
		case 1:
			var nuevo_val=parseInt(r.resultado_att['val'], 10);
			var nueva_media=parseFloat(r.resultado_att['med'])
			this.CELDA_COMPORTAMIENTO_VINCULADA.recargar(nuevo_val, nueva_media);
		break;
	}

	this.desactivar_activar_form(false);
	this.cancelar();
}

Form_comportamiento.prototype.desactivar_activar_form=function(v)
{
	var l=this.DOM_form.length;
	var i=0;
	while(i<l) this.DOM_form[i++].disabled=v;
}

Form_comportamiento.prototype.enviar=function()
{
	this.desactivar_activar_form(true);

	var url=URL_WEB+'guardar_comportamiento_alumno.html?';
	var datos_post='';

	var valor=parseInt(this.DOM_form.valor.value, 10);

	if(!valor || valor < 0 || isNaN(valor))
	{
		alert('Debe introducir un valor mayor que cero');
		this.desactivar_activar_form(false);
	}
	else
	{
		var l=this.DOM_form.length;
		var i=0;

		while(i<l) datos_post+='&'+this.DOM_form[i].name+'='+escape(this.DOM_form[i++].value);

		var xml=new Lector_XML();
		xml.pasar_a_post(datos_post);
		xml.crear(url, this.procesar_enviar, this, false);
	}
}

Form_comportamiento.prototype.iniciar=function(celda_comportamiento, multiplicador, input)
{
	this.CELDA_COMPORTAMIENTO_VINCULADA=celda_comportamiento;

	this.DOM_form.id_comportamiento.value=this.CELDA_COMPORTAMIENTO_VINCULADA.id_comportamiento;
	this.DOM_nombre_alumno.innerHTML=this.CELDA_COMPORTAMIENTO_VINCULADA.nombre_alumno;
	this.DOM_form.multiplicador.value=multiplicador;
	this.DOM_form.className=multiplicador==1 ? 'iu_form positivo' : 'iu_form negativo';
	this.ocupado=true;
}

function invocar_comportamiento(celda_comportamiento, multiplicador, input)
{	
	if(!FORM_COMPORTAMIENTO) FORM_COMPORTAMIENTO=new Form_comportamiento();
	if(!FORM_COMPORTAMIENTO.es_ocupado()) FORM_COMPORTAMIENTO.iniciar(celda_comportamiento, multiplicador, input);
}

var tabla_datos=document.getElementById('tabla_datos');
if(tabla_datos) iniciar_tabla_datos(tabla_datos);
