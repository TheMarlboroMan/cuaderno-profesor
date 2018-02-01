<?

require_once(Constantes::RUTA_SERVER.'class/interfaces/propiedad_usuario.interface.php');
require_once(Constantes::RUTA_SERVER.'class/interfaces/horario.interface.php');

require_once(Constantes::RUTA_SERVER.'class/interfaz_usuario.class.php');
require_once(Constantes::RUTA_SERVER.'class/maquina_web.class.php');
require_once(Constantes::RUTA_SERVER.'class/factoria_plugins.class.php');
require_once(Constantes::RUTA_SERVER.'class/factoria_urls.class.php');
require_once(Constantes::RUTA_SERVER.'class/cache/cache.class.php');

require_once(Constantes::RUTA_SERVER.'class/maquinas_vista/login.mv.class.php');
require_once(Constantes::RUTA_SERVER.'class/maquinas_vista/curso.mv.class.php');
require_once(Constantes::RUTA_SERVER.'class/maquinas_vista/grupo.mv.class.php');
require_once(Constantes::RUTA_SERVER.'class/maquinas_vista/tabla.mv.class.php');
require_once(Constantes::RUTA_SERVER.'class/maquinas_vista/final.mv.class.php');
require_once(Constantes::RUTA_SERVER.'class/maquinas_vista/css.mv.class.php');
require_once(Constantes::RUTA_SERVER.'class/maquinas_vista/js.mv.class.php');
require_once(Constantes::RUTA_SERVER.'class/maquinas_vista/horario.mv.class.php');

require_once(Constantes::RUTA_SERVER.'class/maquinas_logica/herramientas_logica.class.php');
require_once(Constantes::RUTA_SERVER.'class/maquinas_logica/login.ml.class.php');
require_once(Constantes::RUTA_SERVER.'class/maquinas_logica/logout.ml.class.php');
require_once(Constantes::RUTA_SERVER.'class/maquinas_logica/curso.ml.class.php');
require_once(Constantes::RUTA_SERVER.'class/maquinas_logica/grupo.ml.class.php');
require_once(Constantes::RUTA_SERVER.'class/maquinas_logica/tabla.ml.class.php');
require_once(Constantes::RUTA_SERVER.'class/maquinas_logica/horario.ml.class.php');
require_once(Constantes::RUTA_SERVER.'class/maquinas_logica/final.ml.class.php');

require_once(Constantes::RUTA_SERVER.'class/informes/informe_alumno.class.php');

require_once(Constantes::RUTA_SERVER.'class/usuario/usuario.class.php');
require_once(Constantes::RUTA_SERVER.'class/usuario/usuario.sql.class.php');

require_once(Constantes::RUTA_SERVER.'class/curso/curso.class.php');
require_once(Constantes::RUTA_SERVER.'class/curso/curso.sql.class.php');
require_once(Constantes::RUTA_SERVER.'class/curso/curso.vista.class.php');

require_once(Constantes::RUTA_SERVER.'class/grupo/grupo.class.php');
require_once(Constantes::RUTA_SERVER.'class/grupo/grupo.sql.class.php');
require_once(Constantes::RUTA_SERVER.'class/grupo/grupo.vista.class.php');

require_once(Constantes::RUTA_SERVER.'class/alumno/alumno.class.php');
require_once(Constantes::RUTA_SERVER.'class/alumno/alumno.sql.class.php');
require_once(Constantes::RUTA_SERVER.'class/alumno/alumno.vista.class.php');

require_once(Constantes::RUTA_SERVER.'class/comportamiento_alumno/comportamiento_alumno.class.php');
require_once(Constantes::RUTA_SERVER.'class/comportamiento_alumno/comportamiento_alumno.sql.class.php');
//require_once(Constantes::RUTA_SERVER.'class/alumno/alumno.vista.class.php');

require_once(Constantes::RUTA_SERVER.'class/comportamiento_alumno_entrada/comportamiento_alumno_entrada.class.php');
require_once(Constantes::RUTA_SERVER.'class/comportamiento_alumno_entrada/comportamiento_alumno_entrada.sql.class.php');

require_once(Constantes::RUTA_SERVER.'class/evaluable/evaluable.class.php');
require_once(Constantes::RUTA_SERVER.'class/evaluable/evaluable.sql.class.php');
require_once(Constantes::RUTA_SERVER.'class/evaluable/evaluable.vista.class.php');

require_once(Constantes::RUTA_SERVER.'class/item_evaluable/item_evaluable.class.php');
require_once(Constantes::RUTA_SERVER.'class/item_evaluable/item_evaluable.sql.class.php');
require_once(Constantes::RUTA_SERVER.'class/item_evaluable/item_evaluable.vista.class.php');

require_once(Constantes::RUTA_SERVER.'class/dato_evaluacion_alumno/dato_evaluacion_alumno.class.php');
require_once(Constantes::RUTA_SERVER.'class/dato_evaluacion_alumno/dato_evaluacion_alumno.sql.class.php');

require_once(Constantes::RUTA_SERVER.'class/nota_final_evaluacion_alumno/nota_final_evaluacion_alumno.class.php');
require_once(Constantes::RUTA_SERVER.'class/nota_final_evaluacion_alumno/nota_final_evaluacion_alumno.sql.class.php');

require_once(Constantes::RUTA_SERVER.'class/horario_franja/horario_franja.class.php');
require_once(Constantes::RUTA_SERVER.'class/horario_franja/horario_franja.sql.class.php');

require_once(Constantes::RUTA_SERVER.'class/horario_contenido/horario_contenido.class.php');
require_once(Constantes::RUTA_SERVER.'class/horario_contenido/horario_contenido.sql.class.php');
?>
