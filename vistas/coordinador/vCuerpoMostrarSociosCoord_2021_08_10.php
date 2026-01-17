<?php
/****************************** Inicio vCuerpoMostrarSociosCoord *************
FICHERO: vCuerpoMostrarSociosCoord.php
PROYECTO: EL
VERSION: PHP 7.3.21
DESCRIPCION: Contiene menú idz de "Secciones" y el formulario de 
             la tabla de mostrar socios al coordinador 
OBSERVACIONES:Se le llama desde vMostrarSociosCoordInc.php
MEJORAR comentarios
******************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';

/************************ Inicio Cuerpo central ************************/

echo $resDatosSocios['navegacion']['cadNavegEnlaces'];
?>
<br /><br />
<h3 align="center">
    LISTA DE SOCIOS/AS (no incluye las bajas)	  	
</h3>

<!-- *** Inicio formActualizarSocioCoord (se podría pasar com parámetro)******* -->
<?php
require_once './vistas/coordinador/formMostrarSociosCoord.php';
/* require_once './vistas/coordinador/formMostrarSociosCoordNew2.php'; */
?>
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
    <br /><br />			
</div 
<!-- ********************  Fin Form botón anterior *********************** --> 
</div>
<!-- ****************************** Fin Cuerpo central  ******************** -->
</div>
<!-- ******************************* Fin vCuerpoMostrarSociosCoord ************* -->