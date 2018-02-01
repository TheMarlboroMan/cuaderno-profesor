function form_error(resultado_comprobacion, input)
{
	input.className=resultado_comprobacion ? '' : 'error';
	return resultado_comprobacion ? 0 : 1;
}

function form_validar_cadena(val)
{
	return val.length > 0;
}

function form_validar_entero(val, vmin, vmax)
{
	var val=parseInt(val, 10);
	return !isNaN(val) && val >= vmin && val <= vmax;
}

//Se espera un primer nodo con atributos sencillos y dentro varios nodos cdata
//con atributo n. El nombre de los primeros atributos y el valor de "n" son
//los nombres de los campos del form.

function form_cargar_desde_nodo_xml(f, v_nodo)
{
	var l=new Lector_doc(v_nodo);

	var att=l.atributos();
	var i=0;

	for(i in att)
	{
		var nombre_att=i;
		var valor_att=att[nombre_att];
		if(f[nombre_att]) f[nombre_att].value=valor_att;
	}

	var nodos=l.cuenta_nodos();

	var i=0;
	l.bajar(0);
	while(i < nodos)
	{
		var n=l.atributo('n');
		var val=l.obtener();
		if(f[n]) f[n].value=val;
		++i;
	}
}

function form_desactivar_campos(f)
{
	var l=f.length;
	var i=0;
	while(i < l) f[i++].disabled=true;
}

function form_activar_campos(f)
{
	var l=f.length;
	var i=0;
	while(i < l) f[i++].disabled=false;
}

function form_enviar_ajax_post(f, url, callback, ambito)
{
	var datos_post='';
	var lon=f.length;
	var i=0;
	while(i < lon) datos_post+='&'+f[i].name+'='+escape(f[i++].value);

	var xml=new Lector_XML();
	xml.pasar_a_post(datos_post);
	xml.crear(url, callback, ambito, false);
}
