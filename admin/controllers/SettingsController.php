<?php
// admin/controllers/SettingsController.php

require_once 'includes/auth_check.php';
require_once '../config/db.php';

$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'clear_transactions') {
        try {
            $conn->exec("SET FOREIGN_KEY_CHECKS = 0");
            $tables = ['order_items', 'orders', 'reviews', 'contacts', 'favorites'];
            foreach ($tables as $table) {
                $conn->exec("TRUNCATE TABLE `$table`");
            }
            $conn->exec("SET FOREIGN_KEY_CHECKS = 1");
            $msg = "Đã dọn dẹp sạch toàn bộ dữ liệu giao dịch!";
        } catch (PDOException $e) {
            $err = "Lỗi: " . $e->getMessage();
        }
    }

    if ($action === 'force_reset') {
        try {
            $conn->exec("SET FOREIGN_KEY_CHECKS = 0");

            // 1. Truncate Content Tables
            $tables = ['products', 'categories', 'posts', 'faqs', 'order_items', 'orders', 'reviews', 'contacts', 'favorites'];
            foreach ($tables as $table) {
                $conn->exec("TRUNCATE TABLE `$table`");
            }

            // 2. Delete non-admin users
            // Reset Auto Increment if possible, or just delete.
            $conn->exec("DELETE FROM users WHERE role != 'admin'");

            $conn->exec("SET FOREIGN_KEY_CHECKS = 1");

            $msg = "Hệ thống đã được RESET toàn bộ về trạng thái ban đầu (Chỉ giữ lại Admin)!";
        } catch (PDOException $e) {
            $err = "Lỗi Reset: " . $e->getMessage();
        }
    }

    if ($action === 'export_db') {
        $filename = 'backup_maubakery_' . date('Y-m-d_H-i') . '.sql';
        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Initial SQL
        echo "-- Database Backup: Mâu Bakery\n";
        echo "-- Date: " . date('Y-m-d H:i:s') . "\n";
        echo "-- Exported by: " . $_SESSION['username'] . "\n\n";

        // Create Database if not exists
        echo "CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET " . DB_CHARSET . " COLLATE " . DB_CHARSET . "_general_ci;\n";
        echo "USE `" . DB_NAME . "`;\n\n";

        echo "SET FOREIGN_KEY_CHECKS=0;\n\n";

        $tables = $conn->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            // Structure
            $row = $conn->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_NUM);
            echo "\n\n" . $row[1] . ";\n\n";

            // Data
            $rows = $conn->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                $cols = array_keys($row);
                $vals = array_values($row);

                // Escape values
                $vals = array_map(function ($v) use ($conn) {
                    if ($v === null)
                        return "NULL";
                    return $conn->quote($v);
                }, $vals);

                echo "INSERT INTO `$table` (`" . implode('`, `', $cols) . "`) VALUES (" . implode(", ", $vals) . ");\n";
            }
        }

        echo "\nSET FOREIGN_KEY_CHECKS=1;\n";
        exit;
    }
}
?>