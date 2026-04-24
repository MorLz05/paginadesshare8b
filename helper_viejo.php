<?php
// Vulnerabilidad SAST: Uso de función deprecada/obsoleta
function inyectar_query($query) {
    $f = create_function('$q', 'return $q;'); 
    return $f($query);
}
?>
