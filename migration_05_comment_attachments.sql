-- migration_05_comment_attachments.sql
-- Run this in phpMyAdmin (SQL tab, with the tpmc_ticketing database selected).
-- If you've already run a migration_05_* before this one, rename this file
-- to the next available number (migration_06_..., etc.) to keep the order clear.

USE tpmc_ticketing;

ALTER TABLE ticket_comments
  ADD COLUMN attachments_json LONGTEXT NULL AFTER message;
