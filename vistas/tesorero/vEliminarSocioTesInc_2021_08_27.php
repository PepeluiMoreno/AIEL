<?php
/*---------------------------------------------------------------------------------------------------
FICHERO: vEliminarSocioTesInc.php

VERSION: PHP 7.3.21

DESCRIPCION: 
Es el formulario para eliminar los datos de un socio, por un gestor con rol Tesorería. 
Previamente muestra algunos campos de datos y se puede introducir un comentario

Incluye dos botones: "Baja socio/a"  y "Baja socio/a por fallecimiento y guardar su nombre"  
Además de botón "No dar baja socio/a".

Se muestran algunos datos del socio y en caso de que hubiese un archivo con la firma de un socio 
debido a alta por un gestor, también mostraría aquí para eliminaría el archivo del servidor. 										
													
LLAMADA: cTesorero.php.eliminarSocioTes(), que se llega desde tabla de 
"ESTADO CUOTAS SOCIOS/AS",  al hacer clic en el icono Baja (Papelera)

LLAMA: vistas/tesorero/vCuerpoEliminarSocioPres.php 
y además incluye las plantillasGrales
												
OBSERVACIONES:
---------------------------------------------------------------------------------------------------*/
function vEliminarSocioTesInc($tituloSeccion,$datSocio,$navegacion)
{ 
  require_once './vistas/plantillasGrales/vCabeceraSalir.php';

	 //$datSocio = $datosSocioEliminar['valoresCampos'];		
		
  require_once './vistas/tesorero/vCuerpoEliminarSocioTes.php';

  require_once './vistas/plantillasGrales/vPieFinal.php';
}
?>