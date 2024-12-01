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

// *** Mover órdenes completas a la tabla auditada ***
$sql_select_completas = "SELECT * FROM ordenes WHERE Auditoria = 'Completa'";
$result_completas = $conn->query($sql_select_completas);

if ($result_completas->num_rows > 0) {
    while ($row = $result_completas->fetch_assoc()) {
        // Insertar en la tabla `ordenes_auditadas`
        $sql_insert_auditada = "INSERT INTO ordenes_auditadas (Orden, TipoOrden, Territorio, Auditoria, fecha_auditoria)
                                VALUES (?, ?, ?, ?, NOW())";
        $stmt_insert = $conn->prepare($sql_insert_auditada);
        $stmt_insert->bind_param("isss", $row['Orden'], $row['TipoOrden'], $row['Territorio'], $row['Auditoria']);
        $stmt_insert->execute();

        // Eliminar de la tabla `ordenes`
        $sql_delete_original = "DELETE FROM ordenes WHERE Orden = ?";
        $stmt_delete = $conn->prepare($sql_delete_original);
        $stmt_delete->bind_param("i", $row['Orden']);
        $stmt_delete->execute();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabla de Órdenes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 0;
            background-color: #f4f4f9;
        }
        .container {
            width: 90%;
            max-width: 1000px;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        button {
            padding: 8px 12px;
            border: none;
            cursor: pointer;
            background-color: #007BFF;
            color: white;
            border-radius: 4px;
            font-size: 14px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .pagination {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        .pagination a {
            padding: 10px 15px;
            text-decoration: none;
            border: 1px solid #ccc;
            border-radius: 5px;
            color: #007BFF;
        }
        .pagination a:hover {
            background-color: #007BFF;
            color: white;
        }
        .pagination .active {
            background-color: #007BFF;
            color: white;
            border-color: #007BFF;
        }
        .auditadas-container {
            margin-top: 40px;
            width: 100%;
        }
        .auditadas-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .auditadas-table, .auditadas-table th, .auditadas-table td {
            border: 1px solid #ddd;
        }
        .auditadas-table th, .auditadas-table td {
            padding: 12px;
            text-align: center;
        }
        .auditadas-table th {
            background-color: #007BFF;
            color: white;
        }
        .auditadas-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .auditadas-table tr:hover {
            background-color: #ddd;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Tabla de Órdenes</h1>
    <table>
        <thead>
            <tr>
                <th>Orden</th>
                <th>Tipo de Orden</th>
                <th>Territorio</th>
                <th>Auditoría</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Parámetros de paginación
            $results_per_page = 20;
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            if ($page < 1) $page = 1;

            $start_from = ($page - 1) * $results_per_page;

            $sql = "SELECT * FROM ordenes LIMIT ?, ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $start_from, $results_per_page);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$row['Orden']}</td>";
                    echo "<td>{$row['TipoOrden']}</td>";
                    echo "<td>{$row['Territorio']}</td>";
                    echo "<td>{$row['Auditoria']}</td>";
                    echo "<td>
                            <button onclick=\"location.href='formulario.php?orden={$row['Orden']}'\">Auditar</button>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No hay datos disponibles.</td></tr>";
            }

            $sql_total = "SELECT COUNT(*) AS total FROM ordenes";
            $result_total = $conn->query($sql_total);
            $total_orders = $result_total->fetch_assoc()['total'];
            $total_pages = ceil($total_orders / $results_per_page);

            $stmt->close();
            ?>
        </tbody>
    </table>

    <div class="pagination">
        <?php
        for ($i = 1; $i <= $total_pages; $i++) {
            $active = $i === $page ? 'class="active"' : '';
            echo "<a href='index.php?page=$i' $active>$i</a>";
        }
        ?>
    </div>
</div>

<div class="auditadas-container">
    <h2>Órdenes Auditadas</h2>
    <table class="auditadas-table">
        <thead>
            <tr>
                <th>Orden</th>
                <th>Tipo de Orden</th>
                <th>Territorio</th>
                <th>Fecha de Auditoría</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql_auditadas = "SELECT * FROM ordenes_auditadas";
            $result_auditadas = $conn->query($sql_auditadas);

            if ($result_auditadas->num_rows > 0) {
                while ($row = $result_auditadas->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$row['Orden']}</td>";
                    echo "<td>{$row['TipoOrden']}</td>";
                    echo "<td>{$row['Territorio']}</td>";
                    echo "<td>{$row['fecha_auditoria']}</td>";
                    echo "<td>
                            <button onclick=\"location.href='editar_auditada.php?orden={$row['Orden']}'\">
                                <img src='edit-icon.png' alt='Editar' style='width:16px; height:16px; vertical-align:middle; margin-right:5px;'>Editar
                            </button>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No hay órdenes auditadas disponibles.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>
