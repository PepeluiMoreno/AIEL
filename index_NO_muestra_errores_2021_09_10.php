<?php
/*-----------------------------------------------------------------------------
FICHERO: index.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: Será URL de entrada de la aplicación, recibe el nombre del
             controlador y la acción (función), después de comprobar la
             existencia de los dos, llamará a la función correspondiente del
             controlador.
													
OBSERVACIONES: Al llamar index.php sin parámetros, llama al contralor y acción
               por defecto:"controladorLogin" y función "validarLogin" para
               validación del usuario en la BBDD.

VERSIÓN EXPLOTACIÓN: DESACTIVADO MOSTRAR NOTICES, WARNIGS, ERRORES EN PANTALLA															
------------------------------------------------------------------------------*/
//echo "Dentro index.php1";

//--- Inicio MOSTRAR ERRORES: notice, warning, y errores ----------------------- 

//--- OJO: ACTIVAR SOLO PARA VERSIONES DESARROLLO, MANTENIMIENTO, O PRUEBAS ----
/*
  ini_set('display_errors', 1);
  error_reporting(E_ALL);
*/
//----------- FIN MOSTRAR ERRORES: notice, warning, y errores -------------------

date_default_timezone_set('EUROPE/MADRID');                             //<1>

$carpetaControladores = "./controladores/";																													//<2>
$controladorPredefinido = "controladorLogin";																											//<3>
$accionPredefinida = "validarLogin";																																				//<4>

$arrayParamMensaje['textoCabecera'] = 'Identificación como usuario';
$arrayParamMensaje['textoBoton'] = 'Salir de la aplicación';
$arrayParamMensaje['enlaceBoton'] = './index.php?controlador=controladorLogin&amp;accion=logOut';	

if(!empty($_GET['controlador']))																																								//<5>
{  $controlador = $_GET['controlador'];
}
else
{  $controlador = $controladorPredefinido;
}

if(!empty($_GET['accion']))																																												//<6>
{  $accion = $_GET['accion'];
}
else
{  $accion = $accionPredefinida;
}

$controlador = $carpetaControladores . $controlador . '.php';										//<7>

if(is_file($controlador)) 																																													//<8>
{
  require_once $controlador;
  
  /*if(is_callable($accion))																																													//<9>
  {
   $accion();
  }
  else
  {//die('La accion no existe - 404 not found');//header("HTTP/1.1 404 Not Found");
			$arrayParamMensaje['textoComentarios']='Error del sistema al identificarse: 404 not found.
			     <br /><br />La acción no existe: vuelva a intentarlo pasado un tiempo ';

   require_once './vistas/mensajes/vMensajeCabInicialInc.php';						
   vMensajeCabInicialInc("",$arrayParamMensaje,"");			
  } 
		*/
		if(is_callable($accion)) //Llamamos la accion o detenemos todo si no existe
		{
    if (isset($_GET['parametro']) && !empty($_GET['parametro']))
    { $parametro = $_GET['parametro'];
				
		    $accion($parametro);
		  }
		  else
		  {$accion();
		  }
		}      
  else
  {//die('La accion no existe - 404 not found');//header("HTTP/1.1 404 Not Found");
			$arrayParamMensaje['textoComentarios'] = 'Error del sistema al identificarse: 404 not found.
			                    <br /><br />La acción no existe: vuelva a intentarlo pasado un tiempo ';

   require_once './vistas/mensajes/vMensajeCabInicialInc.php';						
   vMensajeCabInicialInc("",$arrayParamMensaje,"");			
  }		
}
else// !if(is_file($controlador))
{
	//die('El controlador no existe - 404 not found');//header("HTTP/1.1 404 Not Found");
	$arrayParamMensaje['textoComentarios'] = 'Error del sistema al identificarse: 404 not found.
			             <br /><br /> El controlador no existe: vuelva a intentarlo pasado un tiempo ';

 require_once './vistas/mensajes/vMensajeCabInicialInc.php';						
 vMensajeCabInicialInc("",$arrayParamMensaje,"");
	
}

/*----------------------------------------------------------------------------
COMENTARIOS:
<1>Sets the default timezone used by all date/time functions in a script
<2>Carpeta donde buscaremos los controladores
<3>Si no se recibe como parámetro un controlador, este se usará por defecto
<4>Si no se indica una accion, esta accion (función) se usará por defecto
<5>Si en la URL existe el parámetro "controlador", se asigna a la variable
   $controlador, si no se asigna el controlador predefinido
<6>Si en la URL existe el parámetro "accion", se asigna a la variable
   $accion, si no se asigna la acción predefinida
<7>Formamos el path y nombre del controlador.
<8>Incluimos el fichero del controlador o detenemos todo si no existe
<9>Llamamos la acción o detenemos todo si no existe

EJEMPLOS DE FORMAS DE LLAMADA URL EN PC:
1ª vez:http://localhost/EL/index.php
otras formas:
http://localhost/EL/index.php?controlador=controladorLogin&accion=validarLogin
*/
?>
