-- Add assigned_staff column to complaints table
ALTER TABLE complaints ADD COLUMN assigned_staff VARCHAR(100) DEFAULT NULL;
