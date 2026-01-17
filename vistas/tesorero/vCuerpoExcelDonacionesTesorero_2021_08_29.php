<?php
/********************************* Inicio vCuerpoExcelDonacionesTesorero ***************
FICHERO: vCuerpoExcelDonacionesTesorero.php
VERSION: PHP 7.3.21	

DESCRIPCION: Contiene menú idz de "Secciones" y el formulario muesta el formulario 
que permite elegir el año de las donaciones y los tipos de donates: Todos,Socios,
Simpatizantes (no socios),Anónimos para a exportar a Excel las donaciones con 
información individual de cada donacion.

LLAMADA: vistas/tesorero/vExcelDonacionesTesoreroInc.php
y su vez por cTesorero.php:excelDonacionesTesorero()

LLAMA: vistas/tesorero/formExcelDonacionesTesorero.php
y contiene plantillasGrales/vCabeceraSalir.php

OBSERVACIONES:
***************************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';

/*************************** Inicio Cuerpo central ***************************/

echo $navegacion['cadNavegEnlaces'];

?>
<br /><br />

<h3 align="center">
    EXPORTAR LAS DONACIONES A EXCEL  	
</h3>

<?php require_once './vistas/tesorero/formExcelDonacionesTesorero.php'; ?>

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
    <br /><br />			
    <?php
    if (isset($navegacion['anterior'])) 
				{
        echo $navegacion['anterior'];
    }
    ?>				
    <br /><br />			
</div 

<!-- ********************  Fin Form botón anterior *********************** --> 
</div>
<!-- ****************************** Fin Cuerpo central  ******************** -->
</div>
<!-- ******************************* Fin vCuerpoExcelDonacionesTesorero **************** -->