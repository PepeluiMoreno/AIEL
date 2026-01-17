<?php
/****************************************** Inicio vCuerpoConfirmarSocio.php **********************
FICHERO:  vCuerpoConfirmarSocio.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: Formulario que pide confirmar o anular el alta de un 
socio (pendiente de confirmar), a petición del mismo, desde el link que recibió al registrase 
como nuevo socio. 
Desde el formulario según la elección se llamará a:
-controladorSocios:confirmarAltaSocio() 
-controladorSocios:anularAltaSocioPendienteConfirmar()
													
LLAMADA: vistas/socios/vConfirmarSocioInc.php y a su vez de controladorSocios.php:confirmarAnularAltaSocio()
LLAMA: vistas/socios/formConfirmarSocio.php

OBSERVACIONES: Probado PHP 7.3.21
**************************************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';

/************************ Inicio Cuerpo central ***********---*************/
/*
if (isset($navegacion['cadNavegEnlaces'])) 
{
    echo $navegacion['cadNavegEnlaces'];
}*/
?>	
<br />	
<h3 align="center">
    CONFIRMAR O ANULAR ALTA DEL SOCIO/A  	
</h3>		

<?php require_once './vistas/socios/formConfirmarSocio.php'; ?>

</div>
<!--****************************** Fin Cuerpo central  ********************-->
</div>
<!--******************************* Fin vCuerpoConfirmarSocio.php***************-->