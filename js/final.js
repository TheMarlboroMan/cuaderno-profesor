function validar_input(input)
{
	var val=parseFloat(input.value, 10);
	if(isNaN(val)) val=0;

	if(val < 1)
	{
		input.value=1;
	}
	else if(val > 10) 
	{
		input.value=10;
	}
}

function asignar_como_celda_final(celda)
{
	var input=celda.getElementsByTagName('input')[0];

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

				input.disabled=false;
				input.className='input_nota';
			break;
		}
	}

	function cambio()
	{
		validar_input(input);

		input.disabled=true;
		input.className='oculto';

		var url=URL_WEB+'guardar_datos_alumno_final.html?';
		var datos_post='';

		var tipo=input.getAttribute('data-t');

		switch(tipo)
		{
			case 'nuevo': datos_post+='&t=nuevo&ida='+input.getAttribute('data-ida'); break;
			case 'actualizar': datos_post+='&t=actualizar&idn='+input.getAttribute('data-idn'); break;
		}

		datos_post+='&v='+input.value;
		var xml=new Lector_XML();
		xml.pasar_a_post(datos_post);
		xml.crear(url, post_grabar_input, this, false); //Ojo al ámbito...
	}

	registrar_evento(input, cambio, this, 'change');
}

function iniciar_tabla_datos(tabla)
{
	function procesar_fila(f)
	{
		var celdas=f.getElementsByTagName('td');
		var l=celdas.length;
		var i=2; //Las dos primeras son nombre e info.

		while(i < l)
		{
			if(celdas[i].getAttribute("data-tipocelda")=='nf')
			{
				asignar_como_celda_final(celdas[i]);
			}
			++i;		
		}
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

var tabla_datos=document.getElementById('tabla_final');
if(tabla_datos) iniciar_tabla_datos(tabla_datos);
