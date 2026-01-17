<?php
/* ***************************** Inicio vCuerpoMostrarSociosPres.php *********************************
FICHERO: vCuerpoMostrarSociosPres.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: 
En el formulario se forma y muestra una tabla-lista paginada "LISTA DE SOCI@S" de los socios de con 
algunos campos de información de los socios de todas las agrupaciones de EL, pero sólo de los que e
stán dados de alta 
Incluye un campo para elegir una agrupación concreta. También permite buscar por apellidos de socios.

En la parte inferior se muestran número de páginas para poder ir directamente a un página, anterior,
siguiente, primera, última.
Además al final para cada fila, hay iconos links para: ver detalles de ese socio, modificar datos,
borrar datos socio 	

LLAMADA: vistas/presidente/vCuerpoMostrarSociosPres.php 
y a su vez desde cPresidente.php: mostrarSociosPres() y el menú izdo del rol presidencia "Lista soci@s"

LLAMA: vistas/presidente/formMostrarSociosPres.php
e incluye plantillasGrales

OBSERVACIONES: El parámetro "$enlacesFuncionRolSeccId" recibe los links del menú de cada usuario.
*****************************************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';

/************************ Inicio Cuerpo central ************************/

echo $resDatosSocios['navegacion']['cadNavegEnlaces'];
?>
<br /><br />
<h3 align="center">
    LISTA DE SOCIOS/AS (no incluye las bajas)
</h3>

<?php require_once './vistas/presidente/formMostrarSociosPres.php'; ?>
	
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
</div>

<!-- ********************  Fin Form botón anterior *********************** --> 

<!-- ********************  Fin formActualizarSocioPres *********************** -->
</div>
<!-- ****************************** Fin Cuerpo central  ******************** -->
</div>
<!-- ******************************* Fin vCuerpoMostrarSociosPres ************* -->