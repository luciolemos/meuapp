#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\Services\DatabasePatientRepository;
use App\Services\LocalPatientStore;
use App\Support\PatientOptions;

require dirname(__DIR__) . '/vendor/autoload.php';

$source = new LocalPatientStore();
$target = new DatabasePatientRepository();

$patients = $source->all();

$imported = 0;
$skipped = 0;

foreach ($patients as $patient) {
    $email = $patient['email'] ?? null;
    if (!$email) {
        $skipped++;
        continue;
    }

    if ($target->emailExists($email)) {
        $skipped++;
        continue;
    }

    $target->create([
        'staff_id' => $patient['staff_id'] ?? null,
        'name' => $patient['name'] ?? 'Sem nome',
        'email' => $email,
        'convenio' => PatientOptions::normalizeConvenio($patient['convenio'] ?? ($patient['role'] ?? null)),
        'situacao_cadastro' => PatientOptions::normalizeSituacao($patient['situacao_cadastro'] ?? ($patient['status'] ?? null)),
        'avatar' => $patient['avatar'] ?? null,
        'birth_date' => $patient['birth_date'] ?? null,
        'cpf' => $patient['cpf'] ?? '',
        'address' => $patient['address'] ?? '',
    ]);

    $imported++;
}

echo sprintf("Pacientes importados: %d\nPacientes ignorados (já existentes ou inválidos): %d\n", $imported, $skipped);
