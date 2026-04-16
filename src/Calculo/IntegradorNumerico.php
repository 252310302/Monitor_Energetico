<?php
namespace App\Calculo;

use InvalidArgumentException;

class IntegradorNumerico {
    // Propiedades privadas para aplicar Encapsulamiento
    private float $inicio; 
    private float $fin;    
    private int $pasos;    
    private string $perfil; 

    public function __construct(float $a, float $b, int $n = 1000, string $perfil = 'AVERAGE') {
        // Manejo de Excepciones
        if ($a >= $b) {
            throw new InvalidArgumentException("El tiempo inicial debe ser menor al final.");
        }
        if ($n <= 0) {
            throw new InvalidArgumentException("La precisión (n) debe ser un número positivo.");
        }

        $this->inicio = $a;
        $this->fin = $b;
        $this->pasos = $n;
        $this->perfil = $perfil;
    }

    /**
     * Modela la función de potencia P(t) según el perfil seleccionado
     */
    private function funcionPotencia(float $t): float {
        return match($this->perfil) {
            'IDLE'     => 5.0,                      // Consumo mínimo constante
            'AVERAGE'  => (2 * $t) + 5,             // Carga lineal creciente
            'STRESS'   => pow($t, 2),               // Carga exponencial
            'ORIGINAL' => pow($t, 2) + (2 * $t),    // Función original del proyecto
            default    => 0.0,
        };
    }

    /**
     * Implementación de la Regla del Trapecio
     */
    public function calcularEnergiaTotal(): float {
        $h = ($this->fin - $this->inicio) / $this->pasos;
        
        // Fórmula: (f(a) + f(b)) / 2
        $suma = ($this->funcionPotencia($this->inicio) + $this->funcionPotencia($this->fin)) / 2;

        for ($i = 1; $i < $this->pasos; $i++) {
            $t_i = $this->inicio + $i * $h;
            $suma += $this->funcionPotencia($t_i);
        }

        return $suma * $h;
    }

    /**
     * Convierte Joules a Kilovatios-hora (kWh)
     */
    public function convertirAKWh(float $joules): float {
        return $joules * 2.7778e-7; 
    }
}