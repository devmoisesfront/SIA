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

$error = '';
$success = '';
$orden_data = null;

// Obtener el número de orden de la URL
$numero_orden = isset($_GET['orden']) ? intval($_GET['orden']) : 0;

// Si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Iniciar transacción
        $conn->begin_transaction();

        // Recoger y procesar datos
        $siprem_data = [];
        $fotos_data = [];
        $validacion_data = [];
        
        // Procesar datos de SIPREM
        for ($i = 1; $i <= 20; $i++) {
            $siprem_data["siprem-$i"] = [
                'respuesta' => $_POST["siprem-$i"] ?? '',
                'observacion' => $_POST["obs-siprem-$i"] ?? ''
            ];
        }

    // Procesar datos de FOTOS
    for ($i = 1; $i <= 20; $i++) {
        $fotos_data["fotos-$i"] = [
            'respuesta' => $_POST["fotos-$i"] ?? '',
            'observacion' => $_POST["obs-fotos-$i"] ?? ''
        ];
    }

    // Procesar datos de VALIDACIÓN
    for ($i = 1; $i <= 4; $i++) {
        $validacion_data["validacion-$i"] = [
            'respuesta' => $_POST["validacion-$i"] ?? '',
            'observacion' => $_POST["obs-validacion-$i"] ?? ''
        ];
    }

        // Convertir a JSON
        $siprem_json = json_encode($siprem_data);
        $fotos_json = json_encode($fotos_data);
        $validacion_json = json_encode($validacion_data);
        
        // Preparar y ejecutar la actualización
        $sql_update = "UPDATE ordenes_auditadas 
                      SET siprem_data = ?,
                          fotos_data = ?,
                          validacion_data = ?,
                          fecha_modificacion = NOW()
                      WHERE Orden = ?";
                      
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("sssi", $siprem_json, $fotos_json, $validacion_json, $numero_orden);
        $stmt->execute();

        // Confirmar transacción
        $conn->commit();
        
        // Guardar mensaje de éxito para mostrar en el popup
        $_SESSION['mensaje_exito'] = true;
        
        // Redireccionar para evitar reenvío del formulario
        header("Location: " . $_SERVER['PHP_SELF'] . "?orden=" . $numero_orden . "&success=true");
        exit();

    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $conn->rollback();
        $error = "Error al guardar los datos: " . $e->getMessage();
        $_SESSION['mensaje_error'] = $error;
    }
}

// Obtener los datos actuales de la orden
if ($numero_orden > 0) {
    $sql = "SELECT * FROM ordenes_auditadas WHERE Orden = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $numero_orden);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $orden_data = $result->fetch_assoc();
        // Decodificar los datos JSON
        $siprem_data = json_decode($orden_data['siprem_data'] ?? '{}', true);
        $fotos_data = json_decode($orden_data['fotos_data'] ?? '{}', true);
        $validacion_data = json_decode($orden_data['validacion_data'] ?? '{}', true);
    } else {
        $error = "Orden no encontrada";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Orden Auditada</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
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
        h1, h3 {
            text-align: center;
            margin-bottom: 20px;
        }
        .tabla-preguntas {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background: white;
        }
        .tabla-preguntas th, .tabla-preguntas td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .tabla-preguntas th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .tabla-preguntas td:not(:last-child) {
            text-align: center;
        }
        textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            resize: vertical;
        }
        .progress-bar {
            margin: 20px 0;
            padding: 15px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .progress-stats {
            display: flex;
            justify-content: space-around;
            margin-bottom: 10px;
        }
        .progress {
            height: 20px;
            border-radius: 10px;
            background-color: #f0f0f0;
        }
        .progress-fill {
            height: 100%;
            border-radius: 10px;
            transition: width 0.3s ease;
        }
        .btn-container {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 20px;
        }
        .btn {
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .hidden {
            display: none;
        }
        @media (max-width: 768px) {
            .container {
                width: 95%;
                padding: 15px;
            }
            .tabla-preguntas th, .tabla-preguntas td {
                padding: 8px;
            }
            .progress-stats {
                flex-direction: column;
                align-items: center;
                gap: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">Editar Orden Auditada</h1>
        
        <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success" role="alert">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <?php if ($orden_data): ?>
            <form method="POST" class="needs-validation" novalidate id="auditForm">
                <!-- Información básica -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Información de la Orden</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Número de Orden</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($orden_data['Orden']); ?>" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fecha de Auditoría</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($orden_data['fecha_auditoria']); ?>" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección SIPREM -->
                <div class="section-title">SIPREM</div>
                <?php for ($i = 1; $i <= 20; $i++): ?>
                    <div class="question-row">
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="siprem-<?php echo $i; ?>" value="Conforme" 
                                <?php echo ($siprem_data["siprem-$i"]['respuesta'] ?? '') === 'Conforme' ? 'checked' : ''; ?>>
                            <label class="form-check-label">Conforme</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="siprem-<?php echo $i; ?>" value="No Conforme"
                                <?php echo ($siprem_data["siprem-$i"]['respuesta'] ?? '') === 'No Conforme' ? 'checked' : ''; ?>>
                            <label class="form-check-label">No Conforme</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="siprem-<?php echo $i; ?>" value="No Aplica"
                                <?php echo ($siprem_data["siprem-$i"]['respuesta'] ?? '') === 'No Aplica' ? 'checked' : ''; ?>>
                            <label class="form-check-label">No Aplica</label>
                        </div>
                        <textarea class="form-control observation-textarea" name="obs-siprem-<?php echo $i; ?>" 
                            placeholder="Observaciones"><?php echo htmlspecialchars($siprem_data["siprem-$i"]['observacion'] ?? ''); ?></textarea>
                    </div>
                <?php endfor; ?>

                <!-- Sección FOTOS -->
                <div class="section-title">FOTOS</div>
                <?php for ($i = 1; $i <= 20; $i++): ?>
                    <div class="question-row">
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="fotos-<?php echo $i; ?>" value="Conforme"
                                <?php echo ($fotos_data["fotos-$i"]['respuesta'] ?? '') === 'Conforme' ? 'checked' : ''; ?>>
                            <label class="form-check-label">Conforme</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="fotos-<?php echo $i; ?>" value="No Conforme"
                                <?php echo ($fotos_data["fotos-$i"]['respuesta'] ?? '') === 'No Conforme' ? 'checked' : ''; ?>>
                            <label class="form-check-label">No Conforme</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="fotos-<?php echo $i; ?>" value="No Aplica"
                                <?php echo ($fotos_data["fotos-$i"]['respuesta'] ?? '') === 'No Aplica' ? 'checked' : ''; ?>>
                            <label class="form-check-label">No Aplica</label>
                        </div>
                        <textarea class="form-control observation-textarea" name="obs-fotos-<?php echo $i; ?>" 
                            placeholder="Observaciones"><?php echo htmlspecialchars($fotos_data["fotos-$i"]['observacion'] ?? ''); ?></textarea>
                    </div>
                <?php endfor; ?>

                <!-- Sección VALIDACIÓN -->
                <div class="section-title">VALIDACIÓN</div>
                <?php for ($i = 1; $i <= 4; $i++): ?>
                    <div class="question-row">
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="validacion-<?php echo $i; ?>" value="Conforme"
                                <?php echo ($validacion_data["validacion-$i"]['respuesta'] ?? '') === 'Conforme' ? 'checked' : ''; ?>>
                            <label class="form-check-label">Conforme</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="validacion-<?php echo $i; ?>" value="No Conforme"
                                <?php echo ($validacion_data["validacion-$i"]['respuesta'] ?? '') === 'No Conforme' ? 'checked' : ''; ?>>
                            <label class="form-check-label">No Conforme</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="validacion-<?php echo $i; ?>" value="No Aplica"
                                <?php echo ($validacion_data["validacion-$i"]['respuesta'] ?? '') === 'No Aplica' ? 'checked' : ''; ?>>
                            <label class="form-check-label">No Aplica</label>
                        </div>
                        <textarea class="form-control observation-textarea" name="obs-validacion-<?php echo $i; ?>" 
                            placeholder="Observaciones"><?php echo htmlspecialchars($validacion_data["validacion-$i"]['observacion'] ?? ''); ?></textarea>
                    </div>
                <?php endfor; ?>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Guardar Cambios
                    </button>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Volver
                    </a>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS y Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>

    <script>
        // Función para validar el formulario antes de enviar
        function validarFormulario(event) {
            event.preventDefault();
            
            let todosLlenosRadio = true;
            let todosLlenosTextarea = true;
            let mensajeError = '';

            // Validar cada sección
            const secciones = ['siprem', 'fotos', 'validacion'];
            const limites = {
                'siprem': 20,
                'fotos': 20,
                'validacion': 4
            };

            for (let seccion of secciones) {
                for (let i = 1; i <= limites[seccion]; i++) {
                    const radios = document.getElementsByName(`${seccion}-${i}`);
                    const textarea = document.querySelector(`textarea[name="obs-${seccion}-${i}"]`);
                    let radioChecked = false;

                    radios.forEach(radio => {
                        if (radio.checked) {
                            radioChecked = true;
                            if (radio.value === 'No Conforme' && (!textarea.value || textarea.value.trim() === '')) {
                                todosLlenosTextarea = false;
                                mensajeError = 'Debe ingresar observaciones para todas las respuestas No Conforme';
                            }
                        }
                    });

                    if (!radioChecked) {
                        todosLlenosRadio = false;
                        mensajeError = `Debe responder todas las preguntas de ${seccion.toUpperCase()}`;
                    }
                }
            }

            if (!todosLlenosRadio || !todosLlenosTextarea) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de validación',
                    text: mensajeError
                });
                return false;
            }

            // Si todo está correcto, mostrar confirmación
            Swal.fire({
                title: '¿Desea guardar los cambios?',
                text: "Esta acción actualizará la información de la orden auditada",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, guardar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('auditForm').submit();
                }
            });
        }

        // Agregar el evento al formulario
        document.getElementById('auditForm').addEventListener('submit', validarFormulario);

        // Mostrar textarea cuando se selecciona No Conforme
        function handleSelection(preguntaId, valor) {
            const textarea = document.getElementById(`observation-${preguntaId}`);
            if (valor === 'No Conforme') {
                textarea.style.display = 'block';
                textarea.required = true;
            } else {
                textarea.style.display = 'none';
                textarea.required = false;
                textarea.value = ''; // Limpiar textarea si no es No Conforme
            }
            
            const grupo = preguntaId.split('-')[0];
            actualizarProgreso(grupo);
        }

        // Funciones para el manejo de la barra de progreso
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

            document.getElementById(`conformes-${grupo}`).textContent = conformes;
            document.getElementById(`no-conformes-${grupo}`).textContent = noConformes;
            document.getElementById(`no-aplica-${grupo}`).textContent = noAplica;

            const progressBar = document.getElementById(`progress-bar-${grupo}`);
            const porcentaje = ((conformes + noConformes + noAplica) / total) * 100;
            progressBar.style.width = porcentaje + '%';

            if (grupo === 'validacion') {
                if (noConformes >= 2) {
                    progressBar.style.backgroundColor = '#dc3545';
                    mostrarAlerta(grupo, 'No Conforme', noConformes);
                } else if (noConformes === 1) {
                    progressBar.style.backgroundColor = '#ffc107';
                    mostrarAlerta(grupo, 'Parcialmente No Conforme', noConformes);
                } else {
                    progressBar.style.backgroundColor = '#4CAF50';
                    ocultarAlerta(grupo);
                }
            } else {
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
        }

        function mostrarTextarea(preguntaId, mostrar) {
            const textarea = document.getElementById(`observation-${preguntaId}`);
            textarea.style.display = mostrar ? 'block' : 'none';
        }
    </script>

    <!-- SweetAlert2 para las confirmaciones -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Mostrar popup de éxito si la operación fue exitosa
        <?php if (isset($_GET['success']) && $_GET['success'] === 'true'): ?>
        Swal.fire({
            title: '¡Guardado exitoso!',
            text: 'Los cambios se han guardado correctamente',
            icon: 'success',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Aceptar'
        }).then((result) => {
            // Limpiar el parámetro success de la URL
            const url = new URL(window.location.href);
            url.searchParams.delete('success');
            window.history.replaceState({}, '', url);
        });
        <?php endif; ?>

        // Mostrar popup de error si hubo algún problema
        <?php if (isset($_SESSION['mensaje_error'])): ?>
        Swal.fire({
            title: 'Error',
            text: '<?php echo addslashes($_SESSION['mensaje_error']); ?>',
            icon: 'error',
            confirmButtonColor: '#d33',
            confirmButtonText: 'Aceptar'
        });
        <?php 
        unset($_SESSION['mensaje_error']);
        endif; 
        ?>

        // Función para validar y confirmar guardado
        function validarYGuardar(event) {
            event.preventDefault();
            
            // ... código de validación existente ...

            if (!todosLlenosRadio || !todosLlenosTextarea) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de validación',
                    text: mensajeError,
                    confirmButtonColor: '#d33'
                });
                return false;
            }

            // Si todo está correcto, mostrar confirmación
            Swal.fire({
                title: '¿Desea guardar los cambios?',
                text: 'Esta acción actualizará la información de la orden auditada',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, guardar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Mostrar indicador de carga
                    Swal.fire({
                        title: 'Guardando cambios',
                        text: 'Por favor espere...',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        allowEnterKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Enviar el formulario
                    document.getElementById('auditForm').submit();
                }
            });
        }

        // Asignar el evento al formulario
        document.getElementById('auditForm').addEventListener('submit', validarYGuardar);
    </script>
</body>
</html>
