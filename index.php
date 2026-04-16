<?php
require_once 'src/Calculo/IntegradorNumerico.php';
use App\Calculo\IntegradorNumerico;

$resultado = null;
$kwh = null;
$error = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Casting de tipos y operador de coalescencia nula (??) para evitar errores de 'null'
        $integrador = new IntegradorNumerico(
            (float)($_POST['t_inicio'] ?? 0),
            (float)($_POST['t_fin'] ?? 0),
            (int)($_POST['precision'] ?? 1000),
            $_POST['perfil'] ?? 'AVERAGE' 
        );
        
        $resultado = $integrador->calcularEnergiaTotal();
        $kwh = $integrador->convertirAKWh($resultado);
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cloud Energy Monitor PRO</title>
    <!-- Favicon para la pestaña del navegador -->
    <link rel="icon" href="images/images.png" type="image/png">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <!-- Logo visual en la página -->
        <img src="images/cloud-monitoring-1.png" alt="Logo Energía" class="logo-icon">
        
        <h1>Monitor de Energía (DataCenter)</h1>
        
        <form method="POST">
            <label>Perfil de Consumo:</label>
            <select name="perfil" class="input-field">
                <option value="IDLE">IDLE (Consumo Mínimo)</option>
                <option value="AVERAGE" selected>AVERAGE (Carga Lineal)</option>
                <option value="STRESS">STRESS (Carga Máxima)</option>
            </select>

            <label>Tiempo Inicial (s):</label>
            <input type="number" name="t_inicio" step="0.1" required class="input-field">

            <label>Tiempo Final (s):</label>
            <input type="number" name="t_fin" step="0.1" required class="input-field">

            <label>Precisión (n subintervalos):</label>
            <input type="number" name="precision" value="1000" required class="input-field">

            <button type="submit">Calcular Consumo</button>
        </form>

        <?php if ($resultado !== null): ?>
            <div class="result">
                <h3><?php echo number_format($resultado, 4); ?> Joules</h3>
                <p>Equivalente a: <u><?php echo number_format($kwh, 10); ?> kWh</u></p>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="error">⚠️ Error: <?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Tabla de Análisis de Convergencia -->
        <div class="analysis-section">
            <h2>Análisis de Convergencia [0, 10]</h2>
            <p>Función: $P(t) = t^2 + 2t$ | Valor Real: <u>433.33</u></p>
            <table>
                <thead>
                    <tr>
                        <th>n (Pasos)</th>
                        <th>Resultado (Joules)</th>
                        <th>Error</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $n_values = [10, 100, 1000];
                    $real_value = 433.33;
                    foreach ($n_values as $n) {
                        $test = new IntegradorNumerico(0, 10, $n, 'ORIGINAL');
                        $res = $test->calcularEnergiaTotal();
                        $err = abs($real_value - $res);
                        echo "<tr><td>$n</td><td>" . number_format($res, 4) . "</td><td>" . number_format($err, 4) . "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>