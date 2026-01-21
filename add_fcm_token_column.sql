-- Run this SQL to add fcm_token column to users table
ALTER TABLE users ADD COLUMN fcm_token VARCHAR(255) DEFAULT NULL;
