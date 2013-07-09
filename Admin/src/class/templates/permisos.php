<!doctype html>
<html lang="es-ES">
<head>
    <meta charset="utf-8">
    <title>Matriz Escala</title>
</head>
<body class="mail" style="background-color: #f4f4f4;">
    <div class="mail" style="background-color: #f4f4f4;">
        <br/>
        <br/>
        <br/>
        <table class="tabla" style="border: 1px solid #747273; border-collapse: collapse; box-shadow: 0 0 2px 1px #747273; padding: 0; width:90%; margin: 0 auto; font-size: 20px;">
            <tr class="asunto" style="text-align: center; background-color: #6FA414; font-size: 22px; font-weight: bold; padding: 10px; color: #FFFFFF;">
                <th colspan="2">
                    {{title}}
                </th>
            </tr>
            <tr class="contenido" style="text-align: left; background-color: #FFFFFF;">
                <td colspan="2" class="tabla-td" style="padding: 10px;">
                    {{menssage}}
                </td>
            </tr>
            <tr class="contenido" style="text-align: center; background-color: #FFFFFF;">
                <td colspan="2" class="tabla-td" >
					{{permisos}}
                </td>
            </tr>
	        <tr class="contenido" style="text-align: center; background-color: #FFFFFF;">
		        <td colspan="2" >
			        <br/>
		        </td>
	        </tr>
            <tr class="contenidoFooter" style="background-color: #FFFFFF;">
                <td class="td-logo" style="text-align: left; height: 80px;">
                    <img class="logo" style="display:block; float: left; height: 80px; padding-left: 10px;  padding-bottom: 10px;" src="http://development.77digital.com/matrizescala/images/logoMail.png" title="Escala Consultores" alt="Escala Consultores">
                </td>
                <td class="td-logoCliente" style="text-align: right; height: 80px;">
                    <img class="logoCliente" style="display:block; float: right; height: 80px; width: 250px; padding-right: 10px; padding-bottom: 10px;" src="{{cliente_imagen}}" alt="{{cliente_nombre}}" title="{{cliente_nombre}}">
                </td>
            </tr>
        </table>

        <table class="footer" style="font-size: 16px; border: 0; text-align: left; border: 0px; margin: 20px auto; background-color: #f4f4f4;" >
            <tr>
                <td rowspan="7" class="direccion" style="padding-right: 20px;" >
                    {{info_from}}
                    <br/>
                    {{direccion_edificio}}
                    <br/>
                    {{direccion}}
                </td>
            </tr><tr>
            <td>
                Mobile
            </td>
            <td>
                {{info_mobile}}
            </td>
        </tr><tr>
            <td>
                Oficina
            </td>
            <td>
                {{info_phone}}
            </td>
        </tr><tr>
            <td>
                Email
            </td>
            <td>
	            <a href="mailto:{{info_email}}">
		            {{info_email}}
	            </a>
            </td>
        </tr><tr>
            <td>
                Website:
            </td>
            <td>
                <a href="{{info_home}}">
                    {{info_home}}
                </a>
            </td>
        </tr>
        </table>

        <div class="disclaim" style="width: 90%; display: block; border-top: 1px solid #dedede; text-align: left !important; font-size: 14px; margin-bottom: 10px; padding-left: 10px; padding-right: 10px; background-color: #f4f4f4; padding-top: 5px; margin-left: auto; margin-right: auto;" >
                        <span class="bold" style="font-weight: bold !important;" >
                        {{disclaim_title}}
                        </span>
            <br/>
            {{disclaim_text}}
        </div>
    </div>
</body>
</html>
