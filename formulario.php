<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auditar Orden</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f9;
        }
        .container {
            width: 90%;
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            border: 1px solid #ccc;
            text-align: center;
            padding: 10px;
        }
        table th {
            background-color: #f9f9f9;
        }
        textarea {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
            display: none;
            box-sizing: border-box;
            resize: vertical;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }

        /* Colores para las filas según la selección */
        .conforme {
            background-color: #d4edda;
        }
        .no-conforme {
            background-color: #f8d7da;
        }
        .no-aplica {
            background-color: #ffffff;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Auditar Orden</h1>
    <form method="POST" action="guardar_respuestas.php">
        <!-- Mostrar los detalles de la orden seleccionada -->

        <h3>Diligenciamiento del acta de SIPREM</h3>
        <!-- Tabla de preguntas y respuestas -->
        <table>
            <thead>
                <tr>
                    <th>Pregunta</th>
                    <th>Conforme</th>
                    <th>No Conforme</th>
                    <th>No Aplica</th>
                    <th>Observación</th>
                </tr>
            </thead>
            <tbody>
                <tr id="question-group-1">
                    <td>Informacion preliminar del cliente</td>
                    <td><input type="radio" name="respuesta1" value="Conforme" onclick="handleSelection(1, 'Conforme')" required></td>
                    <td><input type="radio" name="respuesta1" value="No Conforme" onclick="handleSelection(1, 'No Conforme')"></td>
                    <td><input type="radio" name="respuesta1" value="No Aplica" onclick="handleSelection(1, 'No Aplica')"></td>
                    <td><textarea name="observacion1" id="observation-1" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="question-group-2">
                    <td>Cargas encontradas</td>
                    <td><input type="radio" name="respuesta2" value="Conforme" onclick="handleSelection(2, 'Conforme')" required></td>
                    <td><input type="radio" name="respuesta2" value="No Conforme" onclick="handleSelection(2, 'No Conforme')"></td>
                    <td><input type="radio" name="respuesta2" value="No Aplica" onclick="handleSelection(2, 'No Aplica')"></td>
                    <td><textarea name="observacion2" id="observation-2" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="question-group-3">
                    <td>Datos de medidores encontrado e instalado</td>
                    <td><input type="radio" name="respuesta3" value="Conforme" onclick="handleSelection(3, 'Conforme')" required></td>
                    <td><input type="radio" name="respuesta3" value="No Conforme" onclick="handleSelection(3, 'No Conforme')"></td>
                    <td><input type="radio" name="respuesta3" value="No Aplica" onclick="handleSelection(3, 'No Aplica')"></td>
                    <td><textarea name="observacion3" id="observation-3" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="question-group-4">
                    <td>Sellos encontrado e instalados</td>
                    <td><input type="radio" name="respuesta4" value="Conforme" onclick="handleSelection(4, 'Conforme')" required></td>
                    <td><input type="radio" name="respuesta4" value="No Conforme" onclick="handleSelection(4, 'No Conforme')"></td>
                    <td><input type="radio" name="respuesta4" value="No Aplica" onclick="handleSelection(4, 'No Aplica')"></td>
                    <td><textarea name="observacion3" id="observation-4" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="question-group-5">
                    <td>Datos de Telemedida reportada</td>
                    <td><input type="radio" name="respuesta5" value="Conforme" onclick="handleSelection(5, 'Conforme')" required></td>
                    <td><input type="radio" name="respuesta5" value="No Conforme" onclick="handleSelection(5, 'No Conforme')"></td>
                    <td><input type="radio" name="respuesta5" value="No Aplica" onclick="handleSelection(5, 'No Aplica')"></td>
                    <td><textarea name="observacion5" id="observation-5" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="question-group-6">
                    <td>Caracteristica de TC y TT encontrados e instalados</td>
                    <td><input type="radio" name="respuesta6" value="Conforme" onclick="handleSelection(6, 'Conforme')" required></td>
                    <td><input type="radio" name="respuesta6" value="No Conforme" onclick="handleSelection(6, 'No Conforme')"></td>
                    <td><input type="radio" name="respuesta6" value="No Aplica" onclick="handleSelection(6, 'No Aplica')"></td>
                    <td><textarea name="observacion6" id="observation-6" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="question-group-7">
                    <td>Pruebas iniciales tiempo potencia yregistro</td>
                    <td><input type="radio" name="respuesta7" value="Conforme" onclick="handleSelection(7, 'Conforme')" required></td>
                    <td><input type="radio" name="respuesta7" value="No Conforme" onclick="handleSelection(7, 'No Conforme')"></td>
                    <td><input type="radio" name="respuesta7" value="No Aplica" onclick="handleSelection(7, 'No Aplica')"></td>
                    <td><textarea name="observacion3" id="observation-7" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="question-group-8">
                    <td>RTC</td>
                    <td><input type="radio" name="respuesta8" value="Conforme" onclick="handleSelection(8, 'Conforme')" required></td>
                    <td><input type="radio" name="respuesta8" value="No Conforme" onclick="handleSelection(8, 'No Conforme')"></td>
                    <td><input type="radio" name="respuesta8" value="No Aplica" onclick="handleSelection(8, 'No Aplica')"></td>
                    <td><textarea name="observacion8" id="observation-8" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="question-group-9">
                    <td>RTT</td>
                    <td><input type="radio" name="respuesta9" value="Conforme" onclick="handleSelection(9, 'Conforme')" required></td>
                    <td><input type="radio" name="respuesta9" value="No Conforme" onclick="handleSelection(9, 'No Conforme')"></td>
                    <td><input type="radio" name="respuesta9" value="No Aplica" onclick="handleSelection(9, 'No Aplica')"></td>
                    <td><textarea name="observacion9" id="observation-9" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="question-group-10">
                    <td>Pruebas finales tiempo potencia y registro</td>
                    <td><input type="radio" name="respuesta10" value="Conforme" onclick="handleSelection(10, 'Conforme')" required></td>
                    <td><input type="radio" name="respuesta10" value="No Conforme" onclick="handleSelection(10, 'No Conforme')"></td>
                    <td><input type="radio" name="respuesta10" value="No Aplica" onclick="handleSelection(10, 'No Aplica')"></td>
                    <td><textarea name="observacion10" id="observation-10" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="question-group-11">
                    <td>RTC</td>
                    <td><input type="radio" name="respuesta11" value="Conforme" onclick="handleSelection(11, 'Conforme')" required></td>
                    <td><input type="radio" name="respuesta11" value="No Conforme" onclick="handleSelection(11, 'No Conforme')"></td>
                    <td><input type="radio" name="respuesta11" value="No Aplica" onclick="handleSelection(11, 'No Aplica')"></td>
                    <td><textarea name="observacion11" id="observation-11" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="question-group-12">
                    <td>RTT</td>
                    <td><input type="radio" name="respuesta12" value="Conforme" onclick="handleSelection(12, 'Conforme')" required></td>
                    <td><input type="radio" name="respuesta12" value="No Conforme" onclick="handleSelection(12, 'No Conforme')"></td>
                    <td><input type="radio" name="respuesta12" value="No Aplica" onclick="handleSelection(12, 'No Aplica')"></td>
                    <td><textarea name="observacion12" id="observation-12" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="question-group-13">
                    <td>Irregularidades y/o anomalias detectada</td>
                    <td><input type="radio" name="respuesta13" value="Conforme" onclick="handleSelection(13, 'Conforme')" required></td>
                    <td><input type="radio" name="respuesta13" value="No Conforme" onclick="handleSelection(13, 'No Conforme')"></td>
                    <td><input type="radio" name="respuesta13" value="No Aplica" onclick="handleSelection(13, 'No Aplica')"></td>
                    <td><textarea name="observacion13" id="observation-13" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="question-group-14">
                    <td>Mano de obra</td>
                    <td><input type="radio" name="respuesta14" value="Conforme" onclick="handleSelection(14, 'Conforme')" required></td>
                    <td><input type="radio" name="respuesta14" value="No Conforme" onclick="handleSelection(14, 'No Conforme')"></td>
                    <td><input type="radio" name="respuesta14" value="No Aplica" onclick="handleSelection(14, 'No Aplica')"></td>
                    <td><textarea name="observacion14" id="observation-14" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="question-group-15">
                    <td>Materiales instalados</td>
                    <td><input type="radio" name="respuesta15" value="Conforme" onclick="handleSelection(15, 'Conforme')" required></td>
                    <td><input type="radio" name="respuesta15" value="No Conforme" onclick="handleSelection(15, 'No Conforme')"></td>
                    <td><input type="radio" name="respuesta15" value="No Aplica" onclick="handleSelection(15, 'No Aplica')"></td>
                    <td><textarea name="observacion15" id="observation-15" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="question-group-16">
                    <td>Diagrama unifilar y de conexiones iniciales</td>
                    <td><input type="radio" name="respuesta16" value="Conforme" onclick="handleSelection(16, 'Conforme')" required></td>
                    <td><input type="radio" name="respuesta16" value="No Conforme" onclick="handleSelection(16, 'No Conforme')"></td>
                    <td><input type="radio" name="respuesta16" value="No Aplica" onclick="handleSelection(16, 'No Aplica')"></td>
                    <td><textarea name="observacion16" id="observation-16" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="question-group-17">
                    <td>Diagrama unifilar y de conexiones finales</td>
                    <td><input type="radio" name="respuesta17" value="Conforme" onclick="handleSelection(17, 'Conforme')" required></td>
                    <td><input type="radio" name="respuesta17" value="No Conforme" onclick="handleSelection(17, 'No Conforme')"></td>
                    <td><input type="radio" name="respuesta17" value="No Aplica" onclick="handleSelection(17, 'No Aplica')"></td>
                    <td><textarea name="observacion17" id="observation-17" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="question-group-18">
                    <td>Reporte de adecuaciones y mejoras requeridas</td>
                    <td><input type="radio" name="respuesta18" value="Conforme" onclick="handleSelection(18, 'Conforme')" required></td>
                    <td><input type="radio" name="respuesta18" value="No Conforme" onclick="handleSelection(18, 'No Conforme')"></td>
                    <td><input type="radio" name="respuesta18" value="No Aplica" onclick="handleSelection(18, 'No Aplica')"></td>
                    <td><textarea name="observacion18" id="observation-18" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="question-group-19">
                    <td>Observaciones generales aportan al alcance de la O/S</td>
                    <td><input type="radio" name="respuesta19" value="Conforme" onclick="handleSelection(19, 'Conforme')" required></td>
                    <td><input type="radio" name="respuesta19" value="No Conforme" onclick="handleSelection(19, 'No Conforme')"></td>
                    <td><input type="radio" name="respuesta19" value="No Aplica" onclick="handleSelection(19, 'No Aplica')"></td>
                    <td><textarea name="observacion19" id="observation-19" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="question-group-20">
                    <td>Firmas del acta</td>
                    <td><input type="radio" name="respuesta20" value="Conforme" onclick="handleSelection(20, 'Conforme')" required></td>
                    <td><input type="radio" name="respuesta20" value="No Conforme" onclick="handleSelection(20, 'No Conforme')"></td>
                    <td><input type="radio" name="respuesta20" value="No Aplica" onclick="handleSelection(20, 'No Aplica')"></td>
                    <td><textarea name="observacion20" id="observation-20" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>

                </tbody>
        </table>
        <h3>EVALUACIÓN REGISTROS FOTOGRÁFICOS</h3>
        <!-- Tabla de preguntas y respuestas -->
        <table>
            <thead>
                <tr>
                    <th>Pregunta</th>
                    <th>Conforme</th>
                    <th>No Conforme</th>
                    <th>No Aplica</th>
                    <th>Observación</th>
                </tr>
            </thead>
            <tbody>


                <tr id="question-group-4">
                    <td>Panorámica dela fachada delpredio</td>
                    <td><input type="radio" name="respuesta3" value="Conforme" onclick="handleSelection(4, 'Conforme')" required></td>
                    <td><input type="radio" name="respuesta3" value="No Conforme" onclick="handleSelection(4, 'No Conforme')"></td>
                    <td><input type="radio" name="respuesta3" value="No Aplica" onclick="handleSelection(4, 'No Aplica')"></td>
                    <td><textarea name="observacion3" id="observation-4" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="question-group-4">
                    <td>Cargas iniciales por fase</td>
                    <td><input type="radio" name="respuesta3" value="Conforme" onclick="handleSelection(4, 'Conforme')" required></td>
                    <td><input type="radio" name="respuesta3" value="No Conforme" onclick="handleSelection(4, 'No Conforme')"></td>
                    <td><input type="radio" name="respuesta3" value="No Aplica" onclick="handleSelection(4, 'No Aplica')"></td>
                    <td><textarea name="observacion3" id="observation-4" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="question-group-4">
                    <td>Panoramica del tipo de medida</td>
                    <td><input type="radio" name="respuesta3" value="Conforme" onclick="handleSelection(4, 'Conforme')" required></td>
                    <td><input type="radio" name="respuesta3" value="No Conforme" onclick="handleSelection(4, 'No Conforme')"></td>
                    <td><input type="radio" name="respuesta3" value="No Aplica" onclick="handleSelection(4, 'No Aplica')"></td>
                    <td><textarea name="observacion3" id="observation-4" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="question-group-4">
                    <td>Panorámica del medidor en gabinete y sticker de calibracion</td>
                    <td><input type="radio" name="respuesta3" value="Conforme" onclick="handleSelection(4, 'Conforme')" required></td>
                    <td><input type="radio" name="respuesta3" value="No Conforme" onclick="handleSelection(4, 'No Conforme')"></td>
                    <td><input type="radio" name="respuesta3" value="No Aplica" onclick="handleSelection(4, 'No Aplica')"></td>
                    <td><textarea name="observacion3" id="observation-4" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="question-group-4">
                    <td>Datos del modem encontrado</td>
                    <td><input type="radio" name="respuesta3" value="Conforme" onclick="handleSelection(4, 'Conforme')" required></td>
                    <td><input type="radio" name="respuesta3" value="No Conforme" onclick="handleSelection(4, 'No Conforme')"></td>
                    <td><input type="radio" name="respuesta3" value="No Aplica" onclick="handleSelection(4, 'No Aplica')"></td>
                    <td><textarea name="observacion3" id="observation-4" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="question-group-4">
                    <td>Lecturas encontradas</td>
                    <td><input type="radio" name="respuesta3" value="Conforme" onclick="handleSelection(4, 'Conforme')" required></td>
                    <td><input type="radio" name="respuesta3" value="No Conforme" onclick="handleSelection(4, 'No Conforme')"></td>
                    <td><input type="radio" name="respuesta3" value="No Aplica" onclick="handleSelection(4, 'No Aplica')"></td>
                    <td><textarea name="observacion3" id="observation-4" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="question-group-4">
                    <td>Sellos encontrados legibles</td>
                    <td><input type="radio" name="respuesta3" value="Conforme" onclick="handleSelection(4, 'Conforme')" required></td>
                    <td><input type="radio" name="respuesta3" value="No Conforme" onclick="handleSelection(4, 'No Conforme')"></td>
                    <td><input type="radio" name="respuesta3" value="No Aplica" onclick="handleSelection(4, 'No Aplica')"></td>
                    <td><textarea name="observacion3" id="observation-4" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="question-group-4">
                    <td>Conexiones iniciales en bornera de medidor y bloque de prueba</td>
                    <td><input type="radio" name="respuesta3" value="Conforme" onclick="handleSelection(4, 'Conforme')" required></td>
                    <td><input type="radio" name="respuesta3" value="No Conforme" onclick="handleSelection(4, 'No Conforme')"></td>
                    <td><input type="radio" name="respuesta3" value="No Aplica" onclick="handleSelection(4, 'No Aplica')"></td>
                    <td><textarea name="observacion3" id="observation-4" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="question-group-4">
                    <td>Parte trasera del medidor</td>
                    <td><input type="radio" name="respuesta3" value="Conforme" onclick="handleSelection(4, 'Conforme')" required></td>
                    <td><input type="radio" name="respuesta3" value="No Conforme" onclick="handleSelection(4, 'No Conforme')"></td>
                    <td><input type="radio" name="respuesta3" value="No Aplica" onclick="handleSelection(4, 'No Aplica')"></td>
                    <td><textarea name="observacion3" id="observation-4" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="question-group-4">
                    <td>Parte trasera del bloque de pruebas</td>
                    <td><input type="radio" name="respuesta3" value="Conforme" onclick="handleSelection(4, 'Conforme')" required></td>
                    <td><input type="radio" name="respuesta3" value="No Conforme" onclick="handleSelection(4, 'No Conforme')"></td>
                    <td><input type="radio" name="respuesta3" value="No Aplica" onclick="handleSelection(4, 'No Aplica')"></td>
                    <td><textarea name="observacion3" id="observation-4" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="question-group-4">
                    <td>Evidencia de las conexiones en celda de TC porsecundario</td>
                    <td><input type="radio" name="respuesta3" value="Conforme" onclick="handleSelection(4, 'Conforme')" required></td>
                    <td><input type="radio" name="respuesta3" value="No Conforme" onclick="handleSelection(4, 'No Conforme')"></td>
                    <td><input type="radio" name="respuesta3" value="No Aplica" onclick="handleSelection(4, 'No Aplica')"></td>
                    <td><textarea name="observacion3" id="observation-4" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="question-group-4">
                    <td>Evidencia de las conexiones en TT porsecundario</td>
                    <td><input type="radio" name="respuesta3" value="Conforme" onclick="handleSelection(4, 'Conforme')" required></td>
                    <td><input type="radio" name="respuesta3" value="No Conforme" onclick="handleSelection(4, 'No Conforme')"></td>
                    <td><input type="radio" name="respuesta3" value="No Aplica" onclick="handleSelection(4, 'No Aplica')"></td>
                    <td><textarea name="observacion3" id="observation-4" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="question-group-4">
                    <td>Evidencia de las conexiones en TC por primario</td>
                    <td><input type="radio" name="respuesta3" value="Conforme" onclick="handleSelection(4, 'Conforme')" required></td>
                    <td><input type="radio" name="respuesta3" value="No Conforme" onclick="handleSelection(4, 'No Conforme')"></td>
                    <td><input type="radio" name="respuesta3" value="No Aplica" onclick="handleSelection(4, 'No Aplica')"></td>
                    <td><textarea name="observacion3" id="observation-4" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="question-group-4">
                    <td>Evidencia de las pruebas con el equipo patrón</td>
                    <td><input type="radio" name="respuesta3" value="Conforme" onclick="handleSelection(4, 'Conforme')" required></td>
                    <td><input type="radio" name="respuesta3" value="No Conforme" onclick="handleSelection(4, 'No Conforme')"></td>
                    <td><input type="radio" name="respuesta3" value="No Aplica" onclick="handleSelection(4, 'No Aplica')"></td>
                    <td><textarea name="observacion3" id="observation-4" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="question-group-4">
                    <td>Sellos instalados legibles</td>
                    <td><input type="radio" name="respuesta3" value="Conforme" onclick="handleSelection(4, 'Conforme')" required></td>
                    <td><input type="radio" name="respuesta3" value="No Conforme" onclick="handleSelection(4, 'No Conforme')"></td>
                    <td><input type="radio" name="respuesta3" value="No Aplica" onclick="handleSelection(4, 'No Aplica')"></td>
                    <td><textarea name="observacion3" id="observation-4" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="question-group-4">
                    <td>Evidencias de la irregularidad encontrada</td>
                    <td><input type="radio" name="respuesta3" value="Conforme" onclick="handleSelection(4, 'Conforme')" required></td>
                    <td><input type="radio" name="respuesta3" value="No Conforme" onclick="handleSelection(4, 'No Conforme')"></td>
                    <td><input type="radio" name="respuesta3" value="No Aplica" onclick="handleSelection(4, 'No Aplica')"></td>
                    <td><textarea name="observacion3" id="observation-4" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>

                <tr id="question-group-4">
                    <td>Evidencias de tensiones y corrientes de acuerdo al tipo de irregularidad</td>
                    <td><input type="radio" name="respuesta3" value="Conforme" onclick="handleSelection(4, 'Conforme')" required></td>
                    <td><input type="radio" name="respuesta3" value="No Conforme" onclick="handleSelection(4, 'No Conforme')"></td>
                    <td><input type="radio" name="respuesta3" value="No Aplica" onclick="handleSelection(4, 'No Aplica')"></td>
                    <td><textarea name="observacion3" id="observation-4" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="question-group-4">
                    <td>Evidencia de la corrección de conexiones (siaplica)</td>
                    <td><input type="radio" name="respuesta3" value="Conforme" onclick="handleSelection(4, 'Conforme')" required></td>
                    <td><input type="radio" name="respuesta3" value="No Conforme" onclick="handleSelection(4, 'No Conforme')"></td>
                    <td><input type="radio" name="respuesta3" value="No Aplica" onclick="handleSelection(4, 'No Aplica')"></td>
                    <td><textarea name="observacion3" id="observation-4" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="question-group-4">
                    <td>Evidencia de elementos  retirados (siaplica)</td>
                    <td><input type="radio" name="respuesta3" value="Conforme" onclick="handleSelection(4, 'Conforme')" required></td>
                    <td><input type="radio" name="respuesta3" value="No Conforme" onclick="handleSelection(4, 'No Conforme')"></td>
                    <td><input type="radio" name="respuesta3" value="No Aplica" onclick="handleSelection(4, 'No Aplica')"></td>
                    <td><textarea name="observacion3" id="observation-4" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="question-group-4">
                    <td>Zona de trabajo limpia y despejada</td>
                    <td><input type="radio" name="respuesta3" value="Conforme" onclick="handleSelection(4, 'Conforme')" required></td>
                    <td><input type="radio" name="respuesta3" value="No Conforme" onclick="handleSelection(4, 'No Conforme')"></td>
                    <td><input type="radio" name="respuesta3" value="No Aplica" onclick="handleSelection(4, 'No Aplica')"></td>
                    <td><textarea name="observacion3" id="observation-4" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                </tbody>
                </table>

                <h3>VALIDACIÓN NORMALIZACIÓN Y ALCANCE DE LA ACTIVIDAD
                </h3>
        <!-- Tabla de preguntas y respuestas -->
        <table>
            <thead>
                <tr>
                    <th>Pregunta</th>
                    <th>Conforme</th>
                    <th>No conforme</th>
                    <th>No Aplica</th>
                    <th>Observación</th>
                </tr>
            </thead>
            <tbody>

                <tr id="question-group-4">
                    <td>5.¿Se normalizó la irregularidad encontrada?</td>
                    <td><input type="radio" name="respuesta3" value="Conforme" onclick="handleSelection(4, 'Conforme')" required></td>
                    <td><input type="radio" name="respuesta3" value="No Conforme" onclick="handleSelection(4, 'No Conforme')"></td>
                    <td><input type="radio" name="respuesta3" value="No Aplica" onclick="handleSelection(4, 'No Aplica')"></td>
                    <td><textarea name="observacion3" id="observation-4" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="question-group-4">
                    <td>¿Se cumplió con el alcance del tipo de orden de servicio (O/S)?</td>
                    <td><input type="radio" name="respuesta3" value="Conforme" onclick="handleSelection(4, 'Conforme')" required></td>
                    <td><input type="radio" name="respuesta3" value="No Conforme" onclick="handleSelection(4, 'No Conforme')"></td>
                    <td><input type="radio" name="respuesta3" value="No Aplica" onclick="handleSelection(4, 'No Aplica')"></td>
                    <td><textarea name="observacion3" id="observation-4" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="question-group-4">
                    <td>¿Las observaciones generales aportan o justifican el resultado de la visita?</td>
                    <td><input type="radio" name="respuesta3" value="Conforme" onclick="handleSelection(4, 'Conforme')" required></td>
                    <td><input type="radio" name="respuesta3" value="No Conforme" onclick="handleSelection(4, 'No Conforme')"></td>
                    <td><input type="radio" name="respuesta3" value="No Aplica" onclick="handleSelection(4, 'No Aplica')"></td>
                    <td><textarea name="observacion3" id="observation-4" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="question-group-4">
                    <td>Estado final de la revision del acta</td>
                    <td><input type="radio" name="respuesta3" value="Conforme" onclick="handleSelection(4, 'Conforme')" required></td>
                    <td><input type="radio" name="respuesta3" value="No Conforme" onclick="handleSelection(4, 'No Conforme')"></td>
                    <td><input type="radio" name="respuesta3" value="No Aplica" onclick="handleSelection(4, 'No Aplica')"></td>
                    <td><textarea name="observacion3" id="observation-4" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                </tbody>
                </table>

        <button type="submit">Guardar Respuestas</button>
    </form>
</div>

<script>
    function handleSelection(questionId, value) {
        const observation = document.getElementById(`observation-${questionId}`);
        const row = document.getElementById(`question-group-${questionId}`);

        // Restablecer clases de color
        row.classList.remove("conforme", "no-conforme", "no-aplica");

        if (value === "Conforme") {
            observation.style.display = "none";
            row.classList.add("conforme");
        } else if (value === "No Conforme") {
            observation.style.display = "block";
            row.classList.add("no-conforme");
        } else if (value === "No Aplica") {
            observation.style.display = "none";
            row.classList.add("no-aplica");
        }
    }
</script>

</body>
</html>
