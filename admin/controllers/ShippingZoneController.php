<?php
// admin/controllers/ShippingZoneController.php

require_once '../config/db.php';

class ShippingZoneController
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function handleRequest()
    {
        $action = $_GET['action'] ?? '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // --- CREATE ---
            if (isset($_POST['add_zone'])) {
                $name = $_POST['name'];
                $fee = $_POST['fee'];
                $is_active = isset($_POST['is_active']) ? 1 : 0; // Checkbox usually

                if (!empty($name)) {
                    $sql = "INSERT INTO shipping_zones (name, fee, is_active) VALUES (:name, :fee, :is_active)";
                    $stmt = $this->conn->prepare($sql);
                    if ($stmt->execute(['name' => $name, 'fee' => $fee, 'is_active' => $is_active])) {
                        header("Location: shipping_zones.php?success=added");
                        exit;
                    }
                }
            }

            // --- UPDATE ---
            if (isset($_POST['edit_zone'])) {
                $id = $_POST['id'];
                $name = $_POST['name'];
                $fee = $_POST['fee'];
                // For edit, we might just pass status as is or update it via toggle. 
                // But let's allow editing it here too if needed. 
                // Usually toggle is separate, but full edit form includes it.
                // Let's assume the edit form has it or we just keep it simple.
                // Checking previous TagManager, it didn't have is_active in edit form, only toggle action.
                // Let's stick to Name and Fee in edit, and toggle for status.

                $sql = "UPDATE shipping_zones SET name = :name, fee = :fee WHERE id = :id";
                $stmt = $this->conn->prepare($sql);
                if ($stmt->execute(['name' => $name, 'fee' => $fee, 'id' => $id])) {
                    header("Location: shipping_zones.php?success=updated");
                    exit;
                }
            }
        }

        // --- DELETE ---
        if ($action === 'delete' && isset($_GET['id'])) {
            $id = $_GET['id'];
            $stmt = $this->conn->prepare("DELETE FROM shipping_zones WHERE id = :id");
            if ($stmt->execute(['id' => $id])) {
                header("Location: shipping_zones.php?success=deleted");
                exit;
            }
        }

        // --- TOGGLE STATUS ---
        if ($action === 'toggle' && isset($_GET['id'])) {
            $id = $_GET['id'];
            $stmt = $this->conn->prepare("SELECT is_active FROM shipping_zones WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $curr = $stmt->fetchColumn();
            $new = $curr ? 0 : 1;

            $update = $this->conn->prepare("UPDATE shipping_zones SET is_active = :new WHERE id = :id");
            $update->execute(['new' => $new, 'id' => $id]);
            header("Location: shipping_zones.php");
            exit;
        }
    }

    public function getZones()
    {
        $stmt = $this->conn->prepare("SELECT * FROM shipping_zones ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
