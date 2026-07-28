-- migration_09_delete_permission.sql
-- Run this in phpMyAdmin (SQL tab, tpmc_ticketing database selected).
-- AFTER migration_08 has already been applied.
--
-- Purpose: lets IT control, per department, whether that department's
-- account is allowed to delete its own tickets. Defaults to allowed (1)
-- so existing behavior doesn't change until IT explicitly turns it off.

USE tpmc_ticketing;

ALTER TABLE users
  ADD COLUMN can_delete_tickets TINYINT(1) NOT NULL DEFAULT 1 AFTER is_active;