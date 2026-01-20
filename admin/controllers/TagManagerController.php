<?php
// admin/controllers/TagManagerController.php

require_once '../config/db.php';

class TagManagerController
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function handleRequest()
    {
        $action = $_GET['action'] ?? '';
        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // --- CREATE ---
            if (isset($_POST['add_tag'])) {
                $name = $_POST['name'];
                $url = $_POST['url'];
                $icon = $_POST['icon'];
                $sort = $_POST['sort_order'];

                if (!empty($name) && !empty($url)) {
                    $sql = "INSERT INTO featured_tags (name, url, icon, sort_order) VALUES (:name, :url, :icon, :sort)";
                    $stmt = $this->conn->prepare($sql);
                    if ($stmt->execute(['name' => $name, 'url' => $url, 'icon' => $icon, 'sort' => $sort])) {
                        header("Location: tags.php?success=added");
                        exit;
                    }
                }
            }

            // --- UPDATE ---
            if (isset($_POST['edit_tag'])) {
                $id = $_POST['id'];
                $name = $_POST['name'];
                $url = $_POST['url'];
                $icon = $_POST['icon'];
                $sort = $_POST['sort_order'];

                $sql = "UPDATE featured_tags SET name = :name, url = :url, icon = :icon, sort_order = :sort WHERE id = :id";
                $stmt = $this->conn->prepare($sql);
                if ($stmt->execute(['name' => $name, 'url' => $url, 'icon' => $icon, 'sort' => $sort, 'id' => $id])) {
                    header("Location: tags.php?success=updated");
                    exit;
                }
            }
        }

        // --- DELETE ---
        if ($action === 'delete' && isset($_GET['id'])) {
            $id = $_GET['id'];
            $stmt = $this->conn->prepare("DELETE FROM featured_tags WHERE id = :id");
            if ($stmt->execute(['id' => $id])) {
                header("Location: tags.php?success=deleted");
                exit;
            }
        }

        // --- TOGGLE STATUS ---
        if ($action === 'toggle' && isset($_GET['id'])) {
            $id = $_GET['id'];
            // Get current
            $stmt = $this->conn->prepare("SELECT is_active FROM featured_tags WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $curr = $stmt->fetchColumn();
            $new = $curr ? 0 : 1;

            $update = $this->conn->prepare("UPDATE featured_tags SET is_active = :new WHERE id = :id");
            $update->execute(['new' => $new, 'id' => $id]);
            header("Location: tags.php");
            exit;
        }
    }

    public function getTags()
    {
        $stmt = $this->conn->prepare("SELECT * FROM featured_tags ORDER BY sort_order ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategories()
    {
        // Helper to populate dropdown
        $stmt = $this->conn->prepare("SELECT id, name, slug FROM categories WHERE is_active = 1");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProducts()
    {
        $stmt = $this->conn->prepare("SELECT id, name FROM products WHERE is_active = 1 ORDER BY name ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
