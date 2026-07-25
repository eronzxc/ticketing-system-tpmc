-- migration_06_user_status.sql
-- Run this in phpMyAdmin (SQL tab, with the tpmc_ticketing database selected).
-- If you've already used migration_06 for something else, rename this file
-- to the next available number and let Claude know.

USE tpmc_ticketing;

ALTER TABLE users
  ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1 AFTER department;
