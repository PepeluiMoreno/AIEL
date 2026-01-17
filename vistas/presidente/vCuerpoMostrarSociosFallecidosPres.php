<?php

/***************************** Inicio vCuerpoMostrarSociosFallecidosPres ***********************
FICHERO:vCuerpoMostrarSociosFallecidosPres.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: Muestra una tabla de los socios fallecidos dados de baja por un gestor

A partir de la tabla SOCIOSFALLECIDOS, en la que se inserta una fila cuando un gestor da de baja a 
un socio anotándolo como fallecido, se ontiene el listado que se muestra en formato tabla como
"LISTA DE SOCI@S FALLECIDOS" paginadas, con algunos campos de información de los socios fallecidos
de todas las agrupaciones de EL ordenados alfabéticamente. 

Incluye un campo para elegir una agrupación concreta. También permite buscar por apellidos de socios

Aquí se incluye la páginación de la lista de socios fallecidos. En la parte inferior se muestran
número de páginas para poder ir directamente a un página, anterior, siguiente, priemera, última.

LLAMADA:  vistas/presidente/vMostrarSociosFallecidosPresInc.php
y previamente desde cPresidente.php:mostrarSociosFallecidosPres() 

LLAMA: vistas/presidente/formMostrarSociosFallecidosPres.php
e incluye plantillasGrales

OBSERVACIONES: 
**********************************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';
		
/************************* Inicio Cuerpo central ************************/

echo $resDatosSocios['navegacion']['cadNavegEnlaces'];
?>

<br /><br />
<h3 align="center">
    
				LISTA DE SOCIOS/AS FALLECIDOS	  	
</h3>

<!-- *** Inicio formActualizarSocioPres (se podría pasar com parámetro)******* -->
<?php require_once './vistas/presidente/formMostrarSociosFallecidosPres.php'; ?>
<!-- ********************  Inicio form botón anterior******************** --> 
		
<div align="center">
    <?php
    /* if (isset($botonSubmit['enlaceBoton'])&&($botonSubmit['enlaceBoton']!=='')&&
      ($botonSubmit['enlaceBoton']!==NULL))
      {
      echo	"<form method='post' action=./".$botonSubmit['enlaceBoton'].">".
      " <input type='submit' value=".$botonSubmit['textoBoton'].">";
      echo " </form>";
      }
     */
    ?>		
    <?php
    //Es el botón de "Anterior", pero creo que no es necesario y confunde		
    //  echo $resDatosSocios['navegacion']['anterior'];
    ?>		
    <br /><br />			
</div 	

<!-- ********************  Fin Form botón anterior *********************** --> 
<!-- ********************  Fin formActualizarSocioPres *********************** -->
</div>
<!-- ****************************** Fin Cuerpo central  ******************** -->
</div>
<!-- ******************************* Fin vCuerpoMostrarSociosFallecidosPres ************* -->