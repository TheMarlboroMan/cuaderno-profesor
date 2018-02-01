function preparar_respuesta_xml_standard(v_xml, v_es_frame)
{
	var l=v_es_frame ? new Lector_doc(v_xml) : new Lector_doc(v_xml.raiz);

	l.bajar(0);

	var resultado_att=l.atributos();
	var resultado=parseInt(l.obtener(), 10);
	var mensaje_att=l.atributos();
	var mensaje=l.obtener();
	var resultados={
		'resultado' : resultado,
 		'resultado_att' : resultado_att, 
		'mensaje' : mensaje, 
		'mensaje_att' : mensaje_att,  
		'l' : l};

	return resultados;
}

//AquÃ­ empieza la clase en si.

function Lector_XML(v_documento, v_funcion_callback, v_scope_callback, v_es_cadena)
{
	this.lector=null;
	this.documento=null;
	this.funcion_callback=null;
	this.scope_callback=null;
	this.es_cadena=null;
	this.metodo='GET';
	this.datos_post=null;

	//Estos son para la navegaciÃ³n.
	this.raiz=null;

	if(v_documento && v_funcion_callback)
	{
		this.crear(v_documento, v_funcion_callback, v_scope_callback, v_es_cadena);
	}
}

Lector_XML.prototype.pasar_a_post=function(v_datos_post)
{
	this.metodo='POST';
	this.datos_post=v_datos_post;
}


Lector_XML.prototype.crear=function(v_documento, v_funcion_callback, v_scope_callback, v_es_cadena) //La funciÃ³n callback debe recibir un parÃ¡metro: el propio lector.
{
	this.documento=v_documento;
	this.funcion_callback=v_funcion_callback;
	this.scope_callback=v_scope_callback;
	this.es_cadena=(v_es_cadena ? true : false);
	var aquello=this;

	if(this.es_cadena)
	{
		var temp=new DOMParser();
		this.lector=temp.parseFromString(this.documento, "text/xml");

		if(this.funcion_callback)
		{
			this.preparar.call(this);
		}
		else
		{
			this.raiz=this.lector.documentElement;
		}
	}
	else
	{
		var temp=new XMLHttpRequest();

		if(this.metodo=='GET')
		{
			temp.open(this.metodo, this.documento , true);
			temp.onreadystatechange=function()
			{
				if(temp.readyState==4)
				{
					aquello.lector=temp.responseXML;
					aquello.preparar.call(aquello);
				}
			}
			temp.send(null);
		}
		else if(this.metodo=='POST')
		{
			temp.open(this.metodo, this.documento, true);	//Prepara...						

			temp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			temp.setRequestHeader("Content-length", this.datos_post.length);
			temp.setRequestHeader("Connection", "close");

			temp.onreadystatechange=function()
			{
				if(temp.readyState==4)
				{
					aquello.lector=temp.responseXML;			
					aquello.preparar.call(aquello);
				}
			}
			temp.send(this.datos_post);	//Lanza.	
		}
	}
}

Lector_XML.prototype.preparar=function()
{	
	if(!this.lector) console.log("No se ha cargado el documento XML en "+this.documento);
	else
	{
		this.raiz=this.lector.documentElement;
		if(this.scope_callback) this.funcion_callback.call(this.scope_callback, this);
		else this.funcion_callback(this);
	}
}
