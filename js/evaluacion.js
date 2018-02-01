function click_tab_evaluacion(tab, cuerpo, actual)
{
	var id_grupo=document.getElementById('idg').value;
	var trimestre=actual+1;

	var url=URL_WEB+'grupos/_/'+id_grupo+'/gestionar.html?mwlogica=actualizar_trimestre_grupo';
	var datos_post='idg='+id_grupo+'&trimestre='+trimestre;
	var xml=new Lector_XML();
	xml.pasar_a_post(datos_post);
	xml.crear(url, procesar_click_tab_evaluacion, this, false);
}

function procesar_click_tab_evaluacion(v_xml)
{
	var r=preparar_respuesta_xml_standard(v_xml, false);

	if(!r.resultado)
	{
		alert(r.mensaje);
	}
}
