<?
class IU_plegado
{
	private $clase=null;
	private $visible=0;
	private $callback=null;
	private $callback_params=null;
	private $auto=true;
	private $contenido=null;
	private $valor_boton=null;

	public function __construct($cb=null, $cbp=null)
	{
		$this->callback=$cb;
		$this->callback_params=$cbp;
	}

	public function establecer_valor_boton($v) {$this->valor_boton=$v;}
	public function establecer_callback($v) {$this->callback=$v;}
	public function establecer_callback_params($v) {$this->callback_params=$v;}
	public function establecer_clase($v) {$this->clase=$v;}
	public function establecer_contenido($v) {$this->contenido=$v;}
	public function establecer_auto_montado($v) {$this->auto=$v;}
	public function establecer_visible($v) {$this->visible=$v;}

	public function mostrar()
	{
		$ver_visible=(string)$this->visible;

		//Este parámetro hace que no genere una clase de plegado JS
		//Sólo lo usaremos para el form de plantilla oculto de lo 
		//contrario el de aparecerá con clase "oculto" cuando se copie 
		//y hará cosas raras.

		$ver_no_auto=$this->auto ? '' : 'data-uinoauto="1"';

		return <<<R

	<div class="iu_grupo_form {$this->clase}" data-iuvisible="{$ver_visible}" data-iucallback="{$this->callback}" data-iucallbackparams="{$this->callback_params}" {$ver_no_auto}>

		<input type="button" class="iu_btn_form" value="{$this->valor_boton}" />

		<div>
			{$this->contenido}
		</div>

	</div>
R;
	}
};

abstract class IU_herramientas
{
	public static function texto_url($texto, $caracter_no_legible='_')
	{
		$buscar=" \xc0\xc1\xc2\xc3\xc4\xc5\xe0\xe1\xe2\xe3\xe4\xe5\xd2\xd3\xd4\xd5\xd6\xd8\xf2\xf3\xf4\xf5\xf6\xf8\xc8\xc9\xca\xcb\xe8\xe9\xea\xeb\xc7\xe7\xcc\xcd\xce\xcf\xec\xed\xee\xef\xd9\xda\xdb\xdc\xf9\xfa\xfb\xfc\xff\xd1\xf1_.,/:-?!ªº";
		$reempl=$caracter_no_legible."AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn".$caracter_no_legible.$caracter_no_legible.$caracter_no_legible.$caracter_no_legible.$caracter_no_legible.$caracter_no_legible.$caracter_no_legible.$caracter_no_legible."AO";
		return strtr($texto, $buscar, $reempl);
	}
};
?>
