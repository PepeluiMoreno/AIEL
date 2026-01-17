<?php
/***************************** Inicio vCuerpoAltaSocioPorGestorTes ********************
FICHERO: vCuerpoAltaSocioPorGestorTes.php
VERSION: PHP 7.3.21

DESCRIPCION: Contiene menú idz de "Secciones" y el formulario para el registro de un 
nuevo socio por un gestor con rol Tesorería

Incluye subir archivo con firma del socio, y relacionado con esto se establece aquí 
lo valores fijos para variables relacionadas con ese archivo:
[MaxArchivoSize], [directorioSubir]="/../upload/FIRMAS_ALTAS_SOCIOS_GESTOR" ,
[maxLongNomArchivoSinExtDestino]"="250", [permisosArchivo]"="0444" />solo lectura 
(se podrían haber recibido desde el cTesorero.php:altaSocioPorGestorTes())		
						
LLAMADA: vAltaSocioPorGestorTesInc.php a su vez por cTesorero.php:altaSocioPorGestorTes()
LLAMA: vistas/tesorero/formAltaSocioPorGestorTes.php

OBSERVACIONES: Igual a CuerpoAltaSocioPorGestorPres.php, y cuerpoAltaSocioPorGestorCoord.php,              
excepto en título. 
"vCuerpoAltaSocioPorGestor.php" es una alternativa que unifica los tres pero es menos flexible
***************************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';

/********** Inicio Cuerpo central derecho ************/

echo $datosNavegacion['navegacion']['cadNavegEnlaces'];
?>
<br /><br />

<h3 align="center">	
    ALTA NUEVO/A &nbsp;&nbsp;SOCIO/A POR TESORERÍA 	
</h3>	


<?php require_once './vistas/tesorero/formAltaSocioPorGestorTes.php'; ?>


</div><!-- ***************** Fin Cuerpo central derecho ************** -->

</div><!-- *********** Fin cuerpo central:cuerpo izdo+cuerpo decho ******** -->

<!-- ****************************** Fin vCuerpoAltaSocioPorGestorTes  ******************** -->