//Registrador de eventos según modelo moderno.
//https://developer.mozilla.org/en-US/docs/Web/Events

function registrar_evento(elemento, metodo, ambito, evento)
{
	if(elemento.addEventListener)
	{
		elemento.addEventListener(evento, metodo.bind(ambito));
	}
	else
	{
		switch(evento)
		{
			case 'click': elemento.onclick=metodo.bind(ambito); break;
			case 'change': elemento.onchange=metodo.bind(ambito); break;
			case 'keydown': elemento.onkeydown=metodo.bind(ambito); break;
		}
	}
}

/******************************************************************************/

function Callbacks_plegado_ui()
{
	this.callbacks=Array();
}

Callbacks_plegado_ui.prototype.nuevo_callback=function(clave, valor){this.callbacks[clave]=valor;}
Callbacks_plegado_ui.prototype.llamar_callback=function(clave, params, f, h, vis)
{
	if(this.callbacks[clave]) this.callbacks[clave](vis, f, h, params);
	else console.log('Error: callback no correcto al desplegar UI');
}

var CPUI=new Callbacks_plegado_ui();

//Registrar callback.Se entiende que clave es un entero, y valor un objeto del tipo function.
function registrar_callback_plegado_forms_ui(clave, valor) {CPUI.nuevo_callback(clave, valor);}

function Grupo_plegado(g)
{
	var nodos=g.children;

	if(nodos.length==2)
	{
		this.h=nodos[0];
		this.f=nodos[1];
		this.clase_original=this.f.className;

		//Comprobar si se inicia plegado o no.
		this.vis=parseInt(this.f.getAttribute('data-iuvisible'), 10);			
		if(isNaN(this.vis)) this.vis=false;
		this.callback=parseInt(g.getAttribute('data-iucallback'), 10);
		this.callback_params=g.getAttribute('data-iucallbackparams');

		if(!this.vis) this.f.className='oculto';

		registrar_evento(this.h, this.click, this, 'click');
	}
	else 
	{
		console.log('ERROR: Se intenta usar IU_plegado_forms para '+g.children.length+' nodos');
	}
}

Grupo_plegado.prototype.click=function()
{
	this.vis=!this.vis;
	this.f.className=this.vis ? this.clase_original : 'oculto';
	if(!isNaN(this.callback)) 
	{
		CPUI.llamar_callback(this.callback, this.callback_params, this.h, this.f, this.vis);
	}
}

function procesar_plegado_forms_ui()
{	
	function procesar_grupo(g) 
	{
		var no_auto=g.getAttribute('data-uinoauto'); //Este atributo hace que no se procese el grupo de plegado.
		if(!no_auto) var G=new Grupo_plegado(g);
	}
	var grupos=document.querySelectorAll('.iu_grupo_form');
	var i=0, l=grupos.length;
	while(i < l) procesar_grupo(grupos[i++]);
}

//A partir del cuerpo de un grupo de plegado, despachar el click del botón.
function iu_despachar_click_plegado_desde_cuerpo(c)
{
	var btn=c.parentNode.querySelector('input');		
	if(btn.dispatchEvent) btn.dispatchEvent(new MouseEvent('click'));
	else btn.onclick();
}

/******************************************************************************/

function Grupo_tabs_ui(g)
{
	var nodos=g.children;
	if(nodos.length==2)
	{
		this.cb=g.getAttribute('data-callback');

		this.cab=nodos[0];
		this.lis=nodos[1];
		this.items_cab=this.cab.children;
		this.items_lis=this.lis.children;

		if(this.items_cab.length!=this.items_lis.length)
		{
			console.log('ERROR UI: No se pueden generar tabs '+this.items_cab.length+'!='+this.items_lis.length);
		}
		else
		{
			var i=0;
			var l=this.items_cab.length;
			while(i < l) this.generar_eventos(i++);

			//Click en el activo...
			i=0;
			while(i < l)
			{
				if(this.items_cab[i].getAttribute('data-uitabactual')) 
				{
					this.click(this.items_cab[i], this.items_lis[i], this.items_lis[i].className);
					break;
				}
				++i;
			}
		}
	}
}

Grupo_tabs_ui.prototype.generar_eventos=function(i)
{
	var aquello=this;
	var cab=this.items_cab[i];
	var lis=this.items_lis[i];
	var clase=lis.className;
	registrar_evento(cab, function() {aquello.click(cab, lis, clase);}, this, 'click');
}

Grupo_tabs_ui.prototype.click=function(cab, lis, clase)
{
	var i=0;
	var l=this.items_cab.length;
	var actual=-1;
	while(i < l) 
	{
		this.items_lis[i].className='oculto';
		this.items_cab[i].className='';

		if(this.items_cab[i]==cab) actual=i;
		++i;
	}

	cab.className='tabs_ui_tab_activa';
	lis.className=clase;

	if(this.cb)
	{
		//Se asume que está en el ámbito global.
		if(window[this.cb])
		{
			window[this.cb](cab, lis, actual);
		}
		else
		{
			console.log('ERROR: Callback tab no está en ámbito global');
		}
	}
}

function procesar_tabs_ui()
{	
	var grupos=document.querySelectorAll('.iu_tabs');
	var i=0, l=grupos.length;
	while(i < l) var t=new Grupo_tabs_ui(grupos[i++]);
}

/******************************************************************************/

/*
function procesar_enlaces_eliminar_ui()
{
	var enlaces=document.querySelectorAll('.iu_enlace_eliminar');
	var i=0, l=enlaces.length;

	function procesar_enlace(enlace)
	{
		if(!enlace.onclick)
		{
			registrar_evento(enlace, function() {return confirm('Confirmar el borrado del elemento')}, this, 'click');
		}
	}

	while(i < l) procesar_enlace(enlaces[i++]);
}
*/

/******************************************************************************/

function Bloqueador_ui()
{
	this.DOM=document.createElement('div');
	this.DOM.className='iu_bloqueador';

	this.activo=false;
}

Bloqueador_ui.prototype.activar=function()
{
	if(!this.activo)
	{
		this.activo=true;
		document.body.appendChild(this.DOM);
	}
}

Bloqueador_ui.prototype.desactivar=function()
{
	if(this.activo)
	{
		this.activo=false;
		document.body.removeChild(this.DOM);
	}
}

procesar_plegado_forms_ui();
procesar_tabs_ui();
//procesar_enlaces_eliminar_ui();

var BLOQUEADOR_UI=new Bloqueador_ui();
