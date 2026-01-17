<?php
/****************************** Inicio vCuerpoAnotarIngresoDonacionMenu.php **************
FICHERO: vCuerpoAnotarIngresoDonacionMenu.php
VERSION: PHP 7.3.21

DESCRIPCION: Contiene menú idz de "Secciones" y el Formulario que sirve como 
menú para elegir entre los  casos que se pueden dar al anotar una donación:
- Donante nuevo e identificado (no socio), pero no registrado previamente como 
  donante (el formulario llevará a cTesorero:anotarIngresoDonacion)
- Anónimo. (el formulario llevará a cTesorero:anotarIngresoDonacion)
-	Buscar por Nº Ducumento NIF, NIE, pasaporte, otros, por ser socio o donante no socio 
         ya anotado (buscará en las tablas "MIEMBROS" o "DONACION")									
-	Buscar por email por ser socio o donante no socio ya anotado (buscará en las tablas
         "MIEMBROS" o "DONACION")									
									
En el caso de "buscar" llevará de nuevo a cTesorero:anotarIngresoDonacionMenu(), 
y llama a la función buscarDonante(),  

LLAMADA: vistas/tesorero/vAnotarIngresoDonacionMenuInc.php

LLAMADA: vistas/tesorero/formAnotarIngresoDonacionMenu.php
incluye plantillasGrales
       
OBSERVACIONES: 												
**********************************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';

/*************************** Inicio Cuerpo central ***************************/

if (isset($navegacion['cadNavegEnlaces'])) 
{
    echo $navegacion['cadNavegEnlaces'];
}
?>	
<br /><br />

<h3 align="center">	
    ANOTAR DONACIONES 	
</h3>	

<?php require_once './vistas/tesorero/formAnotarIngresoDonacionMenu.php'; ?>

</div>
<!-- ****************************** Fin Cuerpo central  ******************** -->
</div>
<!-- ********************** Fin Cuerpo vCuerpoAnotarIngresoDonacionMenu.php ********* -->