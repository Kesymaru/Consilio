<?php 
require_once("src/class/session.php");

$session = new SessionInvitado();


if( isset($_GET['reset']) ){
	$_SESSION['reset'] = true;
}

/*
$ip = $REMOTE_ADDR; 
$ip = GetHostByName( $REMOTE_ADDR );
echo 'La ip es: '+$ip;
echo '<br/>Desde gloobal: '.$_SERVER['REMOTE_ADDR'];
*/


?>
<!doctype html public>
<!--[if lt IE 7]> <html lang="en-us" class="lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>    <html lang="en-us" class="lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>    <html lang="en-us" class="lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="en-us"> <!--<![endif]-->
<html>

<head>
	<title>Escala Admin</title>
	
	<meta charset="utf-8">
	<link rel="shortcut icon" href="/favicon.ico"> 

	<link rel="stylesheet" href="css/login.css" type="text/css">
	<link rel="stylesheet" href="css/jquery-ui-1.9.0.custom.css" type="text/css">
	<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css">

	<!-- jquery -->
	<script type="text/javascript" src="js/jquery-1.8.2.js"></script> 
	<!-- <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script> -->
	<script type="text/javascript" src="js/jquery-ui-1.9.0.custom.js"></script>
	
	<!-- validacion de form -->
	<script type="text/javascript" src="js/languages/jquery.validationEngine-es.js" ></script>
	<script type="text/javascript" src="js/jquery.validationEngine.js" ></script>

	<!-- placeholder para ie -->
	<script type="text/javascript" src="js/jquery.placeholder.js" ></script>

	<!-- notificaciones -->
	<script type="text/javascript" src="js/noty/jquery.noty.js"></script>
	<script type="text/javascript" src="js/noty/layouts/topCenter.js"></script>
	<script type="text/javascript" src="js/noty/themes/default.js"></script>
	
	<!-- login -->
	<script src="js/login.js"></script>

</head>
<body>

<div id="contenedorCentro">

	<div class="loginbox">
	<form method="post" id="formID">

		<div id="usuarios">

			<div class="titulo">
		<?php
			if(!isset($_GET['reset'])){
				echo 'Admin';
			}else{
				echo 'Admin Password Reseteado';
			}
		?>
			</div>
			<div class="logoForm">
				<img src="images/logo.png" />
			</div>
			<div class="contenido" id="login">
				
				<div class="etiquetas">Usuario</div>

				<input type="text" class="validate[required] borderAlto usuario" placeholder="Usuario" id="usuario" name="usuario"
					<?php
						if(isset($_GET['usuario'])){
							echo 'value="'.$_GET['usuario'].'"';
						}
					?>
				autofocus="autofocus" />
				<div class="etiquetas">Password</div>
				<input type="password" class="validate[required] borderBajo" placeholder="Password" id="password" name="password" />
				
			</div>

			<!-- recuperacion -->

		<?php
			if( !isset($_GET['reset'])){
		?>
			<!-- <span id="recuperacion" onClick="formRecuperacion()">
				¿Has olvidado tu contraseña?
			</span> -->
		
		<?php
			}
		?>

			<div id="formRecuperacion">

				<div class="etiquetas">Usuario</div>

				<input type="text" class="validate[optional,custom[onlyLetterSp]] borderAlto usuario" placeholder="Usuario" id="usuarioRecuperacion" name="usuarioRecuperacion" />

				<div class="etiquetas">Email</div>

				<input type="email" class="validate[optional,custom[email]] borderBajo" placeholder="Email" id="emailRecuperacion" name="emailRecuperacion" />

			</div>
			<!-- end formRecuperacion -->

			<div class="controls">

				<input type="submit" onClick="logIn()" id="entrar" value="Entrar"></input>

				<button onClick="resetar()" id="resetear">Resetear</button>
			
			<?php
			//si no esta reseteando el password
				if( !isset($_GET['reset'])){	
			?>
					
			<?php
				}
			?>
			</div>
			<!--
			<span id="registrarse" onClick="loginbox(1)">Registrarse</span>
			-->
			<br/><br/>
		</div>
		<!-- end usuarios -->
	</form>

	</div>
</div>

<?php

if( isset($_SESSION['bloqueado'])){
	$ip= $_SERVER['REMOTE_ADDR']; 

	$_SESSION['bloquedo'] = true;

	echo '<script>
			Bloqueado("'.$ip.'");
		  </script>';
}

?>

</body>
</html>

