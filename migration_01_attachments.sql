-- migration_01_attachments.sql
-- Patakbuhin ito sa phpMyAdmin (SQL tab, habang naka-select yung tpmc_ticketing database)
-- kasi na-import mo na yung schema.sql bago ito idinagdag.

USE tpmc_ticketing;

ALTER TABLE tickets
  ADD COLUMN attachments_json LONGTEXT NULL AFTER due_date;
