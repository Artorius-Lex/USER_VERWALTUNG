<?php
/**
 * admin.php - Admin-Verwaltungsseite für alle Benutzer
 * 
 * Dieser Script:
 * 1. Prüft, ob der Benutzer angemeldet ist und Admin-Rechte hat
 * 2. Zeigt alle registrierten Benutzer in einer Tabelle
 * 3. Erlaubt dem Admin, Benutzer zu aktivieren/deaktivieren
 * 4. Erlaubt dem Admin, Admin-Rechte zu vergeben/entziehen
 */

// Session starten
session_start();

// Autoloader laden
require_once __DIR__ . '/../vendor/autoload.php';

// Prüfe, ob Benutzer angemeldet ist
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Prüfe, ob Benutzer Admin ist
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] === false) {
    die("Zugriff verweigert: Sie sind kein Administrator.");
}

// ===== UPDATE-LOGIK =====
// Wenn ein Benutzer aktiviert/deaktiviert oder zum Admin gemacht wird

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id'] ?? 0);
    $action = $_POST['action'] ?? '';
    
    try {
        // Benutzer aus Datenbank holen
        // $user = $entityManager->find(User::class, $user_id);
        
        if ($user) {
            // Benutzer aktivieren/deaktivieren
            if ($action === 'toggle_active') {
                $user->setIsActive(!$user->isActive());
                // $entityManager->flush();
            }
            // Admin-Rechte vergeben/entziehen
            elseif ($action === 'toggle_admin') {
                $user->setIsAdmin(!$user->isAdmin());
                // $entityManager->flush();
            }
        }
    } catch (\Exception $e) {
        $error = "Fehler beim Aktualisieren des Benutzers: " . $e->getMessage();
    }
}

// ===== ALLE BENUTZER LADEN =====
// $users = $entityManager->getRepository(User::class)->findAll();

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin-Dashboard - Benutzerverwaltung</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: #f5f5f5;
        }

        /* Navigation */
        nav {
            background: #333;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        nav h1 {
            font-size: 20px;
            margin: 0;
        }

        nav .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        nav .logout-btn {
            background: #d32f2f;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-weight: bold;
        }

        nav .logout-btn:hover {
            background: #b71c1c;
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 24px;
        }

        .table-wrapper {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #f9f9f9;
            border-bottom: 2px solid #ddd;
        }

        th {
            padding: 15px;
            text-align: left;
            font-weight: bold;
            color: #333;
            border-right: 1px solid #eee;
        }

        th:last-child {
            border-right: none;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            border-right: 1px solid #eee;
        }

        td:last-child {
            border-right: none;
        }

        tbody tr:hover {
            background: #f9f9f9;
        }

        /* Checkboxen in der Tabelle */
        .checkbox-cell {
            text-align: center;
        }

        input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        /* Status-Badges */
        .badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }

        .badge.active {
            background: #c8e6c9;
            color: #2e7d32;
        }

        .badge.inactive {
            background: #ffcccc;
            color: #c62828;
        }

        .badge.admin {
            background: #bbdefb;
            color: #1565c0;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
        }

        .error {
            color: #d32f2f;
            background: #ffebee;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 4px solid #d32f2f;
        }
    </style>
</head>
<body>
    <!-- Navigation mit Benutzer-Info -->
    <nav>
        <h1>📊 Admin-Dashboard</h1>
        <div class="user-info">
            <span>Angemeldet als: <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span>
            <a href="logout.php" class="logout-btn">Abmelden</a>
        </div>
    </nav>

    <div class="container">
        <h2>Benutzerverwaltung</h2>

        <!-- Fehler anzeigen -->
        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <!-- Benutzertabelle -->
        <div class="table-wrapper">
            <?php if (empty($users)): ?>
                <div class="empty-state">
                    <p>Keine Benutzer registriert.</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Benutzername</th>
                            <th>E-Mail</th>
                            <th>Registriert am</th>
                            <th>Status</th>
                            <th>Aktiviert</th>
                            <th>Administrator</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <!-- Benutzername -->
                                <td>
                                    <?php echo htmlspecialchars($user->getUsername()); ?>
                                </td>

                                <!-- E-Mail -->
                                <td>
                                    <?php echo htmlspecialchars($user->getEmail()); ?>
                                </td>

                                <!-- Registrierungsdatum -->
                                <td>
                                    <?php 
                                        $createdAt = $user->getCreatedAt();
                                        echo $createdAt ? $createdAt->format('d.m.Y H:i') : 'N/A';
                                    ?>
                                </td>

                                <!-- Aktivierungsstatus -->
                                <td>
                                    <?php if ($user->isActive()): ?>
                                        <span class="badge active">✓ Aktiv</span>
                                    <?php else: ?>
                                        <span class="badge inactive">✗ Inaktiv</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Aktivierungscheckbox -->
                                <td class="checkbox-cell">
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="user_id" value="<?php echo $user->getId(); ?>">
                                        <input type="hidden" name="action" value="toggle_active">
                                        <input 
                                            type="checkbox" 
                                            <?php if ($user->isActive()) echo 'checked'; ?>
                                            onchange="this.form.submit()"
                                            title="Klicken um zu aktivieren/deaktivieren"
                                        >
                                    </form>
                                </td>

                                <!-- Admin-Checkbox -->
                                <td class="checkbox-cell">
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="user_id" value="<?php echo $user->getId(); ?>">
                                        <input type="hidden" name="action" value="toggle_admin">
                                        <input 
                                            type="checkbox" 
                                            <?php if ($user->isAdmin()) echo 'checked'; ?>
                                            onchange="this.form.submit()"
                                            title="Klicken um Admin-Rechte zu vergeben"
                                        >
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>