ALTER TABLE tickets ADD COLUMN assigned_to INT NULL AFTER created_by;

ALTER TABLE notifications MODIFY type ENUM('new_ticket', 'due_soon', 'in_progress', 'resolved', 'assigned') NOT NULL;
