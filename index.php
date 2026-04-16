<?php
require_once 'src/Calculo/IntegradorNumerico.php';
use App\Calculo\IntegradorNumerico;

$resultado = null;
$error = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Se realiza el casting (float e int) para coincidir con los tipos de la clase
        $integrador = new IntegradorNumerico(
            (float)$_POST['t_inicio'],   // Cast a float para el tiempo inicial
            (float)$_POST['t_fin'],      // Cast a float para el tiempo final
            (int)$_POST['precision']     // Cast a int para la cantidad de pasos
        );
        $resultado = $integrador->calcularEnergiaTotal();
    } catch (Exception $e) {
        // Atrapa InvalidArgumentException o cualquier otra excepción
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cloud Energy Monitor</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Monitor de Energía (DataCenter)</h1>
        <form method="POST">
            <label>Tiempo Inicial (s):</label>
            <input type="number" name="t_inicio" step="0.1" required>

            <label>Tiempo Final (s):</label>
            <input type="number" name="t_fin" step="0.1" required>

            <label>Precisión (n subintervalos):</label>
            <input type="number" name="precision" value="1000" required>

            <button type="submit">Calcular Joules Consumidos</button>
        </form>

        <?php if ($resultado !== null): ?>
            <div class="result">
                <h3>Consumo Total: <?php echo number_format($resultado, 4); ?> Joules</h3>
                <p>Cálculo basado en la <u>integral definida</u> de la carga del servidor.</p>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="error">Error: <?php echo $error; ?></div>
        <?php endif; ?>
    </div>
</body>
</html>