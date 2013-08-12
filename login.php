<?php 
require_once("src/class/session.php");

$session = new SessionInvitado();

/*
//logueo
if( isset($_SESSION['logueado']) ){
	$home = $_SESSION['home']."/index.php";
	header('Location: '.$home);
	exit;
}*/

//ie compatible fix
header('X-UA-Compatible: IE=EDGE');

?>
<!doctype html public>
<!--[if lt IE 7]> <html lang="en-us" class="lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>    <html lang="en-us" class="lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>    <html lang="en-us" class="lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="en-us"> <!--<![endif]-->
<html>

<head>
	<!-- ie compatible mode off -->
	<meta http-equiv="X-UA-Compatible" content="IE=EDGE" />

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta charset="utf-8">

	<title>Escala</title>
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
				echo 'Ingresar';
			}else{
				echo 'Password Reseteado';
			}
		?>
			</div>
			<div class="logoForm">
				<img src="images/logo.png" />
			</div>
			<div class="contenido" id="login">
				
				<input type="text" class="validate[required] borderAlto usuario" placeholder="Usuario" id="usuario" name="usuario"
					<?php
						if(isset($_GET['usuario'])){
							echo 'value="'.$_GET['usuario'].'"';
						}
					?>
				autofocus="autofocus" />

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

				<input type="text" class="validate[optional,custom[onlyLetterSp]] borderAlto usuario" placeholder="Usuario" id="usuarioRecuperacion" name="usuarioRecuperacion" />

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

			<!-- MUESTRA EL FORMULARIO DE REGISTRO
			<span id="registrarse" onClick="loginbox(1)">Registrarse</span>
			-->
			<br/><br/>
		</div>
		<!-- end usuarios -->
<?php
	//si no biene de reset
	/*if(!isset($_GET['reset'])){
?>
		<!-- <div id="registroUsuarios" >

			<div class="titulo">
				Registro
			</div>
			<div class="logoForm">
				<img src="images/logo.png" />
			</div>
			<div class="contenido">

				<input type="text" class="validate[required] borderAlto" id="registroUsuario" placeholder="Usuario" name="registroUsuario"><br/>

				<input type="email" class="validate[required,custom[email]]" id="registroEmail" placeholder="Email" name="registroEmail">

				<input class="validate[required]" id="registroPassword1" placeholder="Password" name="registroPassword1" type="password" />

				<input class="validate[required,equals[registroPassword1]] borderBajo" placeholder="Confirmar password" name="registroPassword2" type="password" />
				
				<div class="controls">
					<button onClick="registro()" id="registrar">Registrarse</button>
				</div>

			</div>

			<span onClick="loginbox(2)">Usuarios</span>
			<br/><br/>
		</div>
		<!-- end registroUsuario -->
<?php
	} *///fin if reset
?>
	</form>

	</div>
	
	<div id="notifica" class="oculto">
	</div>

</div>

</body>
</html>