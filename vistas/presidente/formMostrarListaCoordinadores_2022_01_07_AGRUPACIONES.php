<?php
/*--------------------------------------------------------------------------------------------------
FICHERO: formMostrarListaCoordinadores.php
PROYECTO: EL
VERSION: PHP 7.3.21

DESCRIPCION: Se forma una tabla con la lista actual de las Área de gestión (incluye agrupaciones) 
y de socios con los roles de coordinación correspondiente y datos relacionados

LLAMADA: vistas/presidente/vCuerpoMostrarListaCoordinadores.php
y previamente desde  cPresidente.php:mostrarListaCoordinadores(),
												
OBSERVACIONES:

--------------------------------------------------------------------------------------------------*/
?>

<div id="registro">

 <br />
	<span class='textoNegro8Left'>
	
		- Un Área de Coordinación con ámbito de Comunidad Autónoma (CCAA) incluye a todas las agrupaciones (provincias) de esa CCAA 
		<br />
		- Un Área de Coordinación con ámbito de una provincia (o CCAA uniprovincial) solo incluye a la agrupación correspondiente a esa provincia 
		<br />
		- El Área de Coordinación "Europa Laica Estatal e Internacional" está pensada para incluir a los socios/as que residan en el extranjero

		<br /><br />	
		.Los socios/as con rol sobre un Área de Coordinación concreto, tienen acceso a los datos y envíos de emails 
			a todos los socios/as de las agrupaciones territoriales (provincias) incluidas en ese Área de Coordinación 
		<br /><br />
		.Los emails se enviarán desde la correspondiente dirección "areagestion@europalaica.org"
		<br /><br />
	
		- Pueden existir "subagrupaciones", pero no está contemplado ese nivel de ámbito dentro de esta aplicación de Gestión de Soci@s
			<br /><br />				
	- Para tareas de apoyo en la coordinación, Presidencia puede asignar a más de un socio/a	el rol de Coordinación sobre un determinado Área de Coordinación, 
	  pero un socio/a solo puede tener asignado un único rol.
	</span>
	<br /> 	<br /> 
	
	<div align="left">
			
		<table width="100%" border="1" cellspacing="0" cellpadding="0" bordercolor="#99CCFF">
		
				<!--<tr bgcolor="#CCCCCC">	-->
				<tr bgcolor="#CCCCCC" height='35'>		
		
				<th valign="top" class="textoAzul8C"><strong>Áreas de gestión agrupaciones</strong></th>			
				<th valign="top" class="textoAzul8C"><strong>Email Área de gestión Agrupaciones</strong></th>					
				<!--	<th valign="top" class="textoAzul8C"><strong>Agrupación</strong><br /><br /></th>-->
				<!--<th valign="top" class="textoAzul8C"><strong>Email coordinación</strong></th>	-->
				<!--<th valign="top" class="textoAzul8C"><strong>Área gestión</strong></th>			-->		
				<!--<th valign="top" class="textoAzul8C"><strong>Email área de gestión</strong></th>-->
				
				<th valign="top" class="textoAzul8C"><strong>Nombre</strong></th> 						
				<th valign="top" class="textoAzul8C"><strong>Email personal</strong></th> 
				<th valign="top" class="textoAzul8C"><strong>Tel. fijo</strong><br /></th>  

				<th valign="top" class="textoAzul8C"><strong>Tel. móvil</strong></th> 
				<th valign="top" class="textoAzul8C"><strong>Provincia</strong></th> 
															
				<th valign="top" class="textoAzul8C"><strong>Localidad</strong></th> 
				<!--<th valign="top" class="textoAzul8C"><strong>CP</strong></th> 
				<th valign="top" class="textoAzul8C"><strong>Dirección</strong></th> 
				-->

			</tr>	
			
			<?php 

			$items = $resDatosCoordinadores['resultadoFilas'];
			//echo "<br><br>items:";print_r($items); echo "<br><br>";
			
			$codAreaGestionCCAA = 'NNN';

			$bgcolor1 = '#fffff';				
			$bgcolor2 = '#fffed1';					
			$bgcolorA = $bgcolor2;
			
			foreach ($items as $fila => $contenidoFila)
			{
				//Al cambiár de área de gestión que cambia el color de la fila
				//Los 3 primeros dígitos de 'CODAREAGESTION' de cada Áreas de Gestión (CCAA), 
				//y también coinciden con los 3 primeros dígitos de las agrupaciones (provincias) que pertenecen a un mismo área de gestión, 
				//los siguientes dígitos son propios de cada agrupación (provincias)	y difieren en algún valor
				
				if (substr($items[$fila]['CODAREAGESTION'], 0, 3) !== $codAreaGestionCCAA)	
				{ 
						//echo "<br><br>items[$fila]['NOMAREAGESTION']:";print_r($items[$fila]['NOMAREAGESTION']);
						
						$codAreaGestionCCAA = substr($items[$fila]['CODAREAGESTION'], 0, 3);
							
						if ($bgcolorA == $bgcolor1)
						{$bgcolorA = $bgcolor2;
						}
						else
						{ $bgcolorA = $bgcolor1;
						}						
							//echo "<br><br>bgcolorA: ";print_r($bgcolorA);								
				}	
				
				echo ("<tr height='30'>"); 
				echo ("<td class='textoAzul8L' bgcolor=$bgcolorA><strong>".$items[$fila]['NOMAREAGESTION']."</strong></td>");							
				echo ("<td class='textoAzul8L' bgcolor=$bgcolorA>".$items[$fila]['emailAreaGestion']."</td>"); 						
				
				//echo ("<td class='textoAzul8L'><strong>".$items[$fila]['NOMAGRUPACION']."</strong></td>");
				//echo ("<td class='textoAzul8L'>".$items[$fila]['EMAILCOORD']."</td>");  																				
				//echo ("<td class='textoAzul8L' bgcolor='#ccffcc'><strong>".$items[$fila]['NOMAREAGESTION']."</strong></td>");
				//echo ("<td class='textoAzul8L' bgcolor='#ccffcc'>".$items[$fila]['emailAreaGestion']."</td>");

				echo ("<td class='textoAzul8L' bgcolor=$bgcolorA><strong>&nbsp;".mb_convert_case($items[$fila]['APE1'],MB_CASE_TITLE,"UTF-8")." &nbsp;".
																																																																					mb_convert_case($items[$fila]['APE2'],MB_CASE_TITLE,"UTF-8").", &nbsp;".
																																																																					mb_convert_case($items[$fila]['NOM'], MB_CASE_TITLE, "UTF-8")."</strong></td>");
							
				echo ("<td class='textoAzul8L' bgcolor=$bgcolorA>".$items[$fila]['EMAIL']."</td>");	
				echo ("<td class='textoAzul8L' bgcolor=$bgcolorA>&nbsp;".$items[$fila]['TELFIJOCASA']."&nbsp;</td>");			
				echo ("<td class='textoAzul8L' bgcolor=$bgcolorA>&nbsp;".$items[$fila]['TELMOVIL']."&nbsp;</td>");  						
					
				echo ("<td class='textoAzul8L' bgcolor=$bgcolorA>&nbsp;<strong>".$items[$fila]['NOMPROVINCIA']."</strong></td>"); 
				echo ("<td class='textoAzul8L' bgcolor=$bgcolorA>".$items[$fila]['LOCALIDAD']."</td>");
				//echo ("<td class='textoAzul8L'>".$items[$fila]['CP']."</td>"); 
				//echo ("<td class='textoAzul8L'>".$items[$fila]['DIRECCION']."</td>");			

				echo ("</tr>");
				}
				?>
		</table> 
	</div> 
	<!-- ************************ Fin datos socios ************************* -->						
</div>		
