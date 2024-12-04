<?php
header('Content-Type: application/json');

// Configuración de la base de datos
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'ordenes_db';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode([
        'success' => false,
        'message' => 'Error de conexión: ' . $conn->connect_error
    ]));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orden_id = $_POST['orden_id'] ?? null;
    $is_auto_save = isset($_POST['auto_save']);

    if (!$orden_id) {
        die(json_encode([
            'success' => false,
            'message' => 'No se especificó un número de orden'
        ]));
    }

    $conn->begin_transaction();

    try {
        // Procesar las respuestas como antes...
        $responses = [];
        
        foreach ($_POST as $key => $value) {
            if (preg_match('/^(siprem|fotos)-(\d+)$/', $key, $matches)) {
                $tipo = $matches[1];
                $numero = $matches[2];
                $observacion = $_POST["obs-{$tipo}-{$numero}"] ?? '';
                
                $responses[$tipo][] = [
                    'pregunta' => $numero,
                    'respuesta' => $value,
                    'observacion' => $observacion
                ];
            }
        }

        // Si es guardado automático, usar tabla temporal
        $tabla_sufijo = $is_auto_save ? '_temp' : '';

        foreach ($responses as $tipo => $respuestas) {
            $tabla = "auditoria_{$tipo}{$tabla_sufijo}";
            
            // Eliminar registros anteriores si es guardado automático
            if ($is_auto_save) {
                $sql_delete = "DELETE FROM {$tabla} WHERE orden_id = ?";
                $stmt_delete = $conn->prepare($sql_delete);
                $stmt_delete->bind_param("i", $orden_id);
                $stmt_delete->execute();
            }

            $sql = "INSERT INTO {$tabla} (orden_id, pregunta_numero, respuesta, observacion, fecha_auditoria) 
                    VALUES (?, ?, ?, ?, NOW())";
            $stmt = $conn->prepare($sql);

            foreach ($respuestas as $respuesta) {
                $stmt->bind_param("iiss",
                    $orden_id,
                    $respuesta['pregunta'],
                    $respuesta['respuesta'],
                    $respuesta['observacion']
                );
                
                if (!$stmt->execute()) {
                    throw new Exception("Error guardando respuesta de {$tipo}");
                }
            }
        }

        // Si no es guardado automático, mover la orden a auditadas
        if (!$is_auto_save) {
            $sql_mover = "INSERT INTO ordenes_auditadas SELECT *, NOW() as fecha_auditoria FROM ordenes WHERE Orden = ?";
            $stmt_mover = $conn->prepare($sql_mover);
            $stmt_mover->bind_param("i", $orden_id);
            $stmt_mover->execute();

            $sql_eliminar = "DELETE FROM ordenes WHERE Orden = ?";
            $stmt_eliminar = $conn->prepare($sql_eliminar);
            $stmt_eliminar->bind_param("i", $orden_id);
            $stmt_eliminar->execute();
        }

        $conn->commit();
        
        echo json_encode([
            'success' => true,
            'message' => $is_auto_save ? 'Guardado automático completado' : 'Auditoría guardada completamente',
            'is_auto_save' => $is_auto_save
        ]);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    $conn->close();
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
}
?>

