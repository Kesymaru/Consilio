<?php

/**
* AJAX PARA ENTIDADES
*/

require_once("class/registros.php");


if(isset($_POST['func'])){
	
	switch ($_POST['func']){
		case 'Entidades':
			Entidades();
			break;
	}
}

/**
* CARGA LA LISTA DE ENTIDADES
*/
function Entidades(){
	$registros = new Registros();

	$entidades = $registros->getEntidades();

	$lista = '<div id="tipos" class="tipos">
				<div class="titulo">
					Entidades
			  		<hr>
			  	</div>';

	if(!empty($entidades)){
		$lista .= '<ul>';

		foreach ($entidades as $fila => $entidad) {
			$lista .= '<li id="'.$entidad['id'].'" onClick="SelectTipo('.$entidad['id'].')">'.$entidad['nombre'].'</li>';
		}

		$lista .= '</ul>';

	}else{
		$lista .= 'No hay entidades.';
	}

	$lista .= '<div class="datos-botones">
				<button type="button" id="EliminarEntidad" onClick="EliminarEntidad()">Eliminar</button>
				<button type="button" id="EditarEntidad" onClick="EditarEntidad()">Editar</button>
			   	<button type="button" id="NuevoEntidad" onClick="NuevoEntidad()">Nuevo Tipo</button>
			   </div>
			   <!-- fin botonera -->
			   </div>';

	echo $lista;
}

?>