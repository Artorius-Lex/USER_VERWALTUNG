<?php
/**
 * register.php - Verarbeitet die Benutzerregistrierung
 * 
 * Dieser Script:
 * 1. Empfängt die Registrierungsdaten aus dem Formular
 * 2. Validiert die Eingaben
 * 3. Hasht das Passwort mit einem Salt
 * 4. Speichert den Benutzer in der Datenbank (isActive=false)
 */

// Autoloader für Klassen laden (prüfe mehrere Pfade)
$vendor1 = __DIR__ . '/../vendor/autoload.php';
$vendor2 = __DIR__ . '/vendor/autoload.php';
if (file_exists($vendor1)) {
    require_once $vendor1;
} elseif (file_exists($vendor2)) {
    require_once $vendor2;
} else {
    http_response_code(500);
    echo 'Autoloader nicht gefunden. Bitte Composer ausführen oder den `vendor`-Ordner bereitstellen.';
    exit;
}

use App\Entity\User;
use App\Security\PasswordHasher;

// Fehler und Erfolgsmeldungen initialisieren
$error = null;
$success = null;

// Nur POST-Requests verarbeiten (Formulare verwenden POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Eingabedaten abrufen und Whitespace entfernen
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';

    // ===== VALIDIERUNG =====
    
    // Sind alle Felder gefüllt?
    if (empty($username) || empty($email) || empty($password) || empty($passwordConfirm)) {
        $error = "Bitte füllen Sie alle Felder aus.";
    }
    // Passwörter stimmen überein?
    elseif ($password !== $passwordConfirm) {
        $error = "Die Passwörter stimmen nicht überein.";
    }
    // Benutzername lang genug?
    elseif (strlen($username) < 3) {
        $error = "Der Benutzername muss mindestens 3 Zeichen lang sein.";
    }
    // Passwort lang genug?
    elseif (strlen($password) < 6) {
        $error = "Das Passwort muss mindestens 6 Zeichen lang sein.";
    }
    // E-Mail Format korrekt?
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Bitte geben Sie eine gültige E-Mail-Adresse ein.";
    }
    // Keine Fehler gefunden - Benutzer erstellen
    else {
        try {
            // Doctrine EntityManager laden (später in echter Symfony-App)
            // Hier verwenden wir direkte Datenbankverbindung
            
            // 1. Salt generieren
            $salt = PasswordHasher::generateSalt();
            
            // 2. Passwort mit Salt hashen
            $hashedPassword = PasswordHasher::hashPassword($password, $salt);
            
            // 3. Neue User-Entity erstellen
            $user = new User();
            $user->setUsername($username);
            $user->setEmail($email);
            $user->setPassword($hashedPassword);
            $user->setSalt($salt);
            $user->setIsActive(false);  // Standardmäßig inaktiv
            $user->setIsAdmin(false);   // Standardmäßig kein Admin
            $user->setCreatedAt(new \DateTimeImmutable());
            
            // 4. In Datei speichern (einfache JSON-basierte Speicherung)
            $dataDir = __DIR__ . '/data';
            if (!is_dir($dataDir)) {
                mkdir($dataDir, 0755, true);
            }
            $usersFile = $dataDir . '/users.json';

            $users = [];
            if (file_exists($usersFile)) {
                $json = file_get_contents($usersFile);
                $users = json_decode($json, true) ?: [];
            }

            // einfache Auto-Increment ID
            $nextId = 1;
            foreach ($users as $u) {
                if (!empty($u['id']) && $u['id'] >= $nextId) {
                    $nextId = $u['id'] + 1;
                }
            }

            $userData = [
                'id' => $nextId,
                'username' => $username,
                'email' => $email,
                'password' => $hashedPassword,
                'salt' => $salt,
                'isActive' => false,
                'isAdmin' => false,
                'createdAt' => (new \DateTimeImmutable())->format(DATE_ATOM),
            ];

            $users[] = $userData;
            file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);

            // Erfolgreiche Registrierung
            $success = "Registrierung erfolgreich! Ihr Konto muss noch von einem Administrator aktiviert werden.";
            
            // Optional: Zum Login weiterleiten
            // header('Location: login.html');
            // exit();
            
        } catch (\Exception $e) {
            $error = "Fehler bei der Registrierung: " . $e->getMessage();
        }
    }
}

// HTML-Datei laden und Fehlermeldungen einfügen
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrierung - Benutzerverwaltung</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }

        h1 {
            color: #333;
            margin-bottom: 30px;
            text-align: center;
            font-size: 28px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            color: #555;
            margin-bottom: 8px;
            font-weight: bold;
            font-size: 14px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.3);
        }

        button {
            width: 100%;
            padding: 12px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background: #5568d3;
        }

        .error {
            color: #d32f2f;
            background: #ffebee;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 4px solid #d32f2f;
        }

        .success {
            color: #388e3c;
            background: #e8f5e9;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 4px solid #388e3c;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }

        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: bold;
        }

        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Registrierung</h1>

        <!-- Fehler oder Erfolg anzeigen -->
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <div class="form-group">
                <label for="username">Benutzername</label>
                <input type="text" id="username" name="username" required minlength="3" maxlength="100" placeholder="Mindestens 3 Zeichen">
            </div>

            <div class="form-group">
                <label for="email">E-Mail</label>
                <input type="email" id="email" name="email" required placeholder="example@example.com">
            </div>

            <div class="form-group">
                <label for="password">Passwort</label>
                <input type="password" id="password" name="password" required minlength="6" placeholder="Mindestens 6 Zeichen">
            </div>

            <div class="form-group">
                <label for="password_confirm">Passwort wiederholen</label>
                <input type="password" id="password_confirm" name="password_confirm" required minlength="6" placeholder="Wiederholen Sie Ihr Passwort">
            </div>

            <button type="submit">Registrieren</button>
        </form>

        <div class="login-link">
            Bereits registriert? <a href="login.html">Hier anmelden</a>
        </div>
    </div>
</body>
</html>