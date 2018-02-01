<?
$fecha=date('Y');
return <<<R
<!DOCTYPE html>
<html xml:lang="en" lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
	{$VER_CSS}
	<title>{$TITLE}</title>
</head>
<body>
	<div id="cabecera">
		<h1>Cuaderno del profesor</h1>
		<div class="bienvenida">{$BIENVENIDA}</div>
		<ul class="herramientas">{$HERRAMIENTAS}</ul>
	</div>
	
	<div id="pagina">

		<!--Inicio cuerpo-->
		<div id="cuerpo">

{$CONTENIDO_WEB}

		</div>
		<!-- Fin cuerpo -->
	</div>

	<div id="plantillas">{$PLANTILLAS}</div>

	<div id="pie">{$fecha}.</div>

</body>

<script type="text/javascript">var URL_WEB='{$URL_WEB}';</script>
{$VER_JS}
</html>
R;
