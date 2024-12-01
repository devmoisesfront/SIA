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
    die('Error de conexión: ' . htmlspecialchars($conn->connect_error));
}

// Inicializar variables
$orden_id = isset($_GET['orden']) ? (int) $_GET['orden'] : 0;
$error = '';

// Verificar que se envió el parámetro "orden"
if ($orden_id === 0) {
    die('El parámetro "orden" no fue proporcionado o es inválido. Por favor, verifica la URL.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar y sanitizar datos del formulario
    $respuesta1 = trim(filter_input(INPUT_POST, 'respuesta1', FILTER_SANITIZE_STRING));
    $observacion1 = trim(filter_input(INPUT_POST, 'observacion1', FILTER_SANITIZE_STRING));
    $respuesta2 = trim(filter_input(INPUT_POST, 'respuesta2', FILTER_SANITIZE_STRING));
    $observacion2 = trim(filter_input(INPUT_POST, 'observacion2', FILTER_SANITIZE_STRING));
    $respuesta3 = trim(filter_input(INPUT_POST, 'respuesta3', FILTER_SANITIZE_STRING));
    $observacion3 = trim(filter_input(INPUT_POST, 'observacion3', FILTER_SANITIZE_STRING));

    // Verificar campos requeridos
    if (empty($respuesta1) || empty($respuesta2) || empty($respuesta3)) {
        $error = 'Los campos de respuesta son obligatorios.';
    } else {
        // Actualizar la respuesta en la base de datos
        $sql_update = "UPDATE auditoria_respuestas 
                       SET respuesta1 = ?, observacion1 = ?, 
                           respuesta2 = ?, observacion2 = ?, 
                           respuesta3 = ?, observacion3 = ? 
                       WHERE orden_id = ?";
        $stmt_update = $conn->prepare($sql_update);

        if (!$stmt_update) {
            die('Error al preparar la consulta: ' . htmlspecialchars($conn->error));
        }

        $stmt_update->bind_param("ssssssi", $respuesta1, $observacion1, $respuesta2, $observacion2, $respuesta3, $observacion3, $orden_id);

        if ($stmt_update->execute()) {
            echo "Respuesta actualizada correctamente. <a href='index.php'>Volver al inicio</a>";
            $stmt_update->close();
            $conn->close();
            exit();
        } else {
            $error = 'Error al actualizar la respuesta: ' . htmlspecialchars($conn->error);
        }
    }
}

// Recuperar los datos actuales de la respuesta
$sql_select = "SELECT * FROM auditoria_respuestas WHERE orden_id = ?";
$stmt_select = $conn->prepare($sql_select);

if (!$stmt_select) {
    die('Error al preparar la consulta: ' . htmlspecialchars($conn->error));
}

$stmt_select->bind_param("i", $orden_id);
$stmt_select->execute();
$result = $stmt_select->get_result();

if ($result->num_rows === 0) {
    die('Respuesta no encontrada para el orden especificado.');
}

$respuesta = $result->fetch_assoc();
$stmt_select->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Respuesta Auditada</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .form-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }
        h1 {
            font-size: 20px;
            margin-bottom: 20px;
        }
        input, textarea, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background-color: #007BFF;
            color: white;
            cursor: pointer;
            border: none;
        }
        button:hover {
            background-color: #0056b3;
        }
        .error {
            color: red;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Editar Respuesta Auditada</h1>
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST">
            <label for="respuesta1">Respuesta 1:</label>
            <textarea id="respuesta1" name="respuesta1" required><?php echo htmlspecialchars($respuesta['respuesta1']); ?></textarea>

            <label for="observacion1">Observación 1:</label>
            <textarea id="observacion1" name="observacion1"><?php echo htmlspecialchars($respuesta['observacion1']); ?></textarea>

            <label for="respuesta2">Respuesta 2:</label>
            <textarea id="respuesta2" name="respuesta2" required><?php echo htmlspecialchars($respuesta['respuesta2']); ?></textarea>

            <label for="observacion2">Observación 2:</label>
            <textarea id="observacion2" name="observacion2"><?php echo htmlspecialchars($respuesta['observacion2']); ?></textarea>

            <label for="respuesta3">Respuesta 3:</label>
            <textarea id="respuesta3" name="respuesta3" required><?php echo htmlspecialchars($respuesta['respuesta3']); ?></textarea>

            <label for="observacion3">Observación 3:</label>
            <textarea id="observacion3" name="observacion3"><?php echo htmlspecialchars($respuesta['observacion3']); ?></textarea>

            <button type="submit">Guardar Cambios</button>
        </form>
    </div>
</body>
</html>
