<?php
/************ Inicio vCuerpoConfirmarEmailPassAltaSocioPorGestor.php **********
FICHERO: vCuerpoConfirmarEmailPassAltaSocioPorGestor.php 
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION:  Es el formulario para para que el usuario elija la contraseña.
En "vContent.php", contiene menú idz de "Secciones"
El formulario formConfirmarEmailPassAltaSocioPorGestor.php le pide 
elegir contraseña al socio.

LLAMADA: desde vConfirmarEmailPassAltaSocioPorGestorInc.php
LLAMA: vistas/socios/formConfirmarEmailPassAltaSocioPorGestor.php					
							
OBSERVACIONES:
******************************************************************************/

//$mensajeIzquierda = "Elegir contraseña <br /> y <br />confirmar email de socia/o";
require_once './vistas/plantillasGrales/vContent.php';
?>

<!-- Inicio contenido -->
<br />
<h3 align="center">	
    ELEGIR CONTRASEÑA	Y CONFIRMAR EMAIL 	
</h3>	

<!-- ************************* Inicio form ********************* -->
 <?php require_once './vistas/socios/formConfirmarEmailPassAltaSocioPorGestor.php'; ?>
<!-- *************************  Fin form *********************** -->	

 </div><!-- ***************** Fin Cuerpo central derecho ************** -->

</div><!-- *********** Fin cuerpo central:cuerpo izdo+cuerpo decho ******** -->

<!--********** Fin  vCuerpoConfirmarEmailPassAltaSocioPorGestor.php *********-->