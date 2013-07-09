<!-- template para permisos que aun no han expirado -->
<table style="background: #edeaea; border: none; border-collapse: collapse; margin-top: 10px; margin-left: 5px; display: inline-block; width: 400px; max-width: 450px;">
	<tbody style="width: 100% !important;">
	<tr>
		<td colspan="2" style="text-align: center; font-weight: bold; background: #a0ca48; color: #fff; font-size: 20px; width: 100%;" >
			{{title}}
		</td>
		<td rowspan="3" style="max-width: 80px;">
			<img style="width: 80px; margin-left: -2px; margin-top: -1px;" src="http://development.77digital.com/matrizescala/images/banderin.png" alt="Recordatorio Permiso" title="Recordatorio Permiso" >
		</td>
	</tr>
	<tr>
		<td style="font-weight: bold; text-align: left; padding-left: 5px;">
			Emisi&oacuten
		</td>
		<td  style="padding-right: 5px;">
			{{fecha_recordatorio}}
		</td>
	</tr>
	<tr>
		<td style="font-weight: bold; text-align: left; padding-left: 5px; width: 40%;">
			Expiracion
		</td>
		<td style="padding-right: 5px; width: 60%;">
			{{fecha_expiracion}}
		</td>
	</tr>
	<tr>
		<td style="font-weight: bold; text-align: left; padding-left: 5px;">
			Recordatorio
		</td>
		<td  style="padding-right: 5px;">
			{{fecha_recordatorio}}
		</td>
	</tr>
	</tbody>
</table>