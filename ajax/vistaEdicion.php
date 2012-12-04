<?php

require_once("../src/class/session.php"); 
require_once("../src/class/registros.php");

/**
* VISTA DE EDICION DE CATEGORIAS
*/

//SEGURIDAD DE QUE ESTA LOGUEADO
$session = new Session();
$session->Logueado();

?>
<!-- JAVASCRIPT NECESARIO -->
<script type="text/javascript">
	ActivaMenu();


</script>
			<div class="topControls" >
				
				<!-- menu proyectos -->
				<div id="edicionConstrols" >

					<!-- Lista Proyectos -->
					<input type="radio" id="EditarNormas" name="radio"/>
						<label for="EditarNormas" onClick="EditarNormas()">
						Editar Normas
						</label>

					<!-- Nuevo proyecto -->
					<input type="radio" id="NuevoProyecto" name="radio"/>
						<label for="NuevoProyecto" onClick="NuevoProyecto()">
						Nuevo
						</label>

				</div>
				<hr>
				<script type="text/javascript">
					SetBotones('edicionConstrols');
				</script>

				<!-- end menu proyectos -->
			</div>

			<div id="vista">
				<div class="vista">

				</div>
			</div>

			<script type="text/javascript">
					//Restaurar();
			</script>
			<!-- end nivel 1-->