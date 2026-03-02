<?php
/**
 * login.php - Verarbeitet die Benutzeranmeldung
 * 
 * Dieser Script:
 * 1. Empfängt Benutzernamen und Passwort aus dem Formular
 * 2. Sucht den Benutzer in der Datenbank
 * 3. Verifiziert das Passwort gegen den Hash
 * 4. Speichert die Benutzer-ID in der Session
 * 5. Prüft, ob der Benutzer aktiv ist
 */

// Session starten (um $_SESSION zu verwenden)
session_start();

// Autoloader laden (prüfe zwei mögliche Pfade)
$vendor1 = __DIR__ . '/../vendor/autoload.php';
$vendor2 = __DIR__ . '/vendor/autoload.php';
if (file_exists($vendor1)) {
    require_once $vendor1;
} elseif (file_exists($vendor2)) {
    require_once $vendor2;
} else {
    // Für den Fall, dass kein Autoloader gefunden wird, erzeugen wir eine hilfreiche Fehlermeldung
    http_response_code(500);
    echo 'Autoloader nicht gefunden. Bitte Composer ausführen oder den `vendor`-Ordner bereitstellen.';
    exit;
}

use App\Security\PasswordHasher;

$error = null;

// Nur POST-Requests verarbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Eingaben erhalten und bereinigen
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validierung
    if (empty($username) || empty($password)) {
        $error = "Benutzername und Passwort sind erforderlich.";
    } else {
        try {
            // Benutzer aus Datei laden (data/users.json)
            $usersFile = __DIR__ . '/data/users.json';
            $user = null;
            if (file_exists($usersFile)) {
                $json = file_get_contents($usersFile);
                $users = json_decode($json, true) ?: [];
                foreach ($users as $u) {
                    if (isset($u['username']) && $u['username'] === $username) {
                        $userArr = $u;
                        break;
                    }
                }
            }

            if (empty($userArr)) {
                $error = "Benutzername oder Passwort ungültig.";
            } else {
                // Wrap array into an object with expected methods
                $user = new class($userArr) {
                    private $d;
                    public function __construct($d) { $this->d = $d; }
                    public function isActive() { return !empty($this->d['isActive']); }
                    public function getPassword() { return $this->d['password'] ?? null; }
                    public function getSalt() { return $this->d['salt'] ?? null; }
                    public function getId() { return $this->d['id'] ?? null; }
                    public function getUsername() { return $this->d['username'] ?? null; }
                    public function isAdmin() { return !empty($this->d['isAdmin']); }
                };

                if (!$user->isActive()) {
                    $error = "Dieses Konto ist nicht aktiviert. Bitte warten Sie auf die Aktivierung durch einen Administrator.";
                } elseif (!PasswordHasher::verifyPassword($password, $user->getPassword(), $user->getSalt())) {
                    $error = "Benutzername oder Passwort ungültig.";
                } else {
                    $_SESSION['user_id'] = $user->getId();
                    $_SESSION['username'] = $user->getUsername();
                    $_SESSION['is_admin'] = $user->isAdmin();
                    header('Location: index.php');
                    exit();
                }
            }

        } catch (\Exception $e) {
            $error = "Fehler bei der Anmeldung: " . $e->getMessage();
        }
    }
}

// HTML anzeigen
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anmeldung - Benutzerverwaltung</title>
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
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
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

        .register-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }

        .register-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: bold;
        }

        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Anmeldung</h1>

        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="username">Benutzername</label>
                <input type="text" id="username" name="username" required placeholder="Geben Sie Ihren Benutzernamen ein">
            </div>

            <div class="form-group">
                <label for="password">Passwort</label>
                <input type="password" id="password" name="password" required placeholder="Geben Sie Ihr Passwort ein">
            </div>

            <button type="submit">Anmelden</button>
        </form>

        <div class="register-link">
            Noch kein Konto? <a href="register.php">Hier registrieren</a>
        </div>
    </div>
</body>
</html>