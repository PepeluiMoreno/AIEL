<?php
/************************* vCuerpoMostrarDonaciones.php ***********************************************
FICHERO: vCuerpoMostrarDonaciones.php
VERSION: PHP 7.3.21

DESCRIPCION: Muestra una tabla "LISTADO DE LAS DONACIONES " con la lista de las donaciones ordenadas
según LAST-IN ->FIRST-OUT.
Al final de cada fila de una donación, hay iconos con links para acciones sobre
la correspondiente donación con Acciones: Ver,	Modificar, Eliminar

Se forma y muestra una tabla-lista páginada "LISTADO DE LAS DONACIONES " con la lista de las donaciones
ordenadas según LAST-IN ->FIRST-OUT.

Incluye un botón para elegir por AÑO, y otro botón para elegir por APE1, APE2.

Aquí se incluye la paginación de la lista donaciones. En la parte inferior se muestran número de páginas 
para poder ir directamente a un página, anterior, siguiente, primera, última.
La vista correspondiente en forma de tabla, además de de mostrar en cada fila datos sobre las 
donaciones:	Año,	Apellidos, Nombre, etc. al final para cada fila, hay iconos con links para acciones sobre
 la correspondiente donación con Acciones: Ver,	Modificar, Eliminar

En el formulario-tabla, en la parte superior también están los botones: "Anotar donación", "Total donaciones"
y "Exportar las donaciones a Excel" y "Mostrar y Añadir Conceptos de Donación" que dirigen a las funciones 
correspondientes dentro de cTesorero.php

LLAMADA: vistas/tesorero/vMostrarDonaciones.php 
a su vez desde cTesorero.php: mostrarDonaciones() a su vez desde el menú izdo."-	Donaciones"

LLAMA: vistas/tesorero/formMostrarDonaciones.php
y contiene plantillasGrales/vCabeceraSalir.php
		
OBSERVACIONES: 
********************************************************************************************************/

require_once './vistas/plantillasGrales/vContent.php';

/*************************** Inicio Cuerpo central ***************************/

echo $resDonaciones['navegacion']['cadNavegEnlaces'];

?>
<br /><br />

<h3 align="center">
    DONACIONES	  	
</h3>

<?php require_once './vistas/tesorero/formMostrarDonaciones.php'; ?>

<br />
</div>

<!-- ****************************** Fin Cuerpo central  ******************** -->
</div>
<!-- *********************** Fin vCuerpoMostrarDonaciones.php *********** -->