<?php

require_once("../src/class/registros.php");

if(isset($_POST['func'])){
	
	switch ($_POST['func']){

		case 'Padres':
			echo '<hr>Categorias<hr>';
			echo '<div id="categorias">';
			echo '<div class="categoria" id="Padre0">';

			$registros = new Registros();
			$padres = $registros->Hijos(0);

			if(!empty($padres)){
				$id = 0;
				$nombre = "";
				echo '<ul>';
				foreach ($padres as $f => $c) {
					foreach ($padres[$f] as $campo => $valor) {
						
						if($campo == 'id'){
							$id = $valor;
						}
						if($campo == 'nombre'){
							$nombre = $valor;
						}else{
							continue;
						}
					}
					echo '<li id="'.$id.'" onClick="Hijos('.$id.')">'.$nombre.'</li>';
				}
				echo '</ul>';
			}else{
				echo 'No hay datos.';
			}
			echo '</div>';
			echo '</div>';
			break;

		//OBTIENE LOS HIJOS DE UN PADRE SELECCIONADO
		case 'Hijos':
			if(isset($_POST['padre'])){
				$registros = new Registros();
				$hijos = $registros->Hijos($_POST['padre']);

				if(!empty($hijos)){
					echo '<div class="categoria" id="Padre'.$_POST['padre'].'">';

					$id = 0;
					$nombre = "";

					echo '<ul>';
					foreach ($hijos as $f => $c) {
						foreach ($hijos[$f] as $campo => $valor) {
							
							if($campo == 'id'){
								$id = $valor;
							}
							if($campo == 'nombre'){
								$nombre = $valor;
							}else{
								continue;
							}
						}
						echo '<li id="'.$id.'" onClick="Hijos('.$id.')">'.$nombre.'</li>';
					}
					echo '</ul>';
					echo '</div>';
				}else{
					echo '<script>SeleccionarCategoria('.$_POST['padre'].');</script>';
				}
				
			}
			break;

		//TODOS LOS HIJOS DE UN PADRE
		case 'PadreHijos':
			if( isset($_POST['padre']) ){
				$registros = new Registros();
				$hijos = $registros->HijosId($_POST['padre']);
				echo json_encode($hijos);
			}
			break;
	}

}

?>