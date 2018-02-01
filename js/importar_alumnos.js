function comprobar_importacion_archivo(f)
{
	f.btn_enviar.disabled=true;

	if(!f.archivo.value.length)
	{
		alert('Debe aportar un archivo CSV');
		f.btn_enviar.disabled=false;
	}
	else
	{
		f.submit();
	}
}

function comprobar_importacion_texto(f)
{
	f.btn_enviar.disabled=true;

	if(!f.texto.value.length)
	{
		alert('Debe introducir un alumno por linea, con apellidos y nombre separados por una coma.');
		f.btn_enviar.disabled=false;
	}
	else
	{
		f.submit();
	}
}

var form_alumnos_fichero=document.getElementById('form_alumnos_fichero');
if(form_alumnos_fichero) registrar_evento(form_alumnos_fichero.btn_enviar, function(){comprobar_importacion_archivo(form_alumnos_fichero);}, this, 'click');

var form_alumnos_texto=document.getElementById('form_alumnos_texto');
if(form_alumnos_texto) registrar_evento(form_alumnos_texto.btn_enviar, function(){comprobar_importacion_texto(form_alumnos_texto);}, this, 'click');

