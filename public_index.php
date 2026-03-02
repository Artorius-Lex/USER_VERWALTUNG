<?php
/**
 * index.php - Startseite der Anwendung
 * 
 * Diese Seite:
 * 1. Prüft, ob ein Benutzer angemeldet ist
 * 2. Zeigt Navigation mit Benutzername oder Anmeldebutton
 * 3. Leitet Admins zum Dashboard weiter
 */

// Session starten
session_start();

// Prüfe, ob Benutzer angemeldet ist
$isLoggedIn = isset($_SESSION['user_id']);
$username = $_SESSION['username'] ?? '';
$isAdmin = $_SESSION['is_admin'] ?? false;

// Wenn Admin, zur Admin-Seite weiterleiten
if ($isLoggedIn && $isAdmin) {
    header('Location: admin.php');
    exit();
}

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Startseite - Benutzerverwaltung</title>
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

        nav .nav-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .nav-link {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 4px;
            transition: background 0.3s;
        }

        .nav-link:hover {
            background: #555;
        }

        .logout-btn {
            background: #d32f2f;
        }

        .logout-btn:hover {
            background: #b71c1c;
        }

        .container {
            max-width: 1000px;
            margin: 50px auto;
            padding: 0 20px;
            text-align: center;
        }

        h1 {
            color: #333;
            font-size: 36px;
            margin-bottom: 20px;
        }

        p {
            color: #666;
            font-size: 16px;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .button-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        button, a.button {
            padding: 12px 30px;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-weight: bold;
            transition: background 0.3s;
        }

        .primary-btn {
            background: #667eea;
            color: white;
        }

        .primary-btn:hover {
            background: #5568d3;
        }

        .secondary-btn {
            background: #f0f0f0;
            color: #333;
            border: 1px solid #ddd;
        }

        .secondary-btn:hover {
            background: #e0e0e0;
        }

        .welcome-message {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav>
        <h1>👤 Benutzerverwaltung</h1>
        <div class="nav-right">
            <?php if ($isLoggedIn): ?>
                <span>Angemeldet als: <strong><?php echo htmlspecialchars($username); ?></strong></span>
                <a href="logout.php" class="nav-link logout-btn">Abmelden</a>
            <?php else: ?>
                <a href="login.php" class="nav-link primary-btn">Anmelden</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container">
        <?php if ($isLoggedIn): ?>
            <!-- Willkommensbereich für angemeldete Benutzer -->
            <div class="welcome-message">
                <h1>Willkommen, <?php echo htmlspecialchars($username); ?>! 👋</h1>
                <p>Du bist erfolgreich angemeldet. Dein Konto wurde von einem Administrator aktiviert.</p>
            </div>
        <?php else: ?>
            <!-- Startbereich für nicht angemeldete Benutzer -->
            <h1>Willkommen zur Benutzerverwaltung</h1>
            <p>
                Dies ist ein sicheres System zur Verwaltung von Benutzerkonten.
                <br>
                Sie können sich registrieren, anmelden und Ihre Kontoinformationen verwalten.
            </p>
            
            <div class="button-group">
                <a href="register.php" class="button primary-btn">Registrieren</a>
                <a href="login.php" class="button secondary-btn">Anmelden</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>