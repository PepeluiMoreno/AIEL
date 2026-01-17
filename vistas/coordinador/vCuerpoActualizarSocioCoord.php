<?php
/*********************** Inicio vCuerpovActualizarSocioCoord *********************************
FICHERO: vCuerpoActualizarSocioCoord.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: En este formulario el coordinador, actualiza  datos personales de un socio, cuotas, IBAN, 
agrupación, afecta a varias varias tablas. Contiene menú idz de "Secciones" 
													
LLAMADA: vistas/coordinador/vActualizarSocioCoordInc.php y a su vez desde cCoordinador.php:actualizarSocioCoord()
en lista de socios desde el icono icono Modifica = Pluma

LLAMA: vistas/coordinador/formActualizarSocioCoord.php

OBSERVACIONES:
*********************************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';

/*************************** Inicio Cuerpo central ************************/

if (isset($navegacion['cadNavegEnlaces'])) {
    echo $navegacion['cadNavegEnlaces'];
}
?>	
<br /><br />	
<h3 align="center">
    ACTUALIZAR DATOS SOCIA/O	  	
</h3>
<br />		 
<!--*** Inicio formActualizarSocioCoord (se podría pasar com parámetro)*******-->
<?php require_once './vistas/coordinador/formActualizarSocioCoord.php'; ?>
<!--********************  Fin formActualizarSocioCoord ***********************-->
</div>
<!--****************************** Fin Cuerpo central  ********************-->
</div>
<!--******************************* Fin vCuerpovActualizarSocioCoord **************-->