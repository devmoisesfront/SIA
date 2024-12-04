<?php
// Configuración de la base de datos
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'ordenes_db';

// Conexión a la base de datos
$conn = new mysqli($host, $user, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die('Error de conexión: ' . $conn->connect_error);
}

// Obtener el número de orden de la URL
$numero_orden = isset($_GET['orden']) ? intval($_GET['orden']) : 0;

// Consultar los datos de la orden específica
$sql = "SELECT Orden, TipoOrden, Territorio FROM ordenes WHERE Orden = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $numero_orden);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $orden_data = $result->fetch_assoc();
    $territorio = $orden_data['Territorio'];
    $tipo_orden = $orden_data['TipoOrden'];
} else {
    // Si no se encuentra la orden, redirigir al index
    header('Location: index.php');
    exit;
}

$stmt->close();
$conn->close();
?>

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

        .alerta-no-conforme {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            margin: 10px 0;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            text-align: center;
            font-weight: bold;
        }

        .progress-bar {
            background-color: #f0f0f0;
            border-radius: 5px;
            padding: 10px;
            margin: 15px 0;
        }

        .progress-stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .progress {
            background-color: #e9ecef;
            height: 20px;
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            width: 0;
            transition: width 0.3s ease, background-color 0.3s ease;
        }

        .tabla-preguntas {
            margin-top: 20px;
        }

        .conforme {
            background-color: #d4edda !important;
        }

        .no-conforme {
            background-color: #f8d7da !important;
        }

        .no-aplica {
            background-color: #ffffff !important;
        }

        .info-orden {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .form-group select {
            background-color: white;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #4CAF50;
            outline: none;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.2);
        }

        .filtro-preguntas {
            background-color: #f8f9fa;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .checkbox-group {
            display: flex;
            gap: 20px;
            margin-top: 10px;
        }

        .checkbox-group label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-weight: bold;
        }

        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
        }

        .hidden {
            display: none !important;
        }

        .alerta-estado {
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
            font-weight: bold;
            text-align: center;
        }

        /* Centrar todos los títulos h3 */
        h3 {
            text-align: center;
            margin: 20px 0;
            color: #333;
            font-size: 1.2em;
            font-weight: bold;
        }

        /* Asegurar que los títulos de sección también estén centrados */
        h3[data-seccion] {
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Auditar Orden</h1>
    
    <!-- Nuevos campos de información -->
    <div class="info-orden">
    <div class="form-group">
        <label for="numero_orden">Número de Orden:</label>
        <input type="text" id="numero_orden" name="numero_orden" value="<?php echo htmlspecialchars($numero_orden); ?>" readonly>
    </div>
    
    <div class="form-group">
        <label for="territorio">Territorio:</label>
        <input type="text" id="territorio" name="territorio" value="<?php echo htmlspecialchars($territorio); ?>" readonly>
    </div>
    
    <div class="form-group">
        <label for="tipo_orden">Tipo de Orden:</label>
        <input type="text" id="tipo_orden" name="tipo_orden" value="<?php echo htmlspecialchars($tipo_orden); ?>" readonly>
    </div>
</div>

<!-- Agregar después de info-orden y antes de las tablas de preguntas -->
<div class="filtro-preguntas">
    <h3>Seleccione los grupos de preguntas a responder:</h3>
    <div class="checkbox-group">
        <label>
            <input type="checkbox" id="mostrar-siprem" checked> SIPREM
        </label>
        <label>
            <input type="checkbox" id="mostrar-fotos" checked> Registros Fotográficos
        </label>
        <label>
            <input type="checkbox" id="mostrar-validacion" checked> Validación Normalización
        </label>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Obtener checkboxes
    const checkboxSiprem = document.getElementById('mostrar-siprem');
    const checkboxFotos = document.getElementById('mostrar-fotos');
    const checkboxValidacion = document.getElementById('mostrar-validacion');

    // Función para mostrar/ocultar secciones
    function toggleSeccion(seccionId, mostrar) {
        // Obtener todos los elementos relacionados con la sección
        const titulo = document.querySelector(`h3[data-seccion="${seccionId}"]`);
        const tabla = document.querySelector(`table[data-grupo="${seccionId}"]`);
        const progressBar = document.querySelector(`.progress-bar[data-grupo="${seccionId}"]`);

        // Mantener título y barra de progreso visibles, solo ocultar la tabla
        if (titulo) {
            titulo.classList.remove('hidden');
        }
        if (progressBar) {
            progressBar.classList.remove('hidden');
        }
        if (tabla) {
            if (mostrar) {
                tabla.classList.remove('hidden');
            } else {
                tabla.classList.add('hidden');
            }
        }
    }

    // Eventos para los checkboxes
    checkboxSiprem.addEventListener('change', function() {
        toggleSeccion('siprem', this.checked);
    });

    checkboxFotos.addEventListener('change', function() {
        toggleSeccion('fotos', this.checked);
    });

    checkboxValidacion.addEventListener('change', function() {
        toggleSeccion('validacion', this.checked);
    });

    // Configuración inicial según el tipo de orden
    const tipoOrden = document.getElementById('tipo_orden').value.toUpperCase();
    switch(tipoOrden) {
        case 'REVISION':
            checkboxSiprem.checked = true;
            checkboxFotos.checked = true;
            checkboxValidacion.checked = true;
            break;
        case 'NORMALIZACION':
            checkboxSiprem.checked = true;
            checkboxFotos.checked = false;
            checkboxValidacion.checked = true;
            break;
        default:
            checkboxSiprem.checked = true;
            checkboxFotos.checked = true;
            checkboxValidacion.checked = true;
    }

    // Aplicar visibilidad inicial
    toggleSeccion('siprem', checkboxSiprem.checked);
    toggleSeccion('fotos', checkboxFotos.checked);
    toggleSeccion('validacion', checkboxValidacion.checked);
});
</script>

    <form method="POST" action="guardar_respuestas.php">
        
        <h3>Diligenciamiento del acta de SIPREM</h3>
        <div class="progress-bar">
            <div class="progress-stats">
                <span>Conforme: <span id="conformes-siprem">0</span>/<span id="total-conformes-siprem">20</span></span>
                <span>No Conforme: <span id="no-conformes-siprem">0</span>/<span id="total-no-conformes-siprem">20</span></span>
                <span>No Aplica: <span id="no-aplica-siprem">0</span>/<span id="total-no-aplica-siprem">20</span></span>
            </div>
            <div class="progress">
                <div id="progress-bar-siprem" class="progress-fill"></div>
            </div>
        </div>
        
        <!-- Primera tabla con clase 'pregunta-grupo-siprem' -->
        <table class="tabla-preguntas" data-grupo="siprem">
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
                <tr id="siprem-1" class="pregunta-row">
                    <td><span class="numero-pregunta"></span>. Información preliminar del cliente</td>
                    <td><input type="radio" name="siprem-1" value="Conforme" onclick="handleSelection('siprem-1', 'Conforme')" required></td>
                    <td><input type="radio" name="siprem-1" value="No Conforme" onclick="handleSelection('siprem-1', 'No Conforme')"></td>
                    <td><input type="radio" name="siprem-1" value="No Aplica" onclick="handleSelection('siprem-1', 'No Aplica')"></td>
                    <td><textarea name="obs-siprem-1" id="observation-siprem-1" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="siprem-2" class="pregunta-row">
                    <td><span class="numero-pregunta"></span>. Cargas encontradas</td>
                    <td><input type="radio" name="siprem-2" value="Conforme" onclick="handleSelection('siprem-2', 'Conforme')" required></td>
                    <td><input type="radio" name="siprem-2" value="No Conforme" onclick="handleSelection('siprem-2', 'No Conforme')"></td>
                    <td><input type="radio" name="siprem-2" value="No Aplica" onclick="handleSelection('siprem-2', 'No Aplica')"></td>
                    <td><textarea name="obs-siprem-2" id="observation-siprem-2" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="siprem-3" class="pregunta-row">
                    <td><span class="numero-pregunta"></span>. Datos de medidores encontrado e instalado</td>
                    <td><input type="radio" name="siprem-3" value="Conforme" onclick="handleSelection('siprem-3', 'Conforme')" required></td>
                    <td><input type="radio" name="siprem-3" value="No Conforme" onclick="handleSelection('siprem-3', 'No Conforme')"></td>
                    <td><input type="radio" name="siprem-3" value="No Aplica" onclick="handleSelection('siprem-3', 'No Aplica')"></td>
                    <td><textarea name="obs-siprem-3" id="observation-siprem-3" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="siprem-4" class="pregunta-row">
                    <td><span class="numero-pregunta"></span>. Sellos encontrado e instalados</td>
                    <td><input type="radio" name="siprem-4" value="Conforme" onclick="handleSelection('siprem-4', 'Conforme')" required></td>
                    <td><input type="radio" name="siprem-4" value="No Conforme" onclick="handleSelection('siprem-4', 'No Conforme')"></td>
                    <td><input type="radio" name="siprem-4" value="No Aplica" onclick="handleSelection('siprem-4', 'No Aplica')"></td>
                    <td><textarea name="obs-siprem-4" id="observation-siprem-4" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="siprem-5" class="pregunta-row">
                    <td><span class="numero-pregunta"></span>. Datos de Telemedida reportada</td>
                    <td><input type="radio" name="siprem-5" value="Conforme" onclick="handleSelection('siprem-5', 'Conforme')" required></td>
                    <td><input type="radio" name="siprem-5" value="No Conforme" onclick="handleSelection('siprem-5', 'No Conforme')"></td>
                    <td><input type="radio" name="siprem-5" value="No Aplica" onclick="handleSelection('siprem-5', 'No Aplica')"></td>
                    <td><textarea name="obs-siprem-5" id="observation-siprem-5" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="siprem-6" class="pregunta-row">
                    <td><span class="numero-pregunta"></span>. Caracteristica de TC y TT encontrados e instalados</td>
                    <td><input type="radio" name="siprem-6" value="Conforme" onclick="handleSelection('siprem-6', 'Conforme')" required></td>
                    <td><input type="radio" name="siprem-6" value="No Conforme" onclick="handleSelection('siprem-6', 'No Conforme')"></td>
                    <td><input type="radio" name="siprem-6" value="No Aplica" onclick="handleSelection('siprem-6', 'No Aplica')"></td>
                    <td><textarea name="obs-siprem-6" id="observation-siprem-6" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="siprem-7" class="pregunta-row">
                    <td><span class="numero-pregunta"></span>. Pruebas iniciales tiempo potencia yregistro</td>
                    <td><input type="radio" name="siprem-7" value="Conforme" onclick="handleSelection('siprem-7', 'Conforme')" required></td>
                    <td><input type="radio" name="siprem-7" value="No Conforme" onclick="handleSelection('siprem-7', 'No Conforme')"></td>
                    <td><input type="radio" name="siprem-7" value="No Aplica" onclick="handleSelection('siprem-7', 'No Aplica')"></td>
                    <td><textarea name="obs-siprem-7" id="observation-siprem-7" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="siprem-8" class="pregunta-row">
                    <td><span class="numero-pregunta"></span>. RTC</td>
                    <td><input type="radio" name="siprem-8" value="Conforme" onclick="handleSelection('siprem-8', 'Conforme')" required></td>
                    <td><input type="radio" name="siprem-8" value="No Conforme" onclick="handleSelection('siprem-8', 'No Conforme')"></td>
                    <td><input type="radio" name="siprem-8" value="No Aplica" onclick="handleSelection('siprem-8', 'No Aplica')"></td>
                    <td><textarea name="obs-siprem-8" id="observation-siprem-8" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="siprem-9" class="pregunta-row">
                    <td><span class="numero-pregunta"></span>. RTT</td>
                    <td><input type="radio" name="siprem-9" value="Conforme" onclick="handleSelection('siprem-9', 'Conforme')" required></td>
                    <td><input type="radio" name="siprem-9" value="No Conforme" onclick="handleSelection('siprem-9', 'No Conforme')"></td>
                    <td><input type="radio" name="siprem-9" value="No Aplica" onclick="handleSelection('siprem-9', 'No Aplica')"></td>
                    <td><textarea name="obs-siprem-9" id="observation-siprem-9" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="siprem-10" class="pregunta-row">
                    <td><span class="numero-pregunta"></span>. Pruebas finales tiempo potencia y registro</td>
                    <td><input type="radio" name="siprem-10" value="Conforme" onclick="handleSelection('siprem-10', 'Conforme')" required></td>
                    <td><input type="radio" name="siprem-10" value="No Conforme" onclick="handleSelection('siprem-10', 'No Conforme')"></td>
                    <td><input type="radio" name="siprem-10" value="No Aplica" onclick="handleSelection('siprem-10', 'No Aplica')"></td>
                    <td><textarea name="obs-siprem-10" id="observation-siprem-10" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="siprem-11" class="pregunta-row">
                    <td><span class="numero-pregunta"></span>. RTC</td>
                    <td><input type="radio" name="siprem-11" value="Conforme" onclick="handleSelection('siprem-11', 'Conforme')" required></td>
                    <td><input type="radio" name="siprem-11" value="No Conforme" onclick="handleSelection('siprem-11', 'No Conforme')"></td>
                    <td><input type="radio" name="siprem-11" value="No Aplica" onclick="handleSelection('siprem-11', 'No Aplica')"></td>
                    <td><textarea name="obs-siprem-11" id="observation-siprem-11" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="siprem-12" class="pregunta-row">
                    <td><span class="numero-pregunta"></span>. RTT</td>
                    <td><input type="radio" name="siprem-12" value="Conforme" onclick="handleSelection('siprem-12', 'Conforme')" required></td>
                    <td><input type="radio" name="siprem-12" value="No Conforme" onclick="handleSelection('siprem-12', 'No Conforme')"></td>
                    <td><input type="radio" name="siprem-12" value="No Aplica" onclick="handleSelection('siprem-12', 'No Aplica')"></td>
                    <td><textarea name="obs-siprem-12" id="observation-siprem-12" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="siprem-13" class="pregunta-row">
                    <td><span class="numero-pregunta"></span>. Irregularidades y/o anomalias detectada</td>
                    <td><input type="radio" name="siprem-13" value="Conforme" onclick="handleSelection('siprem-13', 'Conforme')" required></td>
                    <td><input type="radio" name="siprem-13" value="No Conforme" onclick="handleSelection('siprem-13', 'No Conforme')"></td>
                    <td><input type="radio" name="siprem-13" value="No Aplica" onclick="handleSelection('siprem-13', 'No Aplica')"></td>
                    <td><textarea name="obs-siprem-13" id="observation-siprem-13" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="siprem-14" class="pregunta-row">
                    <td><span class="numero-pregunta"></span>. Mano de obra</td>
                    <td><input type="radio" name="siprem-14" value="Conforme" onclick="handleSelection('siprem-14', 'Conforme')" required></td>
                    <td><input type="radio" name="siprem-14" value="No Conforme" onclick="handleSelection('siprem-14', 'No Conforme')"></td>
                    <td><input type="radio" name="siprem-14" value="No Aplica" onclick="handleSelection('siprem-14', 'No Aplica')"></td>
                    <td><textarea name="obs-siprem-14" id="observation-siprem-14" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="siprem-15" class="pregunta-row">
                    <td><span class="numero-pregunta"></span>. Materiales instalados</td>
                    <td><input type="radio" name="siprem-15" value="Conforme" onclick="handleSelection('siprem-15', 'Conforme')" required></td>
                    <td><input type="radio" name="siprem-15" value="No Conforme" onclick="handleSelection('siprem-15', 'No Conforme')"></td>
                    <td><input type="radio" name="siprem-15" value="No Aplica" onclick="handleSelection('siprem-15', 'No Aplica')"></td>
                    <td><textarea name="obs-siprem-15" id="observation-siprem-15" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="siprem-16" class="pregunta-row">
                    <td><span class="numero-pregunta"></span>. Diagrama unifilar y de conexiones iniciales</td>
                    <td><input type="radio" name="siprem-16" value="Conforme" onclick="handleSelection('siprem-16', 'Conforme')" required></td>
                    <td><input type="radio" name="siprem-16" value="No Conforme" onclick="handleSelection('siprem-16', 'No Conforme')"></td>
                    <td><input type="radio" name="siprem-16" value="No Aplica" onclick="handleSelection('siprem-16', 'No Aplica')"></td>
                    <td><textarea name="obs-siprem-16" id="observation-siprem-16" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="siprem-17" class="pregunta-row">
                    <td><span class="numero-pregunta"></span>. Diagrama unifilar y de conexiones finales</td>
                    <td><input type="radio" name="siprem-17" value="Conforme" onclick="handleSelection('siprem-17', 'Conforme')" required></td>
                    <td><input type="radio" name="siprem-17" value="No Conforme" onclick="handleSelection('siprem-17', 'No Conforme')"></td>
                    <td><input type="radio" name="siprem-17" value="No Aplica" onclick="handleSelection('siprem-17', 'No Aplica')"></td>
                    <td><textarea name="obs-siprem-17" id="observation-siprem-17" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="siprem-18" class="pregunta-row">
                    <td><span class="numero-pregunta"></span>. Reporte de adecuaciones y mejoras requeridas</td>
                    <td><input type="radio" name="siprem-18" value="Conforme" onclick="handleSelection('siprem-18', 'Conforme')" required></td>
                    <td><input type="radio" name="siprem-18" value="No Conforme" onclick="handleSelection('siprem-18', 'No Conforme')"></td>
                    <td><input type="radio" name="siprem-18" value="No Aplica" onclick="handleSelection('siprem-18', 'No Aplica')"></td>
                    <td><textarea name="obs-siprem-18" id="observation-siprem-18" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="siprem-19" class="pregunta-row">
                    <td><span class="numero-pregunta"></span>. Observaciones generales aportan al alcance de la O/S</td>
                    <td><input type="radio" name="siprem-19" value="Conforme" onclick="handleSelection('siprem-19', 'Conforme')" required></td>
                    <td><input type="radio" name="siprem-19" value="No Conforme" onclick="handleSelection('siprem-19', 'No Conforme')"></td>
                    <td><input type="radio" name="siprem-19" value="No Aplica" onclick="handleSelection('siprem-19', 'No Aplica')"></td>
                    <td><textarea name="obs-siprem-19" id="observation-siprem-19" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="siprem-20" class="pregunta-row">
                    <td><span class="numero-pregunta"></span>. Firmas del acta</td>
                    <td><input type="radio" name="siprem-20" value="Conforme" onclick="handleSelection('siprem-20', 'Conforme')" required></td>
                    <td><input type="radio" name="siprem-20" value="No Conforme" onclick="handleSelection('siprem-20', 'No Conforme')"></td>
                    <td><input type="radio" name="siprem-20" value="No Aplica" onclick="handleSelection('siprem-20', 'No Aplica')"></td>
                    <td><textarea name="obs-siprem-20" id="observation-siprem-20" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
            </tbody>
        </table>

        <h3>EVALUACIÓN REGISTROS FOTOGRÁFICOS</h3>
        <div class="progress-bar">
            <div class="progress-stats">
                <span>Conforme: <span id="conformes-fotos">0</span>/<span id="total-conformes-fotos">20</span></span>
                <span>No Conforme: <span id="no-conformes-fotos">0</span>/<span id="total-no-conformes-fotos">20</span></span>
                <span>No Aplica: <span id="no-aplica-fotos">0</span>/<span id="total-no-aplica-fotos">20</span></span>
            </div>
            <div class="progress">
                <div id="progress-bar-fotos" class="progress-fill"></div>
            </div>
        </div>

        <!-- Segunda tabla con clase 'pregunta-grupo-fotos' -->
        <table class="tabla-preguntas" data-grupo="fotos">
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
                <tr id="fotos-1" class="pregunta-row">
                    <td><span class="numero-pregunta"></span>. Panorámica dela fachada delpredio</td>
                    <td><input type="radio" name="fotos-1" value="Conforme" onclick="handleSelection('fotos-1', 'Conforme')" required></td>
                    <td><input type="radio" name="fotos-1" value="No Conforme" onclick="handleSelection('fotos-1', 'No Conforme')"></td>
                    <td><input type="radio" name="fotos-1" value="No Aplica" onclick="handleSelection('fotos-1', 'No Aplica')"></td>
                    <td><textarea name="obs-fotos-1" id="observation-fotos-1" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="fotos-2" class="pregunta-row">
                    <td><span class="numero-pregunta"></span>. Cargas iniciales por fase</td>
                    <td><input type="radio" name="fotos-2" value="Conforme" onclick="handleSelection('fotos-2', 'Conforme')" required></td>
                    <td><input type="radio" name="fotos-2" value="No Conforme" onclick="handleSelection('fotos-2', 'No Conforme')"></td>
                    <td><input type="radio" name="fotos-2" value="No Aplica" onclick="handleSelection('fotos-2', 'No Aplica')"></td>
                    <td><textarea name="obs-fotos-2" id="observation-fotos-2" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="fotos-3" class="pregunta-row">
                    <td><span class="numero-pregunta"></span>. Panoramica del tipo de medida</td>
                    <td><input type="radio" name="fotos-3" value="Conforme" onclick="handleSelection('fotos-3', 'Conforme')" required></td>
                    <td><input type="radio" name="fotos-3" value="No Conforme" onclick="handleSelection('fotos-3', 'No Conforme')"></td>
                    <td><input type="radio" name="fotos-3" value="No Aplica" onclick="handleSelection('fotos-3', 'No Aplica')"></td>
                    <td><textarea name="obs-fotos-3" id="observation-fotos-3" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="fotos-4" class="pregunta-row">
                    <td><span class="numero-pregunta"></span>. Panorámica del medidor en gabinete y sticker de calibracion</td>
                    <td><input type="radio" name="fotos-4" value="Conforme" onclick="handleSelection('fotos-4', 'Conforme')" required></td>
                    <td><input type="radio" name="fotos-4" value="No Conforme" onclick="handleSelection('fotos-4', 'No Conforme')"></td>
                    <td><input type="radio" name="fotos-4" value="No Aplica" onclick="handleSelection('fotos-4', 'No Aplica')"></td>
                    <td><textarea name="obs-fotos-4" id="observation-fotos-4" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="fotos-5" class="pregunta-row">
                    <td><span class="numero-pregunta"></span>. Datos del modem encontrado</td>
                    <td><input type="radio" name="fotos-5" value="Conforme" onclick="handleSelection('fotos-5', 'Conforme')" required></td>
                    <td><input type="radio" name="fotos-5" value="No Conforme" onclick="handleSelection('fotos-5', 'No Conforme')"></td>
                    <td><input type="radio" name="fotos-5" value="No Aplica" onclick="handleSelection('fotos-5', 'No Aplica')"></td>
                    <td><textarea name="obs-fotos-5" id="observation-fotos-5" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="fotos-6" class="pregunta-row">
                    <td><span class="numero-pregunta"></span>. Lecturas encontradas</td>
                    <td><input type="radio" name="fotos-6" value="Conforme" onclick="handleSelection('fotos-6', 'Conforme')" required></td>
                    <td><input type="radio" name="fotos-6" value="No Conforme" onclick="handleSelection('fotos-6', 'No Conforme')"></td>
                    <td><input type="radio" name="fotos-6" value="No Aplica" onclick="handleSelection('fotos-6', 'No Aplica')"></td>
                    <td><textarea name="obs-fotos-6" id="observation-fotos-6" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="fotos-7" class="pregunta-row">
                    <td><span class="numero-pregunta"></span>. Sellos encontrados legibles</td>
                    <td><input type="radio" name="fotos-7" value="Conforme" onclick="handleSelection('fotos-7', 'Conforme')" required></td>
                    <td><input type="radio" name="fotos-7" value="No Conforme" onclick="handleSelection('fotos-7', 'No Conforme')"></td>
                    <td><input type="radio" name="fotos-7" value="No Aplica" onclick="handleSelection('fotos-7', 'No Aplica')"></td>
                    <td><textarea name="obs-fotos-7" id="observation-fotos-7" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="fotos-8" class="pregunta-row">
                    <td><span class="numero-pregunta"></span>. Conexiones iniciales en bornera de medidor y bloque de prueba</td>
                    <td><input type="radio" name="fotos-8" value="Conforme" onclick="handleSelection('fotos-8', 'Conforme')" required></td>
                    <td><input type="radio" name="fotos-8" value="No Conforme" onclick="handleSelection('fotos-8', 'No Conforme')"></td>
                    <td><input type="radio" name="fotos-8" value="No Aplica" onclick="handleSelection('fotos-8', 'No Aplica')"></td>
                    <td><textarea name="obs-fotos-8" id="observation-fotos-8" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="fotos-9" class="pregunta-row">
                    <td><span class="numero-pregunta"></span>. Parte trasera del medidor</td>
                    <td><input type="radio" name="fotos-9" value="Conforme" onclick="handleSelection('fotos-9', 'Conforme')" required></td>
                    <td><input type="radio" name="fotos-9" value="No Conforme" onclick="handleSelection('fotos-9', 'No Conforme')"></td>
                    <td><input type="radio" name="fotos-9" value="No Aplica" onclick="handleSelection('fotos-9', 'No Aplica')"></td>
                    <td><textarea name="obs-fotos-9" id="observation-fotos-9" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="fotos-10" class="pregunta-row">
                    <td><span class="numero-pregunta"></span>. Parte trasera del bloque de pruebas</td>
                    <td><input type="radio" name="fotos-10" value="Conforme" onclick="handleSelection('fotos-10', 'Conforme')" required></td>
                    <td><input type="radio" name="fotos-10" value="No Conforme" onclick="handleSelection('fotos-10', 'No Conforme')"></td>
                    <td><input type="radio" name="fotos-10" value="No Aplica" onclick="handleSelection('fotos-10', 'No Aplica')"></td>
                    <td><textarea name="obs-fotos-10" id="observation-fotos-10" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="fotos-11" class="pregunta-row">
                    <td><span class="numero-pregunta"></span>. Evidencia de las conexiones en celda de TC porsecundario</td>
                    <td><input type="radio" name="fotos-11" value="Conforme" onclick="handleSelection('fotos-11', 'Conforme')" required></td>
                    <td><input type="radio" name="fotos-11" value="No Conforme" onclick="handleSelection('fotos-11', 'No Conforme')"></td>
                    <td><input type="radio" name="fotos-11" value="No Aplica" onclick="handleSelection('fotos-11', 'No Aplica')"></td>
                    <td><textarea name="obs-fotos-11" id="observation-fotos-11" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="fotos-12" class="pregunta-row">
                    <td><span class="numero-pregunta"></span>. Evidencia de las conexiones en TT porsecundario</td>
                    <td><input type="radio" name="fotos-12" value="Conforme" onclick="handleSelection('fotos-12', 'Conforme')" required></td>
                    <td><input type="radio" name="fotos-12" value="No Conforme" onclick="handleSelection('fotos-12', 'No Conforme')"></td>
                    <td><input type="radio" name="fotos-12" value="No Aplica" onclick="handleSelection('fotos-12', 'No Aplica')"></td>
                    <td><textarea name="obs-fotos-12" id="observation-fotos-12" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="fotos-13" class="pregunta-row">
                    <td><span class="numero-pregunta"></span>. Evidencia de las conexiones en TC por primario</td>
                    <td><input type="radio" name="fotos-13" value="Conforme" onclick="handleSelection('fotos-13', 'Conforme')" required></td>
                    <td><input type="radio" name="fotos-13" value="No Conforme" onclick="handleSelection('fotos-13', 'No Conforme')"></td>
                    <td><input type="radio" name="fotos-13" value="No Aplica" onclick="handleSelection('fotos-13', 'No Aplica')"></td>
                    <td><textarea name="obs-fotos-13" id="observation-fotos-13" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="fotos-14" class="pregunta-row">
                    <td><span class="numero-pregunta"></span>. Evidencia de las pruebas con el equipo patrón</td>
                    <td><input type="radio" name="fotos-14" value="Conforme" onclick="handleSelection('fotos-14', 'Conforme')" required></td>
                    <td><input type="radio" name="fotos-14" value="No Conforme" onclick="handleSelection('fotos-14', 'No Conforme')"></td>
                    <td><input type="radio" name="fotos-14" value="No Aplica" onclick="handleSelection('fotos-14', 'No Aplica')"></td>
                    <td><textarea name="obs-fotos-14" id="observation-fotos-14" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="fotos-15" class="pregunta-row">
                    <td><span class="numero-pregunta"></span>. Sellos instalados legibles</td>
                    <td><input type="radio" name="fotos-15" value="Conforme" onclick="handleSelection('fotos-15', 'Conforme')" required></td>
                    <td><input type="radio" name="fotos-15" value="No Conforme" onclick="handleSelection('fotos-15', 'No Conforme')"></td>
                    <td><input type="radio" name="fotos-15" value="No Aplica" onclick="handleSelection('fotos-15', 'No Aplica')"></td>
                    <td><textarea name="obs-fotos-15" id="observation-fotos-15" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="fotos-16" class="pregunta-row">
                    <td><span class="numero-pregunta"></span>. Evidencias de la irregularidad encontrada</td>
                    <td><input type="radio" name="fotos-16" value="Conforme" onclick="handleSelection('fotos-16', 'Conforme')" required></td>
                    <td><input type="radio" name="fotos-16" value="No Conforme" onclick="handleSelection('fotos-16', 'No Conforme')"></td>
                    <td><input type="radio" name="fotos-16" value="No Aplica" onclick="handleSelection('fotos-16', 'No Aplica')"></td>
                    <td><textarea name="obs-fotos-16" id="observation-fotos-16" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="fotos-17" class="pregunta-row">
                    <td><span class="numero-pregunta"></span>. Evidencias de tensiones y corrientes de acuerdo al tipo de irregularidad</td>
                    <td><input type="radio" name="fotos-17" value="Conforme" onclick="handleSelection('fotos-17', 'Conforme')" required></td>
                    <td><input type="radio" name="fotos-17" value="No Conforme" onclick="handleSelection('fotos-17', 'No Conforme')"></td>
                    <td><input type="radio" name="fotos-17" value="No Aplica" onclick="handleSelection('fotos-17', 'No Aplica')"></td>
                    <td><textarea name="obs-fotos-17" id="observation-fotos-17" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="fotos-18" class="pregunta-row">
                    <td><span class="numero-pregunta"></span>. Evidencia de la corrección de conexiones (siaplica)</td>
                    <td><input type="radio" name="fotos-18" value="Conforme" onclick="handleSelection('fotos-18', 'Conforme')" required></td>
                    <td><input type="radio" name="fotos-18" value="No Conforme" onclick="handleSelection('fotos-18', 'No Conforme')"></td>
                    <td><input type="radio" name="fotos-18" value="No Aplica" onclick="handleSelection('fotos-18', 'No Aplica')"></td>
                    <td><textarea name="obs-fotos-18" id="observation-fotos-18" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="fotos-19" class="pregunta-row">
                    <td><span class="numero-pregunta"></span>. Evidencia de elementos  retirados (siaplica)</td>
                    <td><input type="radio" name="fotos-19" value="Conforme" onclick="handleSelection('fotos-19', 'Conforme')" required></td>
                    <td><input type="radio" name="fotos-19" value="No Conforme" onclick="handleSelection('fotos-19', 'No Conforme')"></td>
                    <td><input type="radio" name="fotos-19" value="No Aplica" onclick="handleSelection('fotos-19', 'No Aplica')"></td>
                    <td><textarea name="obs-fotos-19" id="observation-fotos-19" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="fotos-20" class="pregunta-row">
                    <td><span class="numero-pregunta"></span>. Zona de trabajo limpia y despejada</td>
                    <td><input type="radio" name="fotos-20" value="Conforme" onclick="handleSelection('fotos-20', 'Conforme')" required></td>
                    <td><input type="radio" name="fotos-20" value="No Conforme" onclick="handleSelection('fotos-20', 'No Conforme')"></td>
                    <td><input type="radio" name="fotos-20" value="No Aplica" onclick="handleSelection('fotos-20', 'No Aplica')"></td>
                    <td><textarea name="obs-fotos-20" id="observation-fotos-20" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
            </tbody>
        </table>

        <h3 data-seccion="validacion">VALIDACIÓN NORMALIZACIÓN Y ALCANCE DE LA ACTIVIDAD</h3>
        <div class="progress-bar" data-grupo="validacion">
            <div class="progress-stats">
                <span>Conforme: <span id="conformes-validacion">0</span>/<span id="total-conformes-validacion">4</span></span>
                <span>No Conforme: <span id="no-conformes-validacion">0</span>/<span id="total-no-conformes-validacion">4</span></span>
                <span>No Aplica: <span id="no-aplica-validacion">0</span>/<span id="total-no-aplica-validacion">4</span></span>
            </div>
            <div class="progress">
                <div id="progress-bar-validacion" class="progress-fill"></div>
            </div>
        </div>

        <table class="tabla-preguntas" data-grupo="validacion">
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
                <tr id="validacion-1" class="pregunta-row">
                    <td><span class="numero-pregunta"></span>. ¿Se normalizó la irregularidad encontrada?</td>
                    <td><input type="radio" name="validacion-1" value="Conforme" onclick="handleSelection('validacion-1', 'Conforme')" required></td>
                    <td><input type="radio" name="validacion-1" value="No Conforme" onclick="handleSelection('validacion-1', 'No Conforme')"></td>
                    <td><input type="radio" name="validacion-1" value="No Aplica" onclick="handleSelection('validacion-1', 'No Aplica')"></td>
                    <td><textarea name="obs-validacion-1" id="observation-validacion-1" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="validacion-2" class="pregunta-row">
                    <td><span class="numero-pregunta"></span>. ¿Se cumplió con el alcance del tipo de orden de servicio (O/S)?</td>
                    <td><input type="radio" name="validacion-2" value="Conforme" onclick="handleSelection('validacion-2', 'Conforme')" required></td>
                    <td><input type="radio" name="validacion-2" value="No Conforme" onclick="handleSelection('validacion-2', 'No Conforme')"></td>
                    <td><input type="radio" name="validacion-2" value="No Aplica" onclick="handleSelection('validacion-2', 'No Aplica')"></td>
                    <td><textarea name="obs-validacion-2" id="observation-validacion-2" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="validacion-3" class="pregunta-row">
                    <td><span class="numero-pregunta"></span>. ¿Las observaciones generales aportan o justifican el resultado de la visita?</td>
                    <td><input type="radio" name="validacion-3" value="Conforme" onclick="handleSelection('validacion-3', 'Conforme')" required></td>
                    <td><input type="radio" name="validacion-3" value="No Conforme" onclick="handleSelection('validacion-3', 'No Conforme')"></td>
                    <td><input type="radio" name="validacion-3" value="No Aplica" onclick="handleSelection('validacion-3', 'No Aplica')"></td>
                    <td><textarea name="obs-validacion-3" id="observation-validacion-3" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                <tr id="validacion-4" class="pregunta-row">
                    <td><span class="numero-pregunta"></span>. Estado final de la revisión del acta</td>
                    <td><input type="radio" name="validacion-4" value="Conforme" onclick="handleSelection('validacion-4', 'Conforme')" required></td>
                    <td><input type="radio" name="validacion-4" value="No Conforme" onclick="handleSelection('validacion-4', 'No Conforme')"></td>
                    <td><input type="radio" name="validacion-4" value="No Aplica" onclick="handleSelection('validacion-4', 'No Aplica')"></td>
                    <td><textarea name="obs-validacion-4" id="observation-validacion-4" placeholder="Escribe tus observaciones"></textarea></td>
                </tr>
                </tbody>
                </table>

        <button type="submit">Guardar Respuestas</button>
    </form>
</div>

<script>
function calcularMitadMasUno(total) {
    return Math.ceil(total / 2) + 1;
}

function handleSelection(id, value) {
    const observation = document.getElementById(`observation-${id}`);
    const row = document.getElementById(id);
    const grupo = row.closest('table').dataset.grupo;

    // Limpiar clases anteriores
    row.classList.remove("conforme", "no-conforme", "no-aplica");

    // Aplicar nueva clase según selección
    switch(value) {
        case "Conforme":
            observation.style.display = "none";
            row.classList.add("conforme");
            break;
        case "No Conforme":
            observation.style.display = "block";
            row.classList.add("no-conforme");
            break;
        case "No Aplica":
            observation.style.display = "none";
            row.classList.add("no-aplica");
            break;
    }

    // Actualizar progreso inmediatamente
    actualizarProgreso(grupo);
}

function actualizarProgreso(grupo) {
    const tabla = document.querySelector(`table[data-grupo="${grupo}"]`);
    const filas = tabla.querySelectorAll('.pregunta-row');
    let conformes = 0;
    let noConformes = 0;
    let noAplica = 0;
    let total = filas.length;

    filas.forEach(fila => {
        const radios = fila.querySelectorAll('input[type="radio"]');
        radios.forEach(radio => {
            if (radio.checked) {
                if (radio.value === 'Conforme') conformes++;
                if (radio.value === 'No Conforme') noConformes++;
                if (radio.value === 'No Aplica') noAplica++;
            }
        });
    });

    // Actualizar contadores
    document.getElementById(`conformes-${grupo}`).textContent = conformes;
    document.getElementById(`no-conformes-${grupo}`).textContent = noConformes;
    document.getElementById(`no-aplica-${grupo}`).textContent = noAplica;
    document.getElementById(`total-conformes-${grupo}`).textContent = total;
    document.getElementById(`total-no-conformes-${grupo}`).textContent = total;
    document.getElementById(`total-no-aplica-${grupo}`).textContent = total;

    // Actualizar barra de progreso
    const respondidas = conformes + noConformes + noAplica;
    const porcentaje = (respondidas / total) * 100;
    const progressBar = document.getElementById(`progress-bar-${grupo}`);
    progressBar.style.width = `${porcentaje}%`;

    // Ajustar criterios específicos para la sección de validación
    if (grupo === 'validacion') {
        if (noConformes >= 2) { // Ajustado para 4 preguntas
            progressBar.style.backgroundColor = '#dc3545'; // Rojo
            mostrarAlerta(grupo, 'No Conforme', noConformes);
        } else if (noConformes === 1) {
            progressBar.style.backgroundColor = '#ffc107'; // Amarillo
            mostrarAlerta(grupo, 'Parcialmente No Conforme', noConformes);
        } else {
            progressBar.style.backgroundColor = '#4CAF50'; // Verde
            ocultarAlerta(grupo);
        }
    } else {
        // Mantener la lógica original para las otras secciones
        if (noConformes >= 11) {
            progressBar.style.backgroundColor = '#dc3545';
            mostrarAlerta(grupo, 'No Conforme', noConformes);
        } else if (noConformes >= 7 && noConformes <= 10) {
            progressBar.style.backgroundColor = '#ffc107';
            mostrarAlerta(grupo, 'Parcialmente No Conforme', noConformes);
        } else {
            progressBar.style.backgroundColor = '#4CAF50';
            ocultarAlerta(grupo);
        }
    }

    // Actualizar el estado en localStorage
    autoSaveToLocalStorage();
}

function mostrarAlerta(grupo, estado, noConformes) {
    const alertaId = `alerta-${grupo}`;
    let alerta = document.getElementById(alertaId);
    
    if (!alerta) {
        alerta = document.createElement('div');
        alerta.id = alertaId;
        alerta.className = 'alerta-estado';
        const tabla = document.querySelector(`[data-grupo="${grupo}"]`);
        tabla.parentNode.insertBefore(alerta, tabla);
    }

    alerta.innerHTML = `
        <strong>¡Atención!</strong><br>
        Esta sección está ${estado} (${noConformes} no conformidades)
    `;

    // Aplicar estilo según estado
    alerta.style.backgroundColor = estado === 'No Conforme' ? '#ffebee' : '#fff3cd';
    alerta.style.color = estado === 'No Conforme' ? '#c62828' : '#856404';
}

function ocultarAlerta(grupo) {
    const alerta = document.getElementById(`alerta-${grupo}`);
    if (alerta) alerta.remove();
}

// Asegurarse de que las tablas tengan el atributo data-grupo
document.addEventListener('DOMContentLoaded', () => {
    // Inicializar barras de progreso
    const grupos = ['siprem', 'fotos'];
    
    grupos.forEach(grupo => {
        const tabla = document.querySelector(`table[data-grupo="${grupo}"]`);
        if (tabla) {
            const barraProgreso = crearBarraProgreso(grupo);
            tabla.insertAdjacentHTML('beforebegin', barraProgreso);
            actualizarProgreso(grupo);
        }
    });

    // Cargar datos guardados si existen
    loadFromLocalStorage();
});

// Asegurarse de que los radio buttons tengan el evento onclick
document.querySelectorAll('input[type="radio"]').forEach(radio => {
    radio.onclick = function() {
        const id = this.name;
        handleSelection(id, this.value);
    };
});
</script>

<style>
.alerta-estado {
    padding: 15px;
    margin: 10px 0;
    border-radius: 4px;
    font-weight: bold;
    text-align: center;
}
</style>

</body>
</html>
