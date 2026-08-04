-- migration_13_delete_permission_default.sql
-- Run this in phpMyAdmin (SQL tab, tpmc_ticketing database selected).
-- AFTER migration_09_delete_permission has already been applied.
--
-- Purpose: migration_09 set can_delete_tickets to default ALLOWED (1) at
-- the column level, and users/create.php separately hardcodes 0 (not
-- allowed) whenever a NEW department account is created. That works, but
-- only because every account-creation code path happens to remember to
-- pass 0 explicitly — if a future change (or a direct DB insert/import)
-- ever creates a user row without setting that column, it would silently
-- fall back to "allowed" again.
--
-- This flips the column's own default to 0 (not allowed), so new
-- department accounts are safe-by-default no matter which code path
-- creates them. This does NOT change any existing account's current
-- setting — only the default used when the column is left unspecified.

USE tpmc_ticketing;

ALTER TABLE users
  MODIFY COLUMN can_delete_tickets TINYINT(1) NOT NULL DEFAULT 0;
