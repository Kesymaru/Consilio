<?php 

/**
* VISTA DE PROYECTOS
*/

require_once("../src/class/session.php"); 
require_once("../src/class/proyectos.php");

//seguridad de que esta logueado
$session = new Session();
$session->Logueado();

$proyectos = new Proyectos();

?>
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

					<!-- Editar proyecto 
					<input type="radio" id="EditarProyecto" name="radio"/>
						<label for="EditarProyecto" onClick="EditarProyecto()">
						Editar
						</label>
					-->

					<!-- Exporat proyecto 
					<input type="radio" id="ExportarProyecto" name="radio"/>
						<label for="ExportarProyecto" onClick="ExportarProyecto()">
						Exportar
						</label>
					-->
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