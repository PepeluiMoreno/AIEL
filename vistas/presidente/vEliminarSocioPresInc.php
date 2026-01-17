<?php
/*---------------------------------------------------------------------------------------------------
FICHERO: vEliminarSocioPresInc.php
VERSION: PHP 7.3.21

DESCRIPCION: Es el formulario para eliminar los datos de un socio, por un gestor con rol Presidente. 
             Previamente muestra algunos campos de datos y se puede introducir un comentario
													Incluye dos botones: "Baja socio/a"  y "Baja socio/a por fallecimiento y guardar su nombre"  
													Además de botón "No dar baja socio/a".
													
LLAMADA: cPresidente.php.eliminarSocioPres(), que se llega desde tabla de 
"LISTA DE SOCIOS/AS", en "formMostrarDatosSocioPres.php" al hacer clic en el icono Baja (Papelera)

LLAMA: vistas/presidente/vCuerpoEliminarSocioPres.php 
y además incluye las plantillas
												
OBSERVACIONES:
---------------------------------------------------------------------------------------------------*/
function vEliminarSocioPresInc($tituloSeccion,$datSocio,$navegacion)
{ 
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';

	 //$datSocio = $datosSocioEliminar['valoresCampos'];		
		
  require_once './vistas/presidente/vCuerpoEliminarSocioPres.php';

  require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>