<?php
	/**
	 * EJECUTA LAS NOTIFICACIONES
	 */
	require_once("notificaciones.php");

	//notificaciones
	$notifcaciones = new Notificaciones();

	//notificaciones de permisos
	$notifcaciones->Permisos();