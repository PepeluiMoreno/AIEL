<?php
/* --------------------------- Inicio my_session_start()-------------------------
FICHERO: my_sessions.php 
PROYECTO: EL
VERSION: php 7.3.19
Cuando se ejecuta session_start() y hay activada una sesion, puede mostrar un "Notice"
aunque no es un error, si warning esta activado. Para evitar estos Notices, 
he creado esta función que controla session_status() para comprobar si ya está activa.

PHP_SESSION_DISABLED si las sesiones están deshabilitadas.
PHP_SESSION_NONE si las sesiones están habilitadas, pero no existe ninguna.
PHP_SESSION_ACTIVE si las sesiones están habilitadas, y existe una.

Dependiendo de PHP settings, puede no devolver lo de arriba y en su lugar sería:
_DISABLED = 0
_NONE = 1
_ACTIVE = 2	

INFORMACION sobre $_SESSION: Al crear una variable en PHP esta se encontrará 
disponible durante la ejecución de la página contenida en un archivo.php, 
eliminándose automáticamente una vez finalizado el script.

En ocasiones necesitaremos que determinada información esté disponible en  
diferentes páginas en PHP (archivos) y en posteriores accesos a las mismas, 
para verlas o moficarlas. Para ello podemos usar variables de sesión $_SESSION,
que es un array especial utilizado para guardar información a través de los 
requests que un usuario hace durante su visita. 
Esta información se guarda en el servidor, mientras que en las cookies la 
información se guarda en disposivo del visitante.
	
La información guardada en una sesión puede llamarse en cualquier momento 
mientras la sesión esté abierta.	

Para abrir una sesión hay que utilizar la función session_start() para poder
utilizar las variables de sesión, y debe hacerse al principio del código PHP
de los scripts y antes de que que cualquier texto, HTML o JavaScript. 

Para no tener que poner session_start() al principio de cada script, 
podríamos configurar PHP para que la sesión se inicie o continúe automáticamente 
abierto sin necesidad de abrir al principio de cada página, para ello en el 
archivo de configuración de PHP (php.ini) localiza la línea session.auto_start = 0
y sustitúyela por: session.auto_start = 1 

Pero para no depender de posibles modificaciones de configuración del php.ini,
prefiero usar session_start()

LLAMADA: desde todos los scripts que usan variables de sesión

OBSERVACIONES: 2020-07-29: mejora para evitar Notices
--------------------------- Fin my_session_start()-------------------------*/
function is_session_started()
{   
  //echo "<br><br>0_1 my_sessions.php:is_session_started:phpversion: ";echo phpversion();	echo "<br>";

								
		if ( version_compare(phpversion(), '5.4.0', '>=') ) 
		{  
					//echo "<br><br>1 my_sessions.php:is_session_started:session_status: ";echo session_status();	echo "<br>";
					
											
					if (session_status() === PHP_SESSION_NONE ) 	
					{ 								
								return FALSE;
					}							
					else 
					{
								return TRUE;
					}								
		} 
		else 
		{
					//echo "<br><br>2-1 my_sessions.php:is_session_started:session_status: ";echo session_status();	echo "<br>";
					//echo "<br><br>2-2 my_sessions.php:is_session_started:session_id: ";echo session_id();	echo "<br>";
				
					if(session_id() === '') 
					{
									return FALSE;
					}
					else 
					{
								return TRUE;
					}								
		}						
}

// Uso: if ( is_session_started() === FALSE ) session_start();

?>