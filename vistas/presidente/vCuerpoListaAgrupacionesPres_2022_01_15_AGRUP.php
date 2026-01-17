<?php
/*********************** Inicio vCuerpoListaAgrupacionesPres.php ****************************************
FICHERO: vCuerpoListaAgrupacionesPres.php
PROYECTO: EL
VERSION: PHP 7.3.21

Con datos de tabla " AGRUPACIONTERRITORIAL" se forma una tabla-lista páginada "LISTADO DE AGRUPACIONES", 
y se  muestran algunos datos de cada agrupación territorial. Al final de cada fila dos enlaces: 
icono lupa (ver toda información de esa agrupación), icono pluma (modificar algunos datos de esa agrupación)

RECIBE: un array "$arrDatosAgrupaciones" con los datos de las agrupaciones, y $navegacion

LLAMADA: vistas/presidente/vListaAgrupacionesPresInc.phpy a su vez desde cPresidente.php:listaAgrupacionesPres()
LLAMA: vistas/presidente/formListaAgrupacionesPres.php e incluye plantillasGrales

OBSERVACIONES: 
********************************************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';
		
/************************** Inicio Cuerpo central **************************/

if (isset($navegacion['cadNavegEnlaces'])) 
{
    echo $navegacion['cadNavegEnlaces'];
}

?>

<br /><br />
<h3 align="center">
    LISTADO DE AGRUPACIONES 
</h3>		 

<?php require_once './vistas/presidente/formListaAgrupacionesPres.php'; ?>
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
<!-- *********************** Fin vCuerpoListaAgrupacionesPres.php *********************************** -->