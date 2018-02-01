<?
class Factoria_urls
{
	///// Login //////

	public static function vista_logout()
	{
		return Constantes::URL_WEB.'logout.html?';
	}

	public static function vista_login()
	{
		return Constantes::URL_WEB.'login.html?';
	}

	public static function resultado_acceso($resultado)
	{
		if(!$resultado) return self::vista_login();
		else return self::vista_cursos();
	}

	///// Cursos //////

	public static function resultado_crear_curso($resultado)
	{	
		return Constantes::URL_WEB.'portal.html?'.Maquina_web::CLAVE_MENSAJES.'[res_crear]='.$resultado;
	}
	
	public static function resultado_eliminar_curso($resultado)
	{
		return Constantes::URL_WEB.'portal.html?'.Maquina_web::CLAVE_MENSAJES.'[res_eliminar]='.$resultado;
	}

/*
	public static function accion_eliminar_curso(Curso &$c)
	{
		return Constantes::URL_WEB.'portal.html?'.Maquina_web::CLAVE_ESTADO_LOGICA.'=eliminar&idc='.$c->ID_INSTANCIA();
	}
*/
	public static function vista_cursos()
	{
		return Constantes::URL_WEB.'portal.html?';
	}

	public static function vista_curso_acceso_no_permitido_a_grupo()
	{
		return Constantes::URL_WEB.'portal.html?'.Maquina_web::CLAVE_MENSAJES.'[grupo_no_permitido]=1';
	}

	///// Grupos //////

	public static function resultado_crear_grupo($resultado)
	{
		return Constantes::URL_WEB.'portal.html?'.Maquina_web::CLAVE_MENSAJES.'[res_crear_grupo]='.$resultado;
	}
	
	public static function resultado_eliminar_grupo($resultado)
	{
		return Constantes::URL_WEB.'portal.html?'.Maquina_web::CLAVE_MENSAJES.'[res_eliminar_grupo]='.$resultado;
	}
/*
	public static function accion_eliminar_grupo(Grupo &$g)
	{
		return Constantes::URL_WEB.'portal.html?'.Maquina_web::CLAVE_ESTADO_LOGICA.'=eliminar_grupo&idg='.$g->ID_INSTANCIA();
	}
*/
	public static function vista_grupo(Grupo &$g)
	{
		return Constantes::URL_WEB.'grupos/'.IU_herramientas::texto_url($g->acc_titulo()).'/'.$g->ID_INSTANCIA().'/gestionar.html';
	}

	///// Horario //////

	public static function vista_horario(Curso &$c)
	{
		return Constantes::URL_WEB.IU_herramientas::texto_url($c->acc_titulo()).'/'.$c->ID_INSTANCIA().'/horario.html';
	}

	public static function vista_tabla_horario_no_permitido(Grupo &$g)
	{
		return self::vista_cursos().'?'.Maquina_web::CLAVE_MENSAJES.'[acceso_no_permitido_horario]=0';
	}

	///// Alumnos /////

	public static function accion_eliminar_alumno(Alumno $a)
	{
		$dg=new Grupo();
		$g=&Cache::obtener_de_cache($dg, $a->acc_id_grupo());
		return Constantes::URL_WEB.'grupos/'.IU_herramientas::texto_url($g->acc_titulo()).'/'.$g->ID_INSTANCIA().'/gestionar.html?'.Maquina_web::CLAVE_ESTADO_LOGICA.'=eliminar_alumno&ida='.$a->ID_INSTANCIA();
	}

	public static function resultado_crear_alumno(Grupo &$g, $resultado)
	{
		$base=self::vista_grupo($g);
		return $base.'?'.Maquina_web::CLAVE_MENSAJES.'[res_crear_alumno]='.$resultado;
	}
	/*
	public static function resultado_eliminar_alumno(Grupo &$g, $resultado)
	{
		$base=self::vista_grupo($g);
		return $base.'?'.Maquina_web::CLAVE_MENSAJES.'[res_eliminar_alumno]='.$resultado;
	}

	///// Evaluables /////
/*
	public static function accion_eliminar_evaluable(Evaluable $e)
	{
		$dg=new Grupo();
		$g=&Cache::obtener_de_cache($dg, $e->acc_id_grupo());
		$base=self::vista_grupo($g);
		return $base.'?'.Maquina_web::CLAVE_ESTADO_LOGICA.'=eliminar_evaluable&ide='.$e->ID_INSTANCIA();
	}
*/
	public static function resultado_crear_evaluable(Grupo &$g, $resultado)
	{
		$base=self::vista_grupo($g);
		return $base.'?'.Maquina_web::CLAVE_MENSAJES.'[res_crear_evaluable]='.$resultado;
	}
	
	public static function resultado_eliminar_evaluable(Grupo &$g, $resultado)
	{
		$base=self::vista_grupo($g);
		return $base.'?'.Maquina_web::CLAVE_MENSAJES.'[res_eliminar_evaluable]='.$resultado;
	}

	///// Items evaluables /////
/*
	public static function accion_eliminar_item_evaluable(Item_evaluable $i)
	{
		$de=new Evaluable();
		$dg=new Grupo();

		$ev=&Cache::obtener_de_cache($de, $i->acc_id_evaluable());
		$g=&Cache::obtener_de_cache($dg, $ev->acc_id_grupo());
		$base=self::vista_grupo($g);
		return $base.'?'.Maquina_web::CLAVE_ESTADO_LOGICA.'=eliminar_item_evaluable&idi='.$i->ID_INSTANCIA();
	}
*/
	public static function resultado_crear_item_evaluable(Grupo &$g, $resultado)
	{
		$base=self::vista_grupo($g);
		return $base.'?'.Maquina_web::CLAVE_MENSAJES.'[res_crear_item_evaluable]='.$resultado;
	}
	
	public static function resultado_eliminar_item_evaluable(Grupo &$g, $resultado)
	{
		$base=self::vista_grupo($g);
		return $base.'?'.Maquina_web::CLAVE_MENSAJES.'[res_eliminar_item_evaluable]='.$resultado;
	}

	///// Vista tabla //////

	public static function vista_tabla_por_trimestre_y_evaluable(Grupo &$g, Evaluable &$e, $trimestre)
	{
		return Constantes::URL_WEB.'grupos/'.IU_herramientas::texto_url($g->acc_titulo()).'/'.$g->ID_INSTANCIA().'/'.$trimestre.'/'.$e->ID_INSTANCIA().'/tabla_parcial.html';
	}

	public static function vista_tabla_por_trimestre_evaluable_e_item(Grupo &$g, Evaluable &$e, Item_evaluable &$i,  $trimestre)
	{
		return Constantes::URL_WEB.'grupos/'.IU_herramientas::texto_url($g->acc_titulo()).'/'.$g->ID_INSTANCIA().'/'.$trimestre.'/'.$e->ID_INSTANCIA().'/'.$i->ID_INSTANCIA().'/tabla_evaluable.html';
	}

	public static function vista_tabla_por_trimestre(Grupo &$g, $trimestre)
	{
		return Constantes::URL_WEB.'grupos/'.IU_herramientas::texto_url($g->acc_titulo()).'/'.$g->ID_INSTANCIA().'/'.$trimestre.'/tabla.html';
	}

	public static function vista_tabla_acceso_no_permitido(Grupo &$g)
	{
		return self::vista_grupo($g).'?'.Maquina_web::CLAVE_MENSAJES.'[acceso_no_permitido_tabla]=0';
	}

	public static function vista_final(Grupo &$g)
	{
		return Constantes::URL_WEB.'grupos/'.IU_herramientas::texto_url($g->acc_titulo()).'/'.$g->ID_INSTANCIA().'/final/tabla.html';
	}
}
