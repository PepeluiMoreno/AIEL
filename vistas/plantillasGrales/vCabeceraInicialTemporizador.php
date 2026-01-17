<!-- ***************************************************************************
FICHERO: vCabeceraInicialTemporizador.php
PROYECTO: EuropaLaica
VERSION: PHP 5.2.3
DESCRIPCION: Escribe la cabecera inicial de entrada en la aplicación, con los 
             links de Entrar, Nuevo socio, Nuevo simpatizante, 
													Recordar contraseña y Salir de la aplicacacion.
OBSERVACIONES: Es incluida para formar las páginas de la aplicación.
***************************************************************************** -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<!--Copyright 2010 Agustín Villacorta,  Inc. All rights reserved.-->
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<title>EuropaLaica</title>
	<!--<link rel="stylesheet" href="./vistas/css/cssUsuarios1.css" type="text/css" />-->
<link rel="stylesheet" href="./vistas/css/cssIndex.css" type="text/css" />	
	<!-- Para IE Lo siguiente se añade a cssIndex.css -->
	<!--[if IE]>
  <link rel="stylesheet" href="./vistas/css/cssIndexIE.css" type="text/css" />
 <![endif]-->

<style>
#bowlG{
position:relative;
width:128px;
height:128px;
}

#bowl_ringG{
position:absolute;
width:128px;
height:128px;
border:11px solid #000000;
-moz-border-radius:128px;
-webkit-border-radius:128px;
-ms-border-radius:128px;
-o-border-radius:128px;
border-radius:128px;
}

.ball_holderG{
position:absolute;
width:34px;
height:128px;
left:47px;
top:0px;
-moz-animation-name:ball_moveG;
-moz-animation-duration:2.1s;
-moz-animation-iteration-count:infinite;
-moz-animation-timing-function:linear;
-webkit-animation-name:ball_moveG;
-webkit-animation-duration:2.1s;
-webkit-animation-iteration-count:infinite;
-webkit-animation-timing-function:linear;
-ms-animation-name:ball_moveG;
-ms-animation-duration:2.1s;
-ms-animation-iteration-count:infinite;
-ms-animation-timing-function:linear;
-o-animation-name:ball_moveG;
-o-animation-duration:2.1s;
-o-animation-iteration-count:infinite;
-o-animation-timing-function:linear;
animation-name:ball_moveG;
animation-duration:2.1s;
animation-iteration-count:infinite;
animation-timing-function:linear;
}

.ballG{
position:absolute;
left:0px;
top:-30px;
width:51px;
height:51px;
background:#FFFFFF;
-moz-border-radius:43px;
-webkit-border-radius:43px;
-ms-border-radius:43px;
-o-border-radius:43px;
border-radius:43px;
}

@-moz-keyframes ball_moveG{
0%{
-moz-transform:rotate(0deg)}

100%{
-moz-transform:rotate(360deg)}

}

@-webkit-keyframes ball_moveG{
0%{
-webkit-transform:rotate(0deg)}

100%{
-webkit-transform:rotate(360deg)}

}

@-ms-keyframes ball_moveG{
0%{
-ms-transform:rotate(0deg)}

100%{
-ms-transform:rotate(360deg)}

}

@-o-keyframes ball_moveG{
0%{
-o-transform:rotate(0deg)}

100%{
-o-transform:rotate(360deg)}

}

@keyframes ball_moveG{
0%{
transform:rotate(0deg)}

100%{
transform:rotate(360deg)}

}

</style>


</head>

<body>
<div class="contedorGeneral"><!-- Afecta toda la pág. se cierra en pie de pág. -->

<!-- ********************** Inicio Cabecera con logos ************************--> 
 <div id="cabecera">	
		<div id="cabIdz">
	   <img src="./vistas/images/lambdaEL_cab_Gris.gif" />
				
  </div>
	 <div id="cabDecha">
	   <img src="./vistas/images/textoVerde_Socios_gris1.gif" />
	 </div>
 </div>
	
<!-- ************************ Fin Cabecera con logos ************************ -->

<!-- ******************* Inicio enlaces registrase, contraseña, ... ************** -->
 <div id="sectionLinksBarCab" style="clear:both;">
			<a href="./index.php?controlador=controladorLogin&amp;accion=validarLogin">
		<b>Entrar </b></a>| 
  <a href="./index.php?controlador=controladorSocios&amp;accion=altaSocio">
		<b> &nbsp;Nuev@ &nbsp;&nbsp;soci@&nbsp; </b></a>|  	
<!--  Desactivarlo hasta que manolo me pase la lista de simpatizantes
	 <a href="./index.php?controlador=controladorSimpatizantes&amp;accion=altaSimpatizante">
		<b>Nuevo simpatizante</b></a>|
	-->
		<a href="./index.php?controlador=controladorLogin&amp;accion=recordarLogin">
		<b>Recordar usuari@ &nbsp;y &nbsp;contraseña&nbsp;</b>&nbsp;&nbsp;</a>|
		<a href="./index.php?controlador=controladorLogin&amp;accion=logOut">
		<b>&nbsp;Salir</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
</div>
<!-- para ejecutar temporizador
<div id="bowlG">
<div id="bowl_ringG">
<div class="ball_holderG">
<div class="ballG">
</div>
</div>
</div>
</div>
-->
<!-- ********************** Fin enlaces registrase, contraseña, ... ************** -->