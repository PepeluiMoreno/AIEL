<?php
/*--------------------------------------------------------------------------------------------------
FICHERO: vMostrarSociosPresInc.php
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

RECIBE: el array $resDatosSocios, con los datos de los socios, $datosFormMiembro, para el APE1 y APE2 
si se busca un socio concreto por apellidos

LLAMADA: cPresidente.php: mostrarSociosPres(), 
y a su vez desde el menú izdo del rol presidencia "Lista soci@s"

LLAMA: vistas/presidente/vCuerpoMostrarSociosPres.php
e incluye plantillasGrales

OBSERVACIONES: El parámetro "$enlacesFuncionRolSeccId" recibe los links del menú de cada usuario.
----------------------------------------------------------------------------------------------------*/
function vMostrarSociosPresInc($tituloSeccion,$enlacesFuncionRolSeccId,$resDatosSocios,$parValorComboAgrupaSocio,$datosFormMiembro)
{
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';

  require_once './vistas/presidente/vCuerpoMostrarSociosPres.php';
  
  require_once './vistas/plantillasGrales/vPieFinal.php';

}
?>