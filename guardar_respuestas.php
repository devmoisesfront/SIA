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

$message = '';
$success = false;

// Verificar si se enviaron los datos del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orden_id = $_POST['orden'] ?? null;

    // Validar si la orden fue seleccionada
    if (!$orden_id) {
        $message = 'No se seleccionó ninguna orden.';
    } else {
        // Recopilar las respuestas y observaciones
        $respuesta1 = $_POST['respuesta1'] ?? null;
        $observacion1 = $_POST['observacion1'] ?? '';
        $respuesta2 = $_POST['respuesta2'] ?? null;
        $observacion2 = $_POST['observacion2'] ?? '';
        $respuesta3 = $_POST['respuesta3'] ?? null;
        $observacion3 = $_POST['observacion3'] ?? '';

        // Validar que las respuestas obligatorias estén presentes
        if (!$respuesta1 || !$respuesta2 || !$respuesta3) {
            $message = 'Debe responder todas las preguntas obligatorias.';
        } else {
            // Iniciar una transacción
            $conn->begin_transaction();

            try {
                // 1. Insertar las respuestas en la tabla `auditoria_respuestas`
                $sql_respuestas = "INSERT INTO auditoria_respuestas 
                                   (orden_id, respuesta1, observacion1, respuesta2, observacion2, respuesta3, observacion3, fecha_auditoria) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
                $stmt_respuestas = $conn->prepare($sql_respuestas);
                if (!$stmt_respuestas) {
                    throw new Exception('Error en la preparación de la consulta: ' . $conn->error);
                }

                $stmt_respuestas->bind_param(
                    "issssss",
                    $orden_id,
                    $respuesta1,
                    $observacion1,
                    $respuesta2,
                    $observacion2,
                    $respuesta3,
                    $observacion3
                );

                if (!$stmt_respuestas->execute()) {
                    throw new Exception('Error al guardar las respuestas: ' . $stmt_respuestas->error);
                }

                // 2. Mover la orden a la tabla `ordenes_auditadas`
                $sql_mover = "INSERT INTO ordenes_auditadas (Orden, TipoOrden, Territorio, Auditoria, fecha_auditoria)
                              SELECT Orden, TipoOrden, Territorio, 'Completada', NOW() FROM ordenes WHERE Orden = ?";
                $stmt_mover = $conn->prepare($sql_mover);
                if (!$stmt_mover) {
                    throw new Exception('Error en la preparación de la consulta para mover la orden: ' . $conn->error);
                }

                $stmt_mover->bind_param("i", $orden_id);
                if (!$stmt_mover->execute()) {
                    throw new Exception('Error al mover la orden a auditadas: ' . $stmt_mover->error);
                }

                // 3. Eliminar la orden de la tabla `ordenes`
                $sql_eliminar = "DELETE FROM ordenes WHERE Orden = ?";
                $stmt_eliminar = $conn->prepare($sql_eliminar);
                if (!$stmt_eliminar) {
                    throw new Exception('Error en la preparación de la consulta para eliminar la orden: ' . $conn->error);
                }

                $stmt_eliminar->bind_param("i", $orden_id);
                if (!$stmt_eliminar->execute()) {
                    throw new Exception('Error al eliminar la orden de la tabla original: ' . $stmt_eliminar->error);
                }

                // Confirmar la transacción
                $conn->commit();
                $success = true;
                $message = 'Respuestas guardadas y la orden fue auditada correctamente.';
            } catch (Exception $e) {
                // Revertir la transacción en caso de error
                $conn->rollback();
                $message = 'Error: ' . $e->getMessage();
            }

            // Cerrar las declaraciones
            $stmt_respuestas->close();
            $stmt_mover->close();
            $stmt_eliminar->close();
        }
    }

    // Cerrar la conexión
    $conn->close();
} else {
    $message = 'Acceso no autorizado.';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado de la Auditoría</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .card {
            max-width: 400px;
            margin: 20px;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        .card.success {
            background-color: #d4edda;
            color: #155724;
        }
        .card.error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .card .icon {
            font-size: 50px;
            margin-bottom: 10px;
        }
        .card.success .icon {
            color: #28a745;
        }
        .card.error .icon {
            color: #dc3545;
        }
        .card h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        .card p {
            font-size: 18px;
        }
    </style>
    <?php if ($success): ?>
    <meta http-equiv="refresh" content="3;url=index.php">
    <?php endif; ?>
</head>
<body>

<div class="card <?php echo $success ? 'success' : 'error'; ?>">
    <div class="icon">
        <?php echo $success ? '&#10003;' : '&#10060;'; ?>
    </div>
    <h1><?php echo $success ? '¡Éxito!' : 'Error'; ?></h1>
    <p><?php echo $message; ?></p>
</div>

</body>
</html>
