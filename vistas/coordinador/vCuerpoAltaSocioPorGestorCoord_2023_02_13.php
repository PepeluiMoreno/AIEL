<?php
/***************************** Inicio vCuerpoAltaSocioPorGestorCoord **************
FICHERO: vCuerpoAltaSocioPorGestorCoord.php
VERSION: PHP 7.3.21

DESCRIPCION: Contiene menú idz de "Secciones" y el formulario para el registro de un 
nuevo socio por un gestor con rol Tesorería
Incluye subir archivo con firma del socio, y relacionado con esto se establece aquí 
lo valores fijos para variables relacionadas con ese archivo:
[MaxArchivoSize], [directorioSubir]="/../upload/FIRMAS_ALTAS_SOCIOS_GESTOR" ,
[maxLongNomArchivoSinExtDestino]"="250", [permisosArchivo]"="0444" />solo lectura 
(se podrían haber recibido desde el cCoordinador.php:altaSocioPorGestorCoord())		
						
LLAMADA: vAltaSocioPorGestorCoordInc.php a su vez por cCoordinador.php:altaSocioPorGestorCoord()
LLAMA: vistas/coordinador/formAltaSocioPorGestorCoord.php

OBSERVACIONES: Igual a CuerpoAltaSocioPorGestorPres.php, y cuerpoAltaSocioPorGestorTes.php,              
excepto en título. "vCuerpoAltaSocioPorGestor.php" es una alternativa que unifica los tres 
pero es menos flexible
**********************************************************************************/
require_once './vistas/plantillasGrales/vContent.php';
?>
<!-- ******* Inicio Cuerpo central derecho ******* -->
<?php
//echo $resDatosSocios['navegacion']['cadNavegEnlaces'];
echo $datosNavegacion['navegacion']['cadNavegEnlaces'];
?>
<br /><br />

<h3 align="center">	
    ALTA DE SOCIO/A POR COORDINADOR/A	
</h3>	

<!-- ************************* Inicio form ********************* -->
<?php require_once './vistas/coordinador/formAltaSocioPorGestorCoord.php'; ?>
<!-- *************************  Fin form *********************** -->

</div><!-- ***************** Fin Cuerpo central derecho ************** -->

</div><!-- *********** Fin cuerpo central:cuerpo izdo+cuerpo decho ******** -->
<!-- ****************************** Fin vCuerpoAltaSocioPorGestorCoord  ******************** -->