<?php

/***************************** Inicio vCuerpoExportarExcelInformeAnualPres.php *********************
FICHERO: vCuerpoExportarExcelInformeAnualPres.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: Formulario para exportar datos soci@s a Excel para informe anual de Secretaría, 
incluye las plantillas y formulario para la exportación de socios por presidencia

En el archivo Excel se incluye todos los socios/as que en el año correspondiente estuvieron de alta,
aunque después en ese mismo año se diesen de baja.

Este formulario permite elegir agrupación (aunque lo normal es que se incluyan todas) y el año desde 2009 
(lo normal es incluir el año que finalizó)

LLAMADA:  vistas/presidente/vExportarExcelInformeAnualPresInc.php
y previamente desde cPresidente.php:cExportarExcelInformeAnualPres()

LLAMA: vistas/presidente/formExportarExcelInformeAnualPres.php
e incluye plantillasGrales

OBSERVACIONES: 
**************************************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';

/************************* Inicio Cuerpo central *************************/

echo $navegacion['cadNavegEnlaces'];

?>
<br /><br />

<h3 align="center">

    EXPORTAR LISTA DE SOCIOS/AS	A EXCEL PARA EL INFORME ANUAL DE PRESIDENCIA  	
</h3>

<!-- *** Inicio formActualizarSocioPres (se podría pasar com parámetro)******* -->
<?php require_once './vistas/presidente/formExportarExcelInformeAnualPres.php'; ?>
<!-- ********************  Inicio form botón anterior******************** --> 		

<div align="center">
    <?php
    /* if (isset($botonSubmit['enlaceBoton'])&&($botonSubmit['enlaceBoton']!=='')&&
      ($botonSubmit['enlaceBoton']!==NULL))
      { echo	"<form method='post' action=./".$botonSubmit['enlaceBoton'].">".
      " <input type='submit' value=".$botonSubmit['textoBoton'].">";
      echo " </form>";
      }
     */
    ?>		
    <?php
    if (isset($navegacion['anterior'])) 
				{
        echo $navegacion['anterior'];
    }
    ?>				

</div 
<!-- ********************  Fin Form botón anterior *********************** --> 
</div>
<!-- ****************************** Fin Cuerpo central  ******************** -->
</div>
<!-- **************************** Fin vCuerpoExportarExcelInformeAnualPres.php *************** -->