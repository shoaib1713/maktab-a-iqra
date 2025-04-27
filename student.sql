-- Add new columns to students table
ALTER TABLE students
ADD COLUMN student_address VARCHAR(255) AFTER phone,
ADD COLUMN class_time VARCHAR(50) AFTER student_address,
ADD COLUMN remarks TEXT AFTER class_time; 