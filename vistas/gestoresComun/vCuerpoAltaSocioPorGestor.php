<!--***************************** Inicio vCuerpoAltaSocioPorGestor **************
FICHERO: vCuerpoAltaSocioPorGestorCoord.php
VERSION: PHP 7.3.21

DESCRIPCION: Contiene menú idz de "Secciones" y el formulario Formulario para 
recoger los datos necesarios para dar de alta a un socio por un gestor en 
"altaSocioPorGestor()" 

LLAMADA: vAltaSocioPorGestorInc.php que asu vez es llamado desde 
controladores/libs/altaSocioPorGestor.php

LLAMA:vistas/plantillasGrales:vContent.php
vistas/gestoresComun/formAltaSocioPorGestor.php

OBSERVACIONES: 
2020-09-10: hago modificaciones para que este formulario pueda ser común para las 
altas de socios por los distintos tipos de gestores										
***************************************************************************-->
<?php
require_once './vistas/plantillasGrales/vContent.php';
?>
<!-- ******* Inicio Cuerpo central derecho ******* -->
<?php

echo $datosNavegacion['navegacion']['cadNavegEnlaces'];
?>
<br /><br />

<h3 align="center">	
    ALTA NUEVO/A &nbsp;&nbsp;SOCIO/A POR GESTOR
</h3>	

<!-- ************************* Inicio form ********************* -->
<?php require_once './vistas/gestoresComun/formAltaSocioPorGestor.php'; ?>
<!-- *************************  Fin form *********************** -->

</div><!-- ***************** Fin Cuerpo central derecho ************** -->

</div><!-- *********** Fin cuerpo central:cuerpo izdo+cuerpo decho ******** -->
<!-- ****************************** Fin vCuerpoAltaSocioPorGestor  ******************** -->