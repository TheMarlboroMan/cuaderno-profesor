
//Funciones de movimiento...

function Lector_doc(nodo)
{
	this.posicion=null;
	this.ir_a(nodo);
}

Lector_doc.prototype.atributo=function(v_valor)
{
	if(this.posicion.atributos[v_valor])
	{
		return this.posicion.atributos[v_valor];
	}
	else
	{
		return null;
	}
}


Lector_doc.prototype.tiene_atributo=function(v_valor)
{
	if(this.posicion.atributos[v_valor]) return true;
	else return false;
}

Lector_doc.prototype.atributos=function() {return this.posicion.atributos;}
Lector_doc.prototype.texto=function() {return this.posicion.texto;}
Lector_doc.prototype.tag=function() {return this.posicion.tag;}
Lector_doc.prototype.cuenta_nodos=function() {return this.posicion.cuenta_nodos;}
Lector_doc.prototype.cuenta_atributos=function() {return this.posicion.cuenta_atributos;}
Lector_doc.prototype.obtener_nodo_real=function() {return this.posicion.actual;}


Lector_doc.prototype.ir_a=function(nodo)
{
	if(nodo.nodeType!=1)
	{
		return false;
	}
	else
	{
		var temp=new this.Nodo(nodo);
		this.posicion=temp;
//		delete(temp);
		temp=null;
		this.posicion.informacion();

		return true;
	}
}

Lector_doc.prototype.avanzar=function()
{
	var siguiente=this.posicion.actual.nextSibling;

	//Vamos a buscar el prÃ³ximo nodo...
	while(siguiente && siguiente.nodeType!=1)
	{
		siguiente=siguiente.nextSibling;
	};

	if(siguiente)
	{
		var temp=new this.Nodo(siguiente);
		this.posicion=temp;
		temp=null;
		this.posicion.informacion();

		return true;
	}
	else
	{
		return false;
	}
}

//Como avanzar pero devolverÃ­a el texto del nodo... Ojo al usar esto: si no
//puede avanzar mÃ¡s NO devolverÃ¡ error.

Lector_doc.prototype.obtener=function()
{
	var siguiente=this.posicion.actual.nextSibling;

	var contenido=this.posicion.texto;

	//Vamos a buscar el prÃ³ximo nodo...
	while(siguiente && siguiente.nodeType!=1)
	{
		siguiente=siguiente.nextSibling;
	};

	if(siguiente)
	{
		var temp=new this.Nodo(siguiente);
		this.posicion=temp;
		temp=null;
		this.posicion.informacion();
	}

	return contenido;
}

Lector_doc.prototype.retroceder=function()
{
	var anterior=this.posicion.actual.previousSibling;

	//Vamos a buscar el prÃ³ximo nodo...
	while(anterior && anterior.nodeType!=1)
	{
		anterior=anterior.previousSibling;
	};

	if(anterior)
	{
		var temp=new this.Nodo(anterior);
		this.posicion=temp;
//		delete(temp);
		temp=null;
		this.posicion.informacion();

		return true;
	}
	else
	{
		return false;
	}
}

Lector_doc.prototype.bajar=function(nodo)	//Avanza dentro de un nuevo nodo...
{
	if(nodo<this.posicion.cuenta_nodos)
	{
		var temp=new this.Nodo(this.posicion.nodos[nodo]);
		this.posicion=temp;
//		delete(temp);
		temp=null;
		this.posicion.informacion();
		
		return true;
	}
	else
	{
		return false;
	}
}

Lector_doc.prototype.subir=function()	//Vuelve al nodo padre...
{
	if(this.posicion.padre!=null)
	{
		var temp=new this.Nodo(this.posicion.padre);
		
		this.posicion=temp;
//		delete(temp);
		temp=null;
		this.posicion.informacion();

		return true;
	}
	else
	{
		return false;
	}
}

//Un nodo...

Lector_doc.prototype.Nodo=function(elemento)
{
	this.actual=elemento;	//El nodo en si...
	this.nodos=Array();	
	this.atributos=Array();
	this.padre=null;
	this.tag=null;
	this.texto=null;
}

Lector_doc.prototype.Nodo.prototype.informacion=function()
{		
	if(this.actual.hasChildNodes)
	{
		var longitud=this.actual.childNodes.length;
		var x;

		for(x=0; x<longitud; x++)
		{
			if(this.actual.childNodes[x].nodeType==1)	//Uno es... Un nodo, tipo ELEMENT_NODE
			{				
				this.nodos.push(this.actual.childNodes[x]);
			}
		}
	}

	if(this.actual.attributes)
	{
		var longitud=this.actual.attributes.length;
		var x;
//		for(x=0; x<longitud; x++)
//		{
//			this.atributos.push(this.actual.attributes[x].nodeValue);
//		}

		//Ahora con arrays asociativos...
		for(x=0; x<longitud; x++)
		{
			this.atributos[this.actual.attributes[x].nodeName]=this.actual.attributes[x].nodeValue;
		}
	}

	this.cuenta_atributos=this.atributos.length/2; //Contamos los atributos, la mitad porque estÃ¡n duplicados por Ã­ndice y clave.
	this.cuenta_nodos=this.nodos.length; //Contamos los nodos

	if(this.actual.parentNode)
	{
		this.padre=this.actual.parentNode;
	}
	else
	{
		this.padre=null;
	}

//Esto hace BOOM en IE.
//	this.padre=( this.actual.parentNode=='object XMLDocument' || this.actual.parentNode=='[object]' ? null : this.actual.parentNode);

	this.tag=this.actual.tagName;
	this.texto=( this.actual.text ? this.actual.text : this.actual.textContent);
	if(!this.texto) this.texto='';	//En IE hay errores si el texto estÃ¡ vacÃ­o...
}

