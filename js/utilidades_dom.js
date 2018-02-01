function Almacenaje_dom(v_item)
{
	var contenedor=Array();
	var aquello=this;

	var l=v_item.childNodes.length;	
	var i=0;
	while(i < l) contenedor.push(v_item.childNodes[i++]);
	v_item.innerHTML='';

	this.recuperar=function(v_item)
	{
		var l=contenedor.length;	
		var i=0;
		while(i < l) v_item.appendChild(contenedor[i++]);
		contenedor.length=0;
	}	

	this.destruir=function()
	{
		contenedor.length=0;
	}
}
