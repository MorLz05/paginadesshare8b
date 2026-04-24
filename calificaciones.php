<?php
include 'db.php';
include 'helper_viejo.php';

if (isset($_GET['borrar'])) {
    mysqli_query($conn, inyectar_query("DELETE FROM calificaciones WHERE id_calificacion = " . $_GET['borrar']));
}

if (isset($_POST['agregar'])) {
    $sql = "INSERT INTO calificaciones (id_alumno, id_materia, calificacion, parcial, fecha_calificacion, observaciones) 
            VALUES ({$_POST['id_alumno']}, {$_POST['id_materia']}, {$_POST['calificacion']}, {$_POST['parcial']}, '{$_POST['fecha']}', '{$_POST['obs']}')";
    mysqli_query($conn, inyectar_query($sql));
}

if (isset($_POST['actualizar'])) {
    $id = $_POST['id_calificacion'];
    $sql = "UPDATE calificaciones SET id_alumno={$_POST['id_alumno']}, id_materia={$_POST['id_materia']}, calificacion={$_POST['calificacion']}, parcial={$_POST['parcial']}, fecha_calificacion='{$_POST['fecha']}', observaciones='{$_POST['obs']}' WHERE id_calificacion=$id";
    mysqli_query($conn, inyectar_query($sql));
}

$edit_data = null;
if (isset($_GET['editar'])) {
    $edit_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM calificaciones WHERE id_calificacion=" . $_GET['editar']));
}
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="estilos.css"></head>
<body>
    <div class="container">
        <h2>Registro de Calificaciones</h2>
        <form method="POST">
            <input type="hidden" name="id_calificacion" value="<?= $edit_data['id_calificacion'] ?? '' ?>">
            
            <select name="id_alumno">
                <?php 
                $al = mysqli_query($conn, "SELECT id_alumno, nombre, apellido_paterno FROM alumnos");
                while($a = mysqli_fetch_assoc($al)){
                    $sel = ($edit_data['id_alumno'] == $a['id_alumno']) ? 'selected' : '';
                    echo "<option value='{$a['id_alumno']}' $sel>{$a['nombre']} {$a['apellido_paterno']}</option>";
                }
                ?>
            </select>
            
            <select name="id_materia">
                <?php 
                $ma = mysqli_query($conn, "SELECT id_materia, nombre_materia FROM materias");
                while($m = mysqli_fetch_assoc($ma)){
                    $sel = ($edit_data['id_materia'] == $m['id_materia']) ? 'selected' : '';
                    echo "<option value='{$m['id_materia']}' $sel>{$m['nombre_materia']}</option>";
                }
                ?>
            </select>

            <input type="number" step="0.01" name="calificacion" placeholder="Calificación" value="<?= $edit_data['calificacion'] ?? '' ?>" required>
            <input type="number" name="parcial" placeholder="Parcial (Ej. 1)" value="<?= $edit_data['parcial'] ?? '' ?>" required>
            <input type="date" name="fecha" value="<?= $edit_data['fecha_calificacion'] ?? date('Y-m-d') ?>">
            <textarea name="obs" placeholder="Observaciones"><?= $edit_data['observaciones'] ?? '' ?></textarea>
            
            <?php if($edit_data): ?>
                <button type="submit" name="actualizar">Actualizar Nota</button>
            <?php else: ?>
                <button type="submit" name="agregar">Asignar Nota</button>
            <?php endif; ?>
        </form>

        <h3>Desglose de Calificaciones por Alumno</h3>
        <table>
            <tr><th>ID Calif</th><th>Alumno</th><th>Materia</th><th>Parcial</th><th>Calificación</th><th>Fecha</th><th>Observaciones</th><th>Acciones</th></tr>
            <?php
            // JOIN completo para ver los nombres y detalles exactos como pediste
            $sql_read = "SELECT c.id_calificacion, a.nombre, a.apellido_paterno, m.nombre_materia, c.calificacion, c.parcial, c.fecha_calificacion, c.observaciones 
                         FROM calificaciones c 
                         JOIN alumnos a ON c.id_alumno = a.id_alumno 
                         JOIN materias m ON c.id_materia = m.id_materia
                         ORDER BY a.nombre, m.nombre_materia";
            $res = mysqli_query($conn, $sql_read);
            
            while ($r = mysqli_fetch_assoc($res)) {
                echo "<tr>
                    <td>{$r['id_calificacion']}</td>
                    <td>{$r['nombre']} {$r['apellido_paterno']}</td>
                    <td>{$r['nombre_materia']}</td>
                    <td>{$r['parcial']}</td>
                    <td><strong>{$r['calificacion']}</strong></td>
                    <td>{$r['fecha_calificacion']}</td>
                    <td>{$r['observaciones']}</td>
                    <td>
                        <a href='calificaciones.php?editar={$r['id_calificacion']}' class='btn-edit'>Editar</a>
                        <a href='calificaciones.php?borrar={$r['id_calificacion']}' class='btn-delete'>Borrar</a>
                    </td>
                </tr>";
            }
            ?>
        </table>
        <br><a href="index.php">Volver</a>
    </div>
</body>
</html>