-- migration_04_ticket_remarks.sql
-- Run this in phpMyAdmin (SQL tab, with the tpmc_ticketing database
-- selected) after migration_01, migration_02, and migration_03.
--
-- Purpose: adds a "remarks" field IT can use for internal notes about a
-- ticket (e.g. root cause, parts replaced, etc.), separate from the
-- reply thread that the requester sees.

USE tpmc_ticketing;

ALTER TABLE tickets
  ADD COLUMN remarks TEXT NULL AFTER resolved_by;
