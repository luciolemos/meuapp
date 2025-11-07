-- =============================================================
-- Script: scripts/sql/update_patients_convenio.sql
-- Objetivo:
--   - Renomear as colunas `role` -> `convenio` e `status` -> `situacao_cadastro`
--   - Ajustar tipos e índices para refletir as novas nomenclaturas
--   - Normalizar dados existentes para os novos valores aceitos
-- =============================================================

START TRANSACTION;

ALTER TABLE `tbl_meuapp_patients`
    CHANGE COLUMN `role` `convenio` VARCHAR(80) NOT NULL DEFAULT 'particular',
    CHANGE COLUMN `status` `situacao_cadastro` ENUM('ativo','incompleto','suspenso','cancelado') NOT NULL DEFAULT 'ativo';

ALTER TABLE `tbl_meuapp_patients`
    RENAME INDEX `idx_tbl_meuapp_patients_role` TO `idx_tbl_meuapp_patients_convenio`,
    RENAME INDEX `idx_tbl_meuapp_patients_status` TO `idx_tbl_meuapp_patients_situacao`;

UPDATE `tbl_meuapp_patients`
   SET `convenio` = CASE
       WHEN `convenio` IS NULL OR TRIM(`convenio`) = '' THEN 'particular'
       WHEN LOWER(`convenio`) IN ('admin', 'editor') THEN 'particular'
       WHEN LOWER(`convenio`) = 'viewer' THEN 'sus'
       ELSE LOWER(`convenio`)
   END;

UPDATE `tbl_meuapp_patients`
   SET `situacao_cadastro` = CASE
       WHEN `situacao_cadastro` IS NULL OR TRIM(`situacao_cadastro`) = '' THEN 'ativo'
       WHEN LOWER(`situacao_cadastro`) = 'active' THEN 'ativo'
       WHEN LOWER(`situacao_cadastro`) = 'invited' THEN 'incompleto'
       WHEN LOWER(`situacao_cadastro`) = 'suspended' THEN 'suspenso'
       ELSE LOWER(`situacao_cadastro`)
   END;

COMMIT;
