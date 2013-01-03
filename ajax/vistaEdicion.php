<?php

require_once("../src/class/session.php");

/**
* VISTA DE EDICION DE CATEGORIAS
*/

//SEGURIDAD DE QUE ESTA LOGUEADO
$session = new Session();
$session->Logueado();


?>

			<div class="topControls" >
				
				<!-- menu proyectos -->
				<div id="categoriasControls" >

					<!-- Lista Proyectos 
					<input type="radio" id="EditarCategorias" name="radio" checked="checked" />
						<label for="EditarCategorias" onClick="EditarCategorias()">
						Categorias
						</label>

					<! -- Nuevo proyecto 
					<input type="radio" id="Editarvars" name="radio"/>
						<label for="Editarvars" onClick="EditarNormas()">
						Normas
						</label>

				</div>
				<hr> 
				<script type="text/javascript">
					SetBotones('categoriasControls');
				</script>
				<! -- end menu proyectos -->
			</div>

			<div id="vista" class="vistaEdicion" >
				<!--
				<div id="nivel1">

					<div id="nombreNorma">
						TITULO
					</div>	
					<div id="generalidades">

						<input type="radio" id="radiox" name="radio"/>
						<label for="radio" onClick="xxx()">Lista Proyectos</label>

						<input type="radio" id="radio2" name="radio1"/>
						<label for="radio2" onClick="xxx()">Lista Proyectos</label>

						<input type="radio" id="radio3" name="radio2"/>
						<label for="radio3" onClick="xxx()">Lista Proyectos</label>

					</div>

				</div><!-- end nivel 1- ->

				<div id="nivel2">
						
						<div class="box">
							<div class="titulo">
								Titulo
								<img class="close" src="images/close.png" />
							</div>
							<div class="content">
								TODO ajax para mostrar informacion de subcategorias<br/>
								TODO mansory para acomodar las columnas
							</div>
						</div>
						<div class="box">
							<div class="titulo">
								Titulo
								<img class="close" src="images/close.png" />
							</div>
							<div class="content">
							TODO ajax para mostrar informacion de subcategorias<br/>
							TODO mansory para acomodar las columnas
							</div>
						</div>
						<div class="box">
							<div class="titulo">
								Titulo
								<img class="close" src="images/close.png" />
							</div>
							<div class="content">
								TODO ajax para mostrar informacion de subcategorias<br/>
								TODO mansory para acomodar las columnas
								<br/>
								<br/>
								<br/>
								<br/>
							</div>
						</div>
						<div class="box">
							<div class="titulo">Titulo</div>
							<div class="content">
								TODO ajax para mostrar informacion de subcategorias<br/>
								TODO mansory para acomodar las columnas
							</div>
						</div>

				</div><!-- end nivel 2- ->

			</div>
			-->
			<script type="text/javascript">
					//RestaurarCategorias();
			</script>
			<!-- end nivel 1-->