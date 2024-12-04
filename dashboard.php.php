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
    
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome para iconos -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f4f4f9;
        }

        .container, .auditadas-container {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 20px;
            max-width: 1000px !important;
            width: 90% !important;
            margin-left: auto !important;
            margin-right: auto !important;
        }

        .auditadas-container {
            margin-bottom: 20px;
        }

        .table {
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 0;
        }

        .table thead th {
            background-color: #0d6efd;
            color: white;
            border: none;
        }

        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }

        .pagination {
            margin: 20px 0;
            gap: 5px;
        }

        .btn-primary {
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        @media (max-width: 768px) {
            .container, .auditadas-container {
                padding: 10px;
                width: 95% !important;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="text-center my-4">Tabla de Órdenes</h1>
    
    <div class="table-responsive">
        <table class="table table-hover">
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
                                <button class='btn btn-primary btn-sm' onclick=\"location.href='formulario.php?orden={$row['Orden']}'\">
                                    <i class='fas fa-edit me-1'></i>Auditar
                                </button>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center'>No hay datos disponibles.</td></tr>";
                }

            $sql_total = "SELECT COUNT(*) AS total FROM ordenes";
            $result_total = $conn->query($sql_total);
            $total_orders = $result_total->fetch_assoc()['total'];
            $total_pages = ceil($total_orders / $results_per_page);

                $stmt->close();
                ?>
            </tbody>
        </table>
    </div>

    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <?php
            for ($i = 1; $i <= $total_pages; $i++) {
                $active = $i === $page ? 'active' : '';
                echo "<li class='page-item $active'><a class='page-link' href='index.php?page=$i'>$i</a></li>";
            }
            ?>
        </ul>
    </nav>
</div>

<div class="auditadas-container">
    <h2 class="text-center my-4">Órdenes Auditadas</h2>
    
    <div class="table-responsive">
        <table class="table table-hover">
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
                // Parámetros de paginación para órdenes auditadas
                $auditadas_page = isset($_GET['auditadas_page']) ? (int)$_GET['auditadas_page'] : 1;
                if ($auditadas_page < 1) $auditadas_page = 1;
                $auditadas_start = ($auditadas_page - 1) * $results_per_page;

                // Consulta con LIMIT para paginación
                $sql_auditadas = "SELECT * FROM ordenes_auditadas LIMIT ?, ?";
                $stmt_auditadas = $conn->prepare($sql_auditadas);
                $stmt_auditadas->bind_param("ii", $auditadas_start, $results_per_page);
                $stmt_auditadas->execute();
                $result_auditadas = $stmt_auditadas->get_result();

                if ($result_auditadas->num_rows > 0) {
                    while ($row = $result_auditadas->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>{$row['Orden']}</td>";
                        echo "<td>{$row['TipoOrden']}</td>";
                        echo "<td>{$row['Territorio']}</td>";
                        echo "<td>{$row['fecha_auditoria']}</td>";
                        echo "<td>
                                <button class='btn btn-primary btn-sm' onclick=\"location.href='editar_auditada.php?orden={$row['Orden']}'\">
                                    <i class='fas fa-edit me-1'></i>Editar
                                </button>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center'>No hay órdenes auditadas disponibles.</td></tr>";
                }

                // Calcular el total de páginas para órdenes auditadas
                $sql_total_auditadas = "SELECT COUNT(*) AS total FROM ordenes_auditadas";
                $result_total_auditadas = $conn->query($sql_total_auditadas);
                $total_auditadas = $result_total_auditadas->fetch_assoc()['total'];
                $total_pages_auditadas = ceil($total_auditadas / $results_per_page);
                ?>
            </tbody>
        </table>
    </div>

    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <?php
            for ($i = 1; $i <= $total_pages_auditadas; $i++) {
                $active = $i === $auditadas_page ? 'active' : '';
                echo "<li class='page-item $active'><a class='page-link' href='index.php?page=$page&auditadas_page=$i'>$i</a></li>";
            }
            ?>
        </ul>
    </nav>
</div>

<!-- Bootstrap JS y Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>

</body>
</html>
