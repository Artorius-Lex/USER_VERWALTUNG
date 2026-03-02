<?php
// Simple admin script to list and activate users stored in data/users.json
// WARNING: This script has no authentication and is for local development only.

$usersFile = __DIR__ . '/data/users.json';
$users = [];
if (file_exists($usersFile)) {
    $users = json_decode(file_get_contents($usersFile), true) ?: [];
}

$id = isset($_GET['id']) ? intval($_GET['id']) : null;
$action = $_GET['action'] ?? null;

if ($id && $action === 'activate') {
    foreach ($users as &$u) {
        if (isset($u['id']) && intval($u['id']) === $id) {
            $u['isActive'] = true;
            file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);
            header('Location: admin_activate.php');
            exit;
        }
    }
}

?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Admin - Users</title></head>
<body>
  <h1>Benutzerverwaltung (Dev)</h1>
  <?php if (empty($users)): ?>
    <p>Keine Benutzer vorhanden.</p>
  <?php else: ?>
    <table border="1" cellpadding="6" cellspacing="0">
      <tr><th>ID</th><th>Username</th><th>Email</th><th>Active</th><th>Aktion</th></tr>
      <?php foreach ($users as $u): ?>
        <tr>
          <td><?php echo htmlspecialchars($u['id'] ?? ''); ?></td>
          <td><?php echo htmlspecialchars($u['username'] ?? ''); ?></td>
          <td><?php echo htmlspecialchars($u['email'] ?? ''); ?></td>
          <td><?php echo (!empty($u['isActive']) ? 'ja' : 'nein'); ?></td>
          <td>
            <?php if (empty($u['isActive'])): ?>
              <a href="admin_activate.php?id=<?php echo $u['id']; ?>&action=activate">aktivieren</a>
            <?php else: ?>
              -
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>
</body>
</html>
