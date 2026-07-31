-- migration_12_login_attempts.sql
-- Patakbuhin sa phpMyAdmin (SQL tab, tpmc_ticketing database).
--
-- Layunin: i-track ang failed login attempts per username, para may
-- rate-limiting/lockout tayo — dati, walang limitasyon kung ilang beses
-- pwedeng subukan ang password ng isang account (brute-force risk),
-- lalo na ngayong shared/kilalang username na ang bawat department
-- (hal. "pharmacy", "laboratory") kaya isang password na lang ang
-- kailangang tuklasin.
--
-- Bawat FAILED attempt lang ang naka-log dito (hindi successful logins).
-- Kapag umabot ng 5 failed attempts ang isang username sa loob ng 15
-- minuto, naka-lock muna siya ng 15 minuto bago makapag-try ulit —
-- makikita sa auth/login.php ang buong logic.

USE tpmc_ticketing;

CREATE TABLE IF NOT EXISTS login_attempts (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  username      VARCHAR(120)  NOT NULL,
  attempted_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX (username, attempted_at)
) ENGINE=InnoDB;
