CREATE TABLE IF NOT EXISTS admins (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_login_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS applications (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  reference_no VARCHAR(32) NULL UNIQUE,
  status VARCHAR(32) NOT NULL DEFAULT 'new',
  first_name VARCHAR(100) NOT NULL,
  last_name VARCHAR(100) NOT NULL,
  birth_date DATE NULL,
  gender VARCHAR(30) NULL,
  nationality VARCHAR(100) NULL,
  country VARCHAR(100) NOT NULL,
  region VARCHAR(100) NULL,
  city VARCHAR(100) NULL,
  neighborhood VARCHAR(120) NULL,
  address VARCHAR(255) NULL,
  phone VARCHAR(40) NOT NULL,
  whatsapp_number VARCHAR(40) NULL,
  email VARCHAR(190) NOT NULL,
  employment_status VARCHAR(100) NULL,
  education_level VARCHAR(100) NULL,
  has_activity TINYINT(1) NOT NULL DEFAULT 0,
  company_name VARCHAR(190) NULL,
  company_created DATE NULL,
  company_sector VARCHAR(120) NULL,
  company_description TEXT NULL,
  company_location VARCHAR(190) NULL,
  employee_count INT UNSIGNED NULL,
  turnover VARCHAR(100) NULL,
  registration_number VARCHAR(100) NULL,
  promoter_experience TEXT NULL,
  project_name VARCHAR(190) NOT NULL,
  project_sector VARCHAR(120) NOT NULL,
  project_subsector VARCHAR(120) NULL,
  project_country VARCHAR(100) NOT NULL,
  project_region VARCHAR(100) NULL,
  project_city VARCHAR(100) NULL,
  project_location VARCHAR(190) NULL,
  project_description TEXT NOT NULL,
  project_objective TEXT NULL,
  project_problem TEXT NOT NULL,
  project_solution TEXT NOT NULL,
  target_customers TEXT NULL,
  competitors TEXT NULL,
  competitive_advantage TEXT NULL,
  current_jobs INT UNSIGNED NULL,
  jobs_to_create INT UNSIGNED NULL,
  project_stage VARCHAR(50) NOT NULL,
  amount_requested DECIMAL(16,2) NOT NULL,
  currency VARCHAR(8) NOT NULL,
  personal_contribution DECIMAL(16,2) NOT NULL DEFAULT 0,
  total_cost DECIMAL(16,2) NOT NULL,
  fund_uses JSON NOT NULL,
  fund_use_details TEXT NOT NULL,
  consent_accuracy TINYINT(1) NOT NULL DEFAULT 0,
  consent_privacy TINYINT(1) NOT NULL DEFAULT 0,
  consent_processing TINYINT(1) NOT NULL DEFAULT 0,
  internal_notes TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_application_status (status),
  INDEX idx_application_sector (project_sector),
  INDEX idx_application_date (created_at),
  INDEX idx_application_country (project_country),
  INDEX idx_application_contact (email, phone)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS application_history (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  application_id BIGINT UNSIGNED NOT NULL,
  admin_id BIGINT UNSIGNED NULL,
  event_type VARCHAR(40) NOT NULL,
  old_value VARCHAR(190) NULL,
  new_value VARCHAR(190) NULL,
  description VARCHAR(500) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_history_application FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
  CONSTRAINT fk_history_admin FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE SET NULL,
  INDEX idx_history_application (application_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS testimonials (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(190) NOT NULL,
  country VARCHAR(100) NOT NULL,
  city VARCHAR(100) NULL,
  activity VARCHAR(190) NULL,
  quote TEXT NOT NULL,
  photo_url VARCHAR(500) NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 0,
  published_at DATE NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS settings (
  setting_key VARCHAR(100) NOT NULL PRIMARY KEY,
  setting_value TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS contact_messages (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(190) NOT NULL,
  email VARCHAR(190) NOT NULL,
  subject VARCHAR(190) NOT NULL,
  message TEXT NOT NULL,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_contact_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO settings (setting_key, setting_value) VALUES
('contact_email', ''), ('phone', ''), ('whatsapp', ''), ('address', ''),
('facebook', ''), ('instagram', ''), ('linkedin', ''), ('website', '');
