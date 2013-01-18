<?php

require_once("../src/class/session.php"); 
require_once("../src/class/registros.php");

/**
* VISTA DE USUARIOS
*/

//SEGURIDAD DE QUE ESTA LOGUEADO
$session = new Session();
$session->Logueado();

$proyectos = new Proyectos();

?>
<!-- JAVASCRIPT NECESARIO -->
<script type="text/javascript">

</script>
			<div class="topControls" >
				
				<!-- menu proyectos -->
				<div id="proyectoControls" >

					<!-- Lista Proyectos -->
					<input type="radio" id="ListaProyectos" name="radio"/>
						<label for="ListaProyectos" onClick="ListaProyectos()">
						Lista Proyectos
						</label>

					<!-- Nuevo proyecto -->
					<input type="radio" id="NuevoProyecto" name="radio"/>
						<label for="NuevoProyecto" onClick="NuevoProyecto()">
						Nuevo
						</label>
				</div>
				<hr>
				<script type="text/javascript">
					SetBotones('proyectoControls');
				</script>

				<!-- end menu proyectos -->
			</div>

			<div id="vista">
				<div class="vista">

				</div>
			</div>

			<script type="text/javascript">
					Restaurar();
			</script>
			<!-- end nivel 1-->