<?php
require_once '../includes/auth_middleware.php';
require_once '../assets/conexion/config.php';
// productos
$productos = $conn->query("SELECT * FROM productos");

// pedidos
$pedidos = $conn->query("SELECT * FROM pedidos ORDER BY id DESC");

if (isset($_POST['id']) && isset($_POST['estado'])) {
    $stmt = $conn->prepare("UPDATE pedidos SET estado=? WHERE id=?");
    $stmt->bind_param(
        "si",
        $_POST['estado'],
        $_POST['id']
    );
    $stmt->execute();
}

header("Location: ../admin/dashboard.php");
?>

<!DOCTYPE html>
<html>

<head>
    <title>Admin CoffeeCol</title>
    <style>
        body {
            font-family: Arial;
            background: #111;
            color: #fff;
        }

        h1 {
            margin-bottom: 20px;
        }

        .card {
            background: #1c1c1c;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
        }

        input,
        button,
        select {
            padding: 8px;
            margin: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 10px;
            border-bottom: 1px solid #333;
        }

        button {
            cursor: pointer;
        }

        .btn {
            background: #47060E;
            color: #fff;
            border: none;
        }

        .btn-danger {
            background: red;
        }

        .btn-success {
            background: green;
        }
    </style>
</head>

<body>

    <h1>☕ Panel Admin CoffeeCol</h1>

    <!-- ================= PRODUCTOS ================= -->
    <div class="card">
        <h2>📦 Productos</h2>

        <form method="POST" action="../api/products.php">
            <input name="nombre" placeholder="Nombre" required>
            <input name="precio" placeholder="Precio" required>
            <input name="categoria" placeholder="Categoria" required>
            <input name="descripcion" placeholder="Descripcion">
            <input name="icono" placeholder="Icono fa">
            <button class="btn">Agregar</button>
        </form>

        <table>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Acciones</th>
            </tr>

            <?php while ($p = $productos->fetch_assoc()): ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    <td><?= $p['nombre'] ?></td>
                    <td>$<?= $p['precio'] ?></td>
                    <td>
                        <form method="POST" action="../api/products.php" style="display:inline;">
                            <input type="hidden" name="delete" value="<?= $p['id'] ?>">
                            <button class="btn-danger">Eliminar</button>
                        </form>

                        <form method="POST" action="../api/products.php" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $p['id'] ?>">
                            <input name="nombre" value="<?= $p['nombre'] ?>">
                            <input name="precio" value="<?= $p['precio'] ?>">
                            <button class="btn-success">Editar</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

    </div>

    <!-- ================= PEDIDOS ================= -->
    <div class="card">
        <h2>🧾 Pedidos</h2>

        <table>
            <tr>
                <th>ID</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Cambiar</th>
            </tr>

            <?php while ($o = $pedidos->fetch_assoc()): ?>
                <tr>
                    <td><?= $o['id'] ?></td>
                    <td>$<?= $o['total'] ?></td>
                    <td><?= $o['estado'] ?></td>
                    <td>
                        <form method="POST" action="../api/orders.php">
                            <input type="hidden" name="id" value="<?= $o['id'] ?>">
                            <select name="estado">
                                <option value="pendiente">Pendiente</option>
                                <option value="enviado">Enviado</option>
                            </select>
                            <button class="btn">Actualizar</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

    </div>

</body>

</html>