<?
class Excepcion_consulta_mysql extends Exception
{
	public function acc_mensaje() {return $this->message;}
	public function acc_codigo() {return $this->code;}

	public function __construct($codigo, $mensaje)
	{
		$this->code=$codigo;
		$this->message=$mensaje;
	}
}
?>
