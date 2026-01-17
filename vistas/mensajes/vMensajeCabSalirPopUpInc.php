<?php

/* -----------------------------------------------------------------------------
  FICHERO: vMensajeCabSalirPopUpInc.php
  VERSION: PHP 5.2.3
  DESCRIPCION: Se utiliza para los mensajes que proceden de PopUp, tiene un botón
  de "aceptar" que cierra la ventana blank
  En la cabecera incluye solo el links para "Salir"
  OBSERVACIONES:Llamado desde cEnlacesPie
  ------------------------------------------------------------------------------ */

function vMensajeCabSalirPopUpInc($arrayParamMensaje) {
    require_once './vistas/plantillasGrales/vCabeceraSalir.php';

    require_once './vistas/mensajes/vCuerpoMensajePopUp.php';

    require_once './vistas/plantillasGrales/vPieFinal.php';
}

?>