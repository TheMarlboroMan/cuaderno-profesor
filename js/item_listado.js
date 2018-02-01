function Item_listado(v_dom)
{
	this.DOM_parentNode=null;
	this.DOM_item=null;
	this.DOM_items=Array(); //Cada elemento que sea hijo de DOM_Item. Automático.
	this.FORM=null;

	if(v_dom)
	{
		this.asignar_dom(v_dom);
	}
}

Item_listado.prototype.asignar_dom=function(v_dom)
{
	this.DOM_parentNode=v_dom.parentNode;
	this.DOM_item=v_dom;

	var l=this.DOM_item.children.length;
	var i=0;
	while(i < l) this.DOM_items.push(this.DOM_item.children[i++]);
}

Item_listado.prototype.item=function(i) 
{
	return this.DOM_items[i];
}

Item_listado.prototype.ocultar_contenido=function()
{
	var l=this.DOM_items.length;
	var i=0;
	while(i < l) this.DOM_item.removeChild(this.DOM_items[i++]);
}

Item_listado.prototype.destruir_contenido=function()
{
	this.DOM_parentNode.removeChild(this.DOM_item);
}

Item_listado.prototype.restaurar_contenido=function()
{
	var l=this.DOM_items.length;
	var i=0;
	while(i < l) this.DOM_item.appendChild(this.DOM_items[i++]);
}

Item_listado.prototype.poner_form=function(f)
{
	this.FORM=f;
	this.DOM_item.appendChild(this.FORM);
}

Item_listado.prototype.retirar_y_destruir_form=function()
{
	this.DOM_item.removeChild(this.FORM);
	
	//Esto realmente no funcionaría...	
	this.FORM.btn_ok.onclick=null;
	this.FORM.btn_cancelar.onclick=null;
	delete this.FORM;
	this.FORM=null;
}

/* Sólo incluye los métodos de trabajo genéricos, muchos de los cuales deben
ser sobreescritos.*/

function Item_listado_base(v_dom)
{
	this.IC=new Item_listado(v_dom);
	
	//Clona el formulario de creación o modificación. El formulario debe 
	//venir completo en un principio. Con los métodos que los invoquen ya
	//los cambiaremos a gusto. En ningún momento meteremos el id de algo
	//que estemos editando: eso ya lo hace sólo.
	this.clonar_form=function() 
	{
		console.log('Llamada a clonar form generica');
		return null;
	}

	//Clona el item del listado vacío y lo devuelve.
	this.clonar_item_listado=function()
	{
		console.log('Llamada a clonar item listado generica');
		return null;
	}

	//Devuelve una referencia al listado donde están contenidos los items.
	//Recibirá siempre como parámetro el formulario, por si es necesario
	//para localizar el listado (que lo será). Cuando lo estamos llamando
	//el objeto IL aún no está en el DOM, de modo que no podemos usarlo
	//para localizarlo (sólo se llama al crear).
	this.obtener_listado=function(f) 
	{
		console.log('Llamada a obtener listado generica');
		return null;
	}

	//Devuelve la clave que se recibe para saber que estamos creando.
	this.obtener_valor_logica_crear=function() 
	{
		console.log('Llamada a obtener valor logica crear generica');
		return null;
	}

	//Devuelve la clave que se recibe para saber que estamos modificando.
	this.obtener_valor_logica_modificar=function() 
	{
		console.log('Llamada a obtener valor logica modificar generica');
		return null;
	}

	//Esto hay que implementarlo. Url que da los datos corrientes de un item.
	this.url_obtener_datos_actualizar=function(vid) 
	{
		console.log('Llamada a obtener datos actualizar generica');
		return null;
	}

	//Una vez recibidos los datos de modificación se llamaría a este método
	//si está presente. Recibe como parámetros el formulario y una copia
	//del lector. Puede usarse para bloquear elementos del formulario para
	//impedir cambios si es necesario. Añadir o eliminar botones, etc.
	//El orden de los parámetros es form y lector.
	this.recepcion_datos_modificar=null;

	//Devuelve la url a la que se envía la información de actualización.
	this.url_envio_actualizar=function() 
	{
		console.log('Llamada a url envio actualizar generica');
		return null;
	}

	//Devuelve la url a la que se envía la información de creación.
	this.url_envio_crear=function() 
	{
		console.log('Llamada a url envio crear generica');
		return null;
	}

	//Devuelve la url a la que se envía la información de eliminación. Esta
	//url no debe contener ningún tipo de dato con respecto al identificador
	//o al tipo de mostrado.
	this.url_eliminar=function() 
	{
		console.log('Llamada a url eliminar generica');
		return null;
	}

	//Recibe el contenedor del elemento y se recibe una vez eliminado. Puede
	//servir, por ejemplo, para ocultar el contenedor si se ha eliminado el
	//último elemento de la lista. Recibe también el lector de datos del
	//xml resultante (descendido ya un nivel) y el array de atributos del 
	//mismo.
	this.post_eliminar=null; //function(v_contenedor, l, atributos) {}


	//Recibe el formulario de creación: se entiende llama cuando se ha
	//finalizado el proceso de creación con éxito, antes de limpiar el
	//valor de los inputs del form y desbloquearlo. Se puede usar para,
	//por ejemplo, ocultar el form.
	this.post_crear=null; //function(f) {}

	//Una vez modificado o creado un elemento se recibe el lector XML con 
	//los datos del mismo, con la intención de modificar los contenidos del 
	//DOM con los nuevos datos. Se recibe el lector de datos, la acción y el 
	//array de atributos de datos.
	this.recibir_datos_xml=function(l, accion, att)
	{
		console.log('Llamada a recibir respuesta XML generica');
	}

	this.validar_form=function(f) {return false;}
}

Item_listado_base.prototype.url_datos_actualizar=function(vid) 
{
	var url=this.url_obtener_datos_actualizar(vid);
	url+='&id_xml='+vid;
	return url;
}

Item_listado_base.prototype.generar_url_eliminar=function() 
{
	var url=this.url_eliminar();
	url+='&rxml=1';
	return url;
}

Item_listado_base.prototype.generar_form=function(vid) 
{
	var f=this.clonar_form();
	if(f && vid) //Modificar...
	{
		var input=document.createElement('input');
		input.type="hidden";
		input.name="id_item_form";
		input.value=vid;
		f.appendChild(input);
	}

	return f;
}

Item_listado_base.prototype.generar_item_listado=function() 
{
	var l=this.clonar_item_listado();
	return l;
}

Item_listado_base.prototype.asignar_dom=function(v_dom) {this.IC.asignar_dom(v_dom);}
Item_listado_base.prototype.ocultar_contenido=function(){this.IC.ocultar_contenido();}
Item_listado_base.prototype.destruir_contenido=function(l)
{	
	this.IC.destruir_contenido();
	var atributos=l.atributos();
	l.bajar(0);
	if(this.post_eliminar) this.post_eliminar(this.IC.DOM_parentNode, l, atributos);
}
Item_listado_base.prototype.restaurar_contenido=function(){this.IC.restaurar_contenido();}
Item_listado_base.prototype.poner_form=function(f){this.IC.poner_form(f);}
Item_listado_base.prototype.retirar_y_destruir_form=function(f) {this.IC.retirar_y_destruir_form();}
Item_listado_base.prototype.recibir_respuesta_xml_modificacion=function(v_xml)
{
	var r=preparar_respuesta_xml_standard(v_xml, false);

	if(!r.resultado)
	{
		alert(r.mensaje);
	}
	else
	{
		var l=r.l;
		var accion=r.resultado_att['accion'];
		var atributos=l.atributos();
		l.bajar(0);

		this.retirar_y_destruir_form();
		this.restaurar_contenido();
		this.recibir_datos_xml(l, accion, atributos);

		delete this;
	}
}

function eliminar_dato(vid, linea, IL)
{
	if(!confirm('Los datos van a ser eliminados de forma definitiva'))
	{
		return;
	}
	else
	{
		IL.asignar_dom(linea);
		IL.ocultar_contenido();

		var url=IL.generar_url_eliminar();

		if(!url.length)
		{
			console.log('No se ha especificado url de eliminado');
		}
		else
		{
			function procesar_eliminar(v_xml)
			{
				var r=preparar_respuesta_xml_standard(v_xml, false);

				if(!r.resultado)
				{
					alert(r.mensaje);
					IL.restaurar_contenido();
				}
				else
				{
					IL.destruir_contenido(r.l);
				}
			}

			var xml=new Lector_XML();
			var datos_post='&id_del='+vid;
			xml.pasar_a_post(datos_post);
			xml.crear(url, procesar_eliminar, this, false);
		}
	}
}

function actualizar_dato(vid, linea, IL)
{
	//Hacer llamada de ajax para recuperar la info.
	var url=IL.url_datos_actualizar(vid);

	if(!url.length)
	{
		console.log('No se ha especificado url para obtener datos de actualizacion');
		return;
	}

	var xml=new Lector_XML();
	xml.crear(url, procesar_obtener_datos, this, false);

	function procesar_obtener_datos(v_xml)
	{
		var r=preparar_respuesta_xml_standard(v_xml, false);

		if(!r.resultado)
		{
			alert(r.mensaje);
		}
		else
		{
			function procesar_modificacion(v_xml) 
			{
				IL.recibir_respuesta_xml_modificacion(v_xml);
			}

			IL.asignar_dom(linea);
			IL.ocultar_contenido();

			//Clonar formulario y rellenar contenido con info ajax.
			var f=IL.generar_form(vid);

			if(!f)
			{
				console.log('No se ha definido el metodo para clonar form');
				return;
			}

			var l=r.l;

			if(IL.recepcion_datos_modificar)
			{
				var cp=new Lector_doc(l.obtener_nodo_real());
				IL.recepcion_datos_modificar(f, cp);
			}

			l.bajar(0);
			form_cargar_desde_nodo_xml(f, l.obtener_nodo_real());

			//Preparar formulario para enviar y cancelar.
			f.mwlogica.value=IL.obtener_valor_logica_modificar();

			if(!f.mwlogica.value.length)
			{
				console.log('No se ha definido el metodo para obtener valor logica modificar');
				return;
			}

			f.className='iu_form'

			//Enviar realiza llamada rellena la nueva información y recupera el contenido de nuevo.
			f.btn_ok.onclick=function()
			{
				if(IL.validar_form(f)) 
				{
					form_desactivar_campos(f)
					var url=IL.url_envio_actualizar();

					if(!url.length)
					{
						console.log('No se ha definido el metodo para obtener url logica modificar');
						return;
					}

					form_enviar_ajax_post(f, url, procesar_modificacion, this);
				}
			}

			//Cancelar destruye el form y recupera contenido tal y como estaba.
			f.btn_cancelar.onclick=function()
			{
				IL.retirar_y_destruir_form();
				IL.restaurar_contenido();
			}

			IL.poner_form(f);
		}
	}
}

function crear_dato(f, IL)
{	
	var valor_logica=IL.obtener_valor_logica_crear()	
	f.mwlogica.value=valor_logica;

	form_desactivar_campos(f)
	var url=IL.url_envio_crear();

	if(!url.length)
	{
		console.log('No se ha definido el metodo para obtener valor logica crear');
		return;
	}
	else
	{
		function procesar_creacion(v_xml) 
		{
			var r=preparar_respuesta_xml_standard(v_xml, false);
			if(!r.resultado)
			{
				alert(r.mensaje);
			}
			else
			{
				var accion=r.resultado_att['accion'];
				var item=IL.generar_item_listado();

				if(!item)
				{
					console.log('ERROR: No se ha generado el item');
				}
				else
				{
					IL.asignar_dom(item);
					var atributos=r.l.atributos();
					r.l.bajar(0); //Siempre están uno por debajo...
					IL.recibir_datos_xml(r.l, accion, atributos);
					var listado=IL.obtener_listado(f);
					if(!listado)
					{
						console.log('ERROR: No se ha localizado el listado');
					}
					else
					{
						if(listado.children.length) listado.insertBefore(item, listado.children[0]);
						else listado.appendChild(item);
					}

					if(IL.post_crear) IL.post_crear(f);
				}
			}

			f.reset();
			form_activar_campos(f);
		}

		form_enviar_ajax_post(f, url, procesar_creacion, this);
	}

}
