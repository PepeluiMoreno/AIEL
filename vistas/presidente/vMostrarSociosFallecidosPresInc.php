<?php
/*---------------------------------------------------------------------------------------------------------
FICHERO: vMostrarSociosFallecidosPresInc.php
VERSION: PHP 7.3.21

DESCRIPCION: Muestra una tabla de los socios fallecidos dados de baja por un gestor

A partir de la tabla SOCIOSFALLECIDOS, en la que se inserta una fila cuando un gestor da de baja a 
un socio anotándolo como fallecido, se ontiene el listado que se muestra en formato tabla como
"LISTA DE SOCI@S FALLECIDOS" paginadas, con algunos campos de información de los socios fallecidos
de todas las agrupaciones de EL ordenados alfabéticamente. 

Incluye un campo para elegir una agrupación concreta. También permite buscar por apellidos de socios

Aquí se incluye la páginación de la lista de socios fallecidos. En la parte inferior se muestran
número de páginas para poder ir directamente a un página, anterior, siguiente, priemera, última.

LLAMADA: cPresidente.php:mostrarSociosFallecidosPres() 
y previamente desde vistas/presidente/vMenuEstadisticasPres.php (página menú ESTADÍSTICAS Y 
DATOS DISPONIBLES PARA PRESIDENCIA)

LLAMA: vistas/presidente/vCuerpoMostrarSociosFallecidosPres.php
e incluye plantillasGrales

OBSERVACIONES: El parámetro "$enlacesRolSeccId" recibe los links del menú de cada usuario.
						
--------------------------------------------------------------------------------------------------------*/
function vMostrarSociosFallecidosPresInc($tituloSeccion,$enlacesFuncionRolSeccId,$resDatosSocios,$parValorComboAgrupaSocio,$datosFormMiembro)
{
		require_once './vistas/plantillasGrales/vCabeceraSalir.php';

  require_once './vistas/presidente/vCuerpoMostrarSociosFallecidosPres.php';
  
  require_once './vistas/plantillasGrales/vPieFinal.php';

}
?>