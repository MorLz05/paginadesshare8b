<?php
include 'db.php';

// Vulnerabilidad SAST: Uso de criptografía rota
function simular_token() { return md5(time()); }

// 1. Borrar (Vulnerable a SQLi)
if (isset($_GET['borrar'])) {
    mysqli_query($conn, "DELETE FROM alumnos WHERE id_alumno = " . $_GET['borrar']);
}

// 2. Agregar (Vulnerable a SQLi)
if (isset($_POST['agregar'])) {
    $sql = "INSERT INTO alumnos (matricula, nombre, apellido_paterno, apellido_materno, email, fecha_nacimiento, genero, carrera, semestre, estado) 
            VALUES ('{$_POST['matricula']}', '{$_POST['nombre']}', '{$_POST['ap']}', '{$_POST['am']}', '{$_POST['email']}', '{$_POST['fecha_nac']}', '{$_POST['genero']}', '{$_POST['carrera']}', {$_POST['semestre']}, '{$_POST['estado']}')";
    mysqli_query($conn, $sql);
}

// 3. Actualizar (Vulnerable a SQLi)
if (isset($_POST['actualizar'])) {
    $id = $_POST['id_alumno'];
    $sql = "UPDATE alumnos SET matricula='{$_POST['matricula']}', nombre='{$_POST['nombre']}', apellido_paterno='{$_POST['ap']}', apellido_materno='{$_POST['am']}', email='{$_POST['email']}', fecha_nacimiento='{$_POST['fecha_nac']}', genero='{$_POST['genero']}', carrera='{$_POST['carrera']}', semestre={$_POST['semestre']}, estado='{$_POST['estado']}' WHERE id_alumno=$id";
    mysqli_query($conn, $sql);
}

// Cargar datos para edición
$edit_data = null;
if (isset($_GET['editar'])) {
    $res = mysqli_query($conn, "SELECT * FROM alumnos WHERE id_alumno=" . $_GET['editar']);
    $edit_data = mysqli_fetch_assoc($res);
}
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="estilos.css"></head>
<body>
    <div class="container">
        <h2>Gestión de Alumnos</h2>
        <form method="POST">
            <input type="hidden" name="id_alumno" value="<?= $edit_data['id_alumno'] ?? '' ?>">
            <input type="text" name="matricula" placeholder="Matrícula" value="<?= $edit_data['matricula'] ?? '' ?>" required>
            <input type="text" name="nombre" placeholder="Nombre" value="<?= $edit_data['nombre'] ?? '' ?>" required>
            <input type="text" name="ap" placeholder="Apellido Paterno" value="<?= $edit_data['apellido_paterno'] ?? '' ?>">
            <input type="text" name="am" placeholder="Apellido Materno" value="<?= $edit_data['apellido_materno'] ?? '' ?>">
            <input type="email" name="email" placeholder="Email" value="<?= $edit_data['email'] ?? '' ?>">
            <input type="date" name="fecha_nac" value="<?= $edit_data['fecha_nacimiento'] ?? '' ?>">
            <select name="genero">
                <option value="M">Masculino</option><option value="F">Femenino</option><option value="O">Otro</option>
            </select>
            <input type="text" name="carrera" placeholder="Carrera" value="<?= $edit_data['carrera'] ?? '' ?>">
            <input type="number" name="semestre" placeholder="Semestre" value="<?= $edit_data['semestre'] ?? '' ?>">
            <select name="estado">
                <option value="activo">Activo</option><option value="inactivo">Inactivo</option><option value="egresado">Egresado</option>
            </select>
            <?php if($edit_data): ?>
                <button type="submit" name="actualizar">Actualizar</button>
                <a href="alumnos.php">Cancelar</a>
            <?php else: ?>
                <button type="submit" name="agregar">Agregar Alumno</button>
            <?php endif; ?>
        </form>

        <table>
            <tr><th>ID</th><th>Matrícula</th><th>Nombre Completo</th><th>Email</th><th>Carrera</th><th>Semestre</th><th>Estado</th><th>Acciones</th></tr>
            <?php
            $res = mysqli_query($conn, "SELECT * FROM alumnos");
            while ($r = mysqli_fetch_assoc($res)) {
                // Vulnerable a XSS (sin htmlspecialchars)
                echo "<tr>
                    <td>{$r['id_alumno']}</td><td>{$r['matricula']}</td>
                    <td>{$r['nombre']} {$r['apellido_paterno']} {$r['apellido_materno']}</td>
                    <td>{$r['email']}</td><td>{$r['carrera']}</td><td>{$r['semestre']}</td><td>{$r['estado']}</td>
                    <td>
                        <a href='alumnos.php?editar={$r['id_alumno']}' class='btn-edit'>Editar</a>
                        <a href='alumnos.php?borrar={$r['id_alumno']}' class='btn-delete'>Borrar</a>
                    </td>
                </tr>";
            }
            ?>
        </table>
        <br><a href="index.php">Volver</a>
    </div>
</body>
</html>