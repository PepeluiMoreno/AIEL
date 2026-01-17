<?php
/****************************** Inicio vCuerpoAnotarIngresoDonacionMenu.php ****************
FICHERO: vCuerpoAnotarIngresoDonacionMenu.php
VERSION: PHP 7.3.21

DESCRIPCION: Contiene menú idz de "Secciones" y el Formulario que sirve como 
menú para elegir entre los  casos que se pueden dar al anotar una donación:

- Donante nuevo e identificado (no socio), pero no registrado previamente como donante 
- Anónimo. 

-	Buscar por "Nº Documento NIF, NIE, pasaporte, otros" o por "email" por ser socio, 
  o donante no socio ya anotado (buscará en las tablas "MIEMBROS" o "DONACION")				
									

LLAMADA: vistas/tesorero/vAnotarIngresoDonacionMenuInc.php 
y previamente cTesorero.php:anotarIngresoDonacionMenu()

LLAMA: vistas/tesorero/formAnotarIngresoDonacionMenu.php
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
    MENÚ ANOTAR DONACIONES 	
</h3>	

<?php require_once './vistas/tesorero/formAnotarIngresoDonacionMenu.php'; ?>


<!-- ********************  Inicio form botón anterior******************** --> 		

<div align="center">
				<br />	
				<?php
					if (isset($navegacion['anterior'])) 
					{
									echo $navegacion['anterior'];
					}
				?>	
				<br />
</div>		
<br /><br />		
<!-- ********************  Fin Form botón anterior *********************** --> 

</div>
<!-- ****************************** Fin Cuerpo central  ******************** -->
</div>
<!-- ********************** Fin Cuerpo vCuerpoAnotarIngresoDonacionMenu.php ********* -->