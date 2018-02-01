<?
class Abstraccion_archivo
{
	private $url_archivo=null;
	private $ruta_archivo=null;
	private $extension_archivo=null;

	private $id_item=null;
	private $ruta=null;
	private $clave=null;
	private $tamano=null;
	private $extension_obligatoria=null;

	public function acc_url_archivo() {return $this->url_archivo;}
	public function acc_ruta_archivo() {return $this->ruta_archivo;}
	public function acc_extension_archivo() {return $this->extension_archivo;}

	public function __construct($id_item, $ruta, $clave, $tamano=300, $extension=null)
	{
		$this->id_item=$id_item;
		$this->ruta=$ruta;
		$this->clave=$clave;
		$this->tamano=$tamano;
		$this->extension_obligatoria=$extension;

		$this->cargar_archivo();
	}

	public function refrescar() {$this->cargar_archivo();}
	public function sincronizar_id($id) {$this->id_item=$id;}

	private function cargar_archivo()
	{
		$ruta=$this->ruta.$this->id_item.'.'.$this->extension_obligatoria;	//Realmente aquí no es "obligatoria"...

		if(file_exists(Constantes::RUTA_SERVER.$ruta) && is_file(Constantes::RUTA_SERVER.$ruta))
		{
			$this->url_archivo=Constantes::URL_WEB.$ruta;
			$this->ruta_archivo=Constantes::RUTA_SERVER.$ruta;
			$this->extension_archivo=Herramientas::obtener_extension_archivo($this->ruta_archivo);

		}
		else
		{
			$this->url_archivo=null;
			$this->ruta_archivo=null;
			$this->extension_archivo=null;
		}
	}

	public function subir_archivo(&$archivos)
	{
		$ruta_adjunto=Constantes::RUTA_SERVER.$this->ruta.$this->id_item;

		//Subir archivo libre...
		if(!$this->extension_obligatoria)
		{
			$extension=Herramientas::subir_archivo_libre($archivos[$this->clave], $ruta_adjunto, $this->tamano);
		}
		//Forzar extension...
		else
		{
			$extension=Herramientas::subir_archivo_extension($archivos[$this->clave], $ruta_adjunto, $this->extension_obligatoria, $this->tamano);
		}

		//Eliminar el antiguo...
		if($extension && $extension!=$this->extension_archivo && $this->ruta_archivo)
		{
			$this->eliminar_archivo();
		}
		else
		{
			$this->cargar_archivo();
		}
	}

	public function eliminar_archivo()
	{
		if($this->ruta_archivo)
		{
			unlink($this->ruta_archivo);
			$this->cargar_archivo();
		}
	}
}
?>
