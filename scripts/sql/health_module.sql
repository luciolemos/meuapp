-- =============================================================
-- Script: scripts/sql/health_module.sql
-- Descrição:
--   Cria a tabela `tbl_meuapp_health` para acompanhamento médico
--   dos pacientes e ajusta a tabela `tbl_meuapp_patients` para armazenar
--   metadados do último atendimento.
-- =============================================================

START TRANSACTION;

-- -------------------------------------------------------------
-- 1) Tabela de prontuários / atendimentos médicos
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tbl_meuapp_health` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `consulta_data` DATETIME NOT NULL,
    `estado` ENUM('leve','inspira_cuidados','grave','critico','terminal','recuperado','estavel') NOT NULL DEFAULT 'leve',
    `diagnostico` VARCHAR(255) NOT NULL,
    `cid` VARCHAR(10) DEFAULT NULL,
    `tratamento` TEXT DEFAULT NULL,
    `prescricoes` TEXT DEFAULT NULL,
    `observacoes` TEXT DEFAULT NULL,
    `medico_responsavel` VARCHAR(150) DEFAULT NULL,
    `instituicao` VARCHAR(150) DEFAULT NULL,
    `pressao_arterial` VARCHAR(7) DEFAULT NULL,
    `frequencia_cardiaca` TINYINT UNSIGNED DEFAULT NULL,
    `temperatura_c` DECIMAL(4,1) DEFAULT NULL,
    `peso_kg` DECIMAL(5,2) DEFAULT NULL,
    `altura_m` DECIMAL(4,2) DEFAULT NULL,
    `proxima_consulta` DATETIME DEFAULT NULL,
    `status_tratamento` ENUM('em_andamento','concluido','suspenso') DEFAULT 'em_andamento',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_health_patient` (`user_id`),
    INDEX `idx_health_estado` (`estado`),
    INDEX `idx_health_cid` (`cid`),
    CONSTRAINT `fk_health_user`
        FOREIGN KEY (`user_id`)
        REFERENCES `tbl_meuapp_patients` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 2) Ajustes na tabela de usuários para metadados de saúde
-- -------------------------------------------------------------
ALTER TABLE `tbl_meuapp_patients`
    ADD COLUMN `last_consulta_em` DATETIME NULL AFTER `address`,
    ADD COLUMN `last_health_state` ENUM('leve','inspira_cuidados','grave','critico','terminal','recuperado','estavel') NULL AFTER `last_consulta_em`,
    ADD COLUMN `last_cid` VARCHAR(10) NULL AFTER `last_health_state`,
    ADD COLUMN `ultima_clinica` VARCHAR(150) NULL AFTER `last_cid`,
    ADD INDEX `idx_tbl_meuapp_patients_last_state` (`last_health_state`);

COMMIT;
