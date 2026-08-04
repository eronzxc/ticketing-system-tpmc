-- migration_12_login_attempts.sql
-- Run this in phpMyAdmin (SQL tab, tpmc_ticketing database selected).
--
-- Purpose: track failed login attempts per username so we can add
-- rate-limiting/lockout — previously there was no limit on how many
-- times a password could be tried against an account (brute-force
-- risk), especially now that each department uses a shared, predictable
-- username (e.g. "pharmacy", "laboratory"), meaning an attacker only
-- has to guess one password.
--
-- Only FAILED attempts are logged here (not successful logins). Once a
-- username hits 5 failed attempts within 15 minutes, it gets locked for
-- 15 minutes before it can be tried again — the full logic lives in
-- auth/login.php.

USE tpmc_ticketing;

CREATE TABLE IF NOT EXISTS login_attempts (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  username      VARCHAR(120)  NOT NULL,
  attempted_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX (username, attempted_at)
) ENGINE=InnoDB;
