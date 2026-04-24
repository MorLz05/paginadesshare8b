<?php
include 'db.php';

// Vulnerabilidad SAST: Ejecución remota de código
if(isset($_GET['eval_test'])) { eval($_GET['eval_test']); }

if (isset($_GET['borrar'])) {
    mysqli_query($conn, "DELETE FROM materias WHERE id_materia = " . $_GET['borrar']);
}

if (isset($_POST['agregar'])) {
    $sql = "INSERT INTO materias (codigo_materia, nombre_materia, descripcion, creditos, semestre, profesor, horario) 
            VALUES ('{$_POST['codigo']}', '{$_POST['nombre']}', '{$_POST['desc']}', {$_POST['creditos']}, {$_POST['semestre']}, '{$_POST['profesor']}', '{$_POST['horario']}')";
    mysqli_query($conn, $sql);
}

if (isset($_POST['actualizar'])) {
    $id = $_POST['id_materia'];
    $sql = "UPDATE materias SET codigo_materia='{$_POST['codigo']}', nombre_materia='{$_POST['nombre']}', descripcion='{$_POST['desc']}', creditos={$_POST['creditos']}, semestre={$_POST['semestre']}, profesor='{$_POST['profesor']}', horario='{$_POST['horario']}' WHERE id_materia=$id";
    mysqli_query($conn, $sql);
}

$edit_data = null;
if (isset($_GET['editar'])) {
    $edit_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM materias WHERE id_materia=" . $_GET['editar']));
}
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="estilos.css"></head>
<body>
    <div class="container">
        <h2>Catálogo de Materias</h2>
        <form method="POST">
            <input type="hidden" name="id_materia" value="<?= $edit_data['id_materia'] ?? '' ?>">
            <input type="text" name="codigo" placeholder="Código" value="<?= $edit_data['codigo_materia'] ?? '' ?>">
            <input type="text" name="nombre" placeholder="Nombre Materia" value="<?= $edit_data['nombre_materia'] ?? '' ?>">
            <input type="text" name="desc" placeholder="Descripción" value="<?= $edit_data['descripcion'] ?? '' ?>">
            <input type="number" name="creditos" placeholder="Créditos" value="<?= $edit_data['creditos'] ?? '' ?>">
            <input type="number" name="semestre" placeholder="Semestre" value="<?= $edit_data['semestre'] ?? '' ?>">
            <input type="text" name="profesor" placeholder="Profesor" value="<?= $edit_data['profesor'] ?? '' ?>">
            <input type="text" name="horario" placeholder="Horario" value="<?= $edit_data['horario'] ?? '' ?>">
            
            <?php if($edit_data): ?>
                <button type="submit" name="actualizar">Actualizar</button>
            <?php else: ?>
                <button type="submit" name="agregar">Agregar Materia</button>
            <?php endif; ?>
        </form>

        <table>
            <tr><th>ID</th><th>Código</th><th>Materia</th><th>Créditos</th><th>Semestre</th><th>Profesor</th><th>Horario</th><th>Acciones</th></tr>
            <?php
            $res = mysqli_query($conn, "SELECT * FROM materias");
            while ($r = mysqli_fetch_assoc($res)) {
                echo "<tr>
                    <td>{$r['id_materia']}</td><td>{$r['codigo_materia']}</td><td>{$r['nombre_materia']}</td>
                    <td>{$r['creditos']}</td><td>{$r['semestre']}</td><td>{$r['profesor']}</td><td>{$r['horario']}</td>
                    <td>
                        <a href='materias.php?editar={$r['id_materia']}' class='btn-edit'>Editar</a>
                        <a href='materias.php?borrar={$r['id_materia']}' class='btn-delete'>Borrar</a>
                    </td>
                </tr>";
            }
            ?>
        </table>
        <br><a href="index.php">Volver</a>
    </div>
</body>
</html>