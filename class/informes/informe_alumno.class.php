<?
class Informe_alumno
{
	private $usuario;
	private $alumno;
	private $grupo;
	private $trimestre;

	public function __construct(Usuario &$u, Alumno $a, $trimestre)
	{
		$this->usuario=&$u;
		$this->alumno=&$a;
		$this->trimestre=$trimestre;
		$this->grupo=new Grupo($this->alumno->acc_id_grupo());
	}

	public function generar_texto()
	{
		$media_final=0;

		//Composición de evaluables.
		$VER_EVALUABLES=null;
		$evaluables=Evaluable::obtener_para_grupo_y_trimestre($this->grupo, $this->trimestre);
		$suma=0;
		foreach($evaluables as $clave => &$valor) 
		{
			$VER_EVALUABLES.=$this->componer_evaluable($valor, $suma);
			$parcial=round( ($suma * $valor->acc_porcentaje()) / 100, 2);
			$media_final+=$parcial;
		}

		//Composición de comportamiento...
		$comportamiento=Comportamiento_alumno::obtener_para_alumno_y_trimestre($this->alumno, $this->trimestre);

		if(!$comportamiento) $comportamiento=Comportamiento_alumno::generar_para_alumno_y_trimestre($this->alumno, $this->trimestre);

		$porcentaje_comportamiento=$this->grupo->acc_porcentaje_comportamiento();
		$max_comportamiento=$this->grupo->acc_max_comportamiento();
		$valor_comportamiento=$comportamiento->acc_valor();

		$calculo_comportamiento=$valor_comportamiento / $max_comportamiento;

//		$parcial_comportamiento=round( ($valor_comportamiento * $porcentaje_comportamiento) / 100, 2);
//		$parcial_comportamiento=round( ($calculo_comportamiento * $porcentaje_comportamiento) / 10, 2);

		$puntos_por_max_comportamiento=$porcentaje_comportamiento / 10;
		$parcial_comportamiento=($calculo_comportamiento * $puntos_por_max_comportamiento)  / 1;
		
		$media_final+=$parcial_comportamiento;

		return <<<R
Informe de {$this->alumno->acc_nombre()} {$this->alumno->acc_apellidos()}

--------------------------------------------------------------------------------
{$VER_EVALUABLES}
Comportamiento: {$valor_comportamiento} / {$this->grupo->acc_max_comportamiento()} [{$parcial_comportamiento}] correspondiente a un {$porcentaje_comportamiento}% de la nota final:

Media actual: {$media_final}

Observaciones: 
R;
	}

	private function componer_evaluable(Evaluable &$e, &$suma)
	{
		$VER_ITEMS=null;
		$items_evaluables=Item_evaluable::obtener_para_evaluable($e);
		$total=0;
		$cuenta=0;

		foreach($items_evaluables as $clave => &$valor)
		{
			$dato=Dato_evaluacion_alumno::obtener($this->usuario, $this->alumno, $valor);
			$nota=null;
			if(!$dato)
			{
				$nota='N/P';
			}
			else
			{
				++$cuenta;
				$nota=$dato->acc_valor();
				$total+=$nota;
			}
			
			$VER_ITEMS.=<<<R

	{$valor->acc_titulo()} - {$nota} / {$valor->acc_maximo_valor()}
R;
		}

		$media_alumno=!$cuenta ? 0 : round($total / $cuenta, 2);
		$suma=$media_alumno;

		return <<<R

{$e->acc_titulo()} : {$media_alumno} correspondiente a {$e->acc_porcentaje()}% de la nota final.
{$VER_ITEMS}

R;
	}
}
?>
