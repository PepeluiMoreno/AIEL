<?php
/****************************** Inicio Cuerpo actualizarSocio ******************
FICHERO: vCuerpovActualizarSocio.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: Contiene menú idz de "Secciones" y el formulario de 
             
Se actualizan los datos de un socio por el propio socio en varias tablas  
(incluye a los propios gestores como socios). 

La parte de navegación se añade, para que cuando un socio gestor CODROL >2 
(presidente, coordinador, secretaria, tesoreria, etc....) accede a sus propios datos
mantenga la navegación, si no es gestor no se muestra ninguna barra.

LLAMADA: vistas/socios/vActualizarSocioInc.php y a su vez de controladorSocios.php:actualizarSocio()
LLAMA: vistas/socios/formActualizarSocio.php
													
OBSERVACIONES: Probado PHP 7.3.21
********************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';

/************************ Inicio Cuerpo central *****************************/

if (isset($navegacion['cadNavegEnlaces'])) 
{
    echo $navegacion['cadNavegEnlaces'];
}
?>	
<br />
<h3 align="center">
    ACTUALIZAR DATOS DEL SOCIO / A	  	
</h3> 
<br />		

<!-- *** Inicio formActualizarSocio (se podría pasar com parámetro)******* -->
<?php require_once './vistas/socios/formActualizarSocio.php'; ?>
<!-- ********************  Fin formActualizarSocio *********************** -->

</div>
<!-- ****************************** Fin Cuerpo central  ******************** -->
</div>
<!-- ******************************* Fin Cuerpo actualizarSocio*************** -->