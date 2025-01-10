CREATE DATABASE esp32_qrdb;

USE esp32_qrdb;

CREATE TABLE qr_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    device_id VARCHAR(50) NOT NULL,
    qr_code_data TEXT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
