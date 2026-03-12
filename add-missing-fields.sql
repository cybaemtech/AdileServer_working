-- SQL to add missing EPIC and FEATURE fields
-- Generated on 2026-03-12 09:38:06

ALTER TABLE work_items ADD COLUMN prototype_link varchar(500) NULL;
ALTER TABLE work_items ADD COLUMN drag_drop_enabled tinyint(1) NULL;
ALTER TABLE work_items ADD COLUMN pdf_upload_path varchar(500) NULL;
ALTER TABLE work_items ADD COLUMN pdf_upload_blob longblob NULL;
ALTER TABLE work_items ADD COLUMN prototype_status enum('not_started','in_progress','completed','approved') NULL;
ALTER TABLE work_items ADD COLUMN mockup_link varchar(500) NULL;
