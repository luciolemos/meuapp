<?php
declare(strict_types=1);

namespace App\Support;

final class HealthOptions
{
    private const STATES = [
        'leve' => ['label' => 'Leve', 'badge' => 'text-bg-success'],
        'inspira_cuidados' => ['label' => 'Inspira cuidados', 'badge' => 'text-bg-warning text-dark'],
        'grave' => ['label' => 'Grave', 'badge' => 'text-bg-danger'],
        'critico' => ['label' => 'Crítico', 'badge' => 'text-bg-danger'],
        'terminal' => ['label' => 'Terminal', 'badge' => 'text-bg-dark'],
        'recuperado' => ['label' => 'Recuperado', 'badge' => 'text-bg-primary'],
        'estavel' => ['label' => 'Estável', 'badge' => 'text-bg-info text-dark'],
    ];

    private const TREATMENTS = [
        'em_andamento' => 'Em andamento',
        'concluido' => 'Concluído',
        'suspenso' => 'Suspenso',
    ];

    public static function states(): array
    {
        return self::STATES;
    }

    public static function stateLabels(): array
    {
        $labels = [];
        foreach (self::STATES as $key => $info) {
            $labels[$key] = $info['label'];
        }
        return $labels;
    }

    public static function treatmentStatuses(): array
    {
        return self::TREATMENTS;
    }

    public static function defaultState(): string
    {
        return array_key_first(self::STATES);
    }

    public static function defaultTreatmentStatus(): string
    {
        return array_key_first(self::TREATMENTS);
    }
}
