-- migration_02_ticket_owner.sql
-- Patakbuhin ito sa phpMyAdmin (SQL tab, habang naka-select yung tpmc_ticketing database)
-- kasi na-import mo na yung schema.sql at migration_01_attachments.sql bago ito idinagdag.
--
-- Layunin: i-link yung bawat ticket sa TOTOONG account (user id) na gumawa nito,
-- para malaman kung sino talaga ang pwedeng sumagot sa ticket (owner-only reply).

USE tpmc_ticketing;

ALTER TABLE tickets
  ADD COLUMN created_by INT NULL AFTER resolved_by,
  ADD CONSTRAINT fk_tickets_created_by
    FOREIGN KEY (created_by) REFERENCES users(id)
    ON DELETE SET NULL;

-- Note: yung mga LUMANG tickets (bago ma-apply itong migration) ay magkakaroon
-- ng created_by = NULL, dahil wala talagang naka-link na account dati.
-- Ibig sabihin: yung mga lumang tickets na 'yun, IT na lang ang makakareply sa kanila
-- (walang matching owner). Bagong tickets mula ngayon, tama na ang owner.
