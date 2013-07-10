<?php
	/**
	 * EJECUTA LAS NOTIFICACIONES
	 */
	require_once("class/notificaciones.php");

	//notificaciones
	$notifcaciones = new Notificaciones();

	//notificaciones de permisos
	$notifcaciones->Permisos();