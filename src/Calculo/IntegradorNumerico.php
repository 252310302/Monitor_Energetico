<?php
namespace App\Calculo;

use InvalidArgumentException;

class IntegradorNumerico {
    // Propiedades privadas para garantizar el encapsulamiento
    private float $inicio; // Límite inferior (segundos)
    private float $fin;    // Límite superior (segundos)
    private int $pasos;    // Precisión (n subintervalos)

    public function __construct(float $a, float $b, int $n = 1000) {
        // Manejo de Excepciones: Validación de los límites de tiempo
        if ($a >= $b) {
            throw new InvalidArgumentException("El tiempo inicial debe ser menor al final.");
        }

        // Manejo de Excepciones: Validación de la precisión
        if ($n <= 0) {
            throw new InvalidArgumentException("La precisión (n) debe ser un número positivo.");
        }

        // Asignación de valores a las propiedades privadas
        $this->inicio = $a;
        $this->fin = $b;
        $this->pasos = $n;
    }

    /**
     * Modela la función de potencia P(t) = t^2 + 2t
     * Esta función es privada ya que es una regla interna del cálculo de potencia.
     */
    private function funcionPotencia(float $t): float {
        return pow($t, 2) + (2 * $t);
    }

    /**
     * Calcula la Energía Total utilizando la Regla del Trapecio.
     * La energía es la integral de la potencia respecto al tiempo.
     */
    public function calcularEnergiaTotal(): float {
        // Cálculo del ancho del intervalo (h)
        $h = ($this->fin - $this->inicio) / $this->pasos;

        // Aplicación de la fórmula del Trapecio: (f(a) + f(b)) / 2
        $suma = ($this->funcionPotencia($this->inicio) + $this->funcionPotencia($this->fin)) / 2;

        // Sumatoria de los puntos intermedios
        for ($i = 1; $i < $this->pasos; $i++) {
            $t_i = $this->inicio + $i * $h;
            $suma += $this->funcionPotencia($t_i);
        }

        // Resultado final: Sumatoria multiplicada por el ancho del intervalo
        return $suma * $h;
    }
}