CREATE DATABASE IF NOT EXISTS bai_tap_ca_nhan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bai_tap_ca_nhan;

CREATE TABLE hoc_ky (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ten_hoc_ky VARCHAR(100) NOT NULL,
    ngay_bat_dau DATE NOT NULL,
    ngay_ket_thuc DATE NOT NULL
);

INSERT INTO hoc_ky (ten_hoc_ky, ngay_bat_dau, ngay_ket_thuc) VALUES
('Học kỳ 1 năm 2026-2027', '2026-09-01', '2027-01-15'),
('Học kỳ 2 năm 2026-2027', '2027-02-15', '2027-06-30'),
('Học kỳ Hè năm 2027', '2027-07-01', '2027-08-15');