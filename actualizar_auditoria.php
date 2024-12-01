<?php
// Configuración de la base de datos
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'ordenes_db';

$conn = new mysqli($host, $user, $password, $dbname);


// Verificar conexión
if ($conn->connect_error) {
    die('Error de conexión: ' . $conn->connect_error);
}

// Buscar órdenes con auditoría "Completa" en la tabla `ordenes`
$sql_select_completas = "SELECT * FROM ordenes WHERE Auditoria = 'Completa'";
$result_completas = $conn->query($sql_select_completas);

if ($result_completas->num_rows > 0) {
    while ($row = $result_completas->fetch_assoc()) {
        // Insertar en la tabla `ordenes_auditadas`
        $sql_insert_auditada = "INSERT INTO ordenes_auditadas (Orden, TipoOrden, Territorio, Auditoria)
                                VALUES (?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert_auditada);
        $stmt_insert->bind_param("isss", $row['Orden'], $row['TipoOrden'], $row['Territorio'], $row['Auditoria']);
        $stmt_insert->execute();

        // Eliminar de la tabla `ordenes`
        $sql_delete_original = "DELETE FROM ordenes WHERE Orden = ?";
        $stmt_delete = $conn->prepare($sql_delete_original);
        $stmt_delete->bind_param("i", $row['Orden']);
        $stmt_delete->execute();
    }
    echo "Órdenes completas movidas a la tabla auditada correctamente.";
} else {
    echo "No hay órdenes completas para mover.";
}

$conn->close();
?>
