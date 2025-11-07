<?php
declare(strict_types=1);

namespace App\Support;

/**
 * Centraliza opções exibidas e armazenadas para pacientes,
 * garantindo consistência entre camada web e persistência.
 */
final class PatientOptions
{
    public const CONVENIOS = [
        'particular' => 'Particular',
        'sus' => 'SUS',
        'fusex' => 'FUSEX',
        'unimed' => 'Unimed',
        'hapvida_notredame_intermedica' => 'Hapvida Notredame Intermédica',
        'bradesco_saude' => 'Bradesco Saúde',
        'amil' => 'Amil',
        'sulamerica_saude' => 'SulAmérica Saúde',
        'prevent_senior' => 'Prevent Senior',
        'plansaude' => 'Plansaúde',
        'outro' => 'Outro Convênio',
    ];

    public const SITUACOES = [
        'ativo' => 'Ativo',
        'incompleto' => 'Incompleto',
        'suspenso' => 'Suspenso',
        'cancelado' => 'Cancelado',
    ];

    public static function defaultConvenio(): string
    {
        return array_key_first(self::CONVENIOS);
    }

    public static function defaultSituacao(): string
    {
        return array_key_first(self::SITUACOES);
    }

    public static function normalizeConvenio(?string $value): string
    {
        $value = $value ?? '';
        $slug = strtolower(trim($value));

        if ($slug !== '' && isset(self::CONVENIOS[$slug])) {
            return $slug;
        }

        foreach (self::CONVENIOS as $key => $label) {
            if (strcasecmp($label, $value) === 0) {
                return $key;
            }
        }

        return self::defaultConvenio();
    }

    public static function normalizeSituacao(?string $value): string
    {
        $value = $value ?? '';
        $slug = strtolower(trim($value));

        return isset(self::SITUACOES[$slug]) ? $slug : self::defaultSituacao();
    }
}
