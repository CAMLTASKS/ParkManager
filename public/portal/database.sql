CREATE DATABASE IF NOT EXISTS parkmanager_portal
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE parkmanager_portal;

CREATE TABLE IF NOT EXISTS portal_tickets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    source_ticket_id VARCHAR(80) NULL,
    ticket_code VARCHAR(80) NOT NULL,
    barcode VARCHAR(120) NULL,
    plate VARCHAR(30) NOT NULL,
    vehicle_type VARCHAR(40) NOT NULL,
    status VARCHAR(40) NOT NULL,
    location_number INT NULL,
    site_name VARCHAR(160) NULL,
    tariff_name VARCHAR(160) NULL,
    tariff_type VARCHAR(40) NULL,
    entry_time DATETIME NULL,
    exit_time DATETIME NULL,
    payment_method VARCHAR(40) NULL,
    payment_status VARCHAR(40) NULL,
    paid_at DATETIME NULL,
    subtotal INT NOT NULL DEFAULT 0,
    discount INT NOT NULL DEFAULT 0,
    surcharge INT NOT NULL DEFAULT 0,
    tax INT NOT NULL DEFAULT 0,
    total INT NOT NULL DEFAULT 0,
    duration_minutes INT NOT NULL DEFAULT 0,
    last_synced_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    UNIQUE KEY portal_tickets_ticket_code_unique (ticket_code),
    KEY portal_tickets_plate_index (plate),
    KEY portal_tickets_entry_time_index (entry_time),
    KEY portal_tickets_paid_at_index (paid_at),
    KEY portal_tickets_status_index (status),
    KEY portal_tickets_vehicle_type_index (vehicle_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS portal_events (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    event_type VARCHAR(60) NOT NULL,
    ticket_code VARCHAR(80) NOT NULL,
    payload JSON NULL,
    event_time DATETIME NULL,
    created_at DATETIME NOT NULL,
    KEY portal_events_ticket_code_index (ticket_code),
    KEY portal_events_event_type_index (event_type),
    KEY portal_events_event_time_index (event_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS portal_users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    username VARCHAR(80) NOT NULL,
    password_hash VARCHAR(120) NOT NULL,
    role VARCHAR(30) NOT NULL DEFAULT 'viewer',
    active TINYINT(1) NOT NULL DEFAULT 1,
    last_login_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    UNIQUE KEY portal_users_username_unique (username),
    KEY portal_users_role_index (role),
    KEY portal_users_active_index (active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO portal_users (name, username, password_hash, role, active, created_at, updated_at)
VALUES ('Administrador', 'admin', SHA2('123Alejandro.', 256), 'admin', 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE username = username;
