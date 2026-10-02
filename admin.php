<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { http_response_code(403); echo "Access denied."; exit; }
$usersFile = __DIR__ . '/users.json';
$users = json_decode(file_get_contents($usersFile), true);
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $target = $_POST['email'] ?? '';
    if ($action === 'force_reset' && isset($users[$target])) {
        $users[$target]['must_change_password'] = true;
        $users[$target]['password_changed_at'] = 0;
        file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $msg = "Reset forced for $target";
    }
    if ($action === 'delete' && isset($users[$target])) {
        unset($users[$target]);
        file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $msg = "Deleted $target";
    }
    if ($action === 'add') {
        $newEmail = strtolower(trim($_POST['new_email'] ?? ''));
        $newRole = $_POST['new_role'] ?? 'viewer';
        $newClasses = array_filter(array_map('trim', explode(',', $_POST['new_classes'] ?? '')));
        $tempPw = $_POST['temp_pw'] ?? 'KSG@2026';
        if ($newEmail && strlen($tempPw) >= 8 && preg_match('/[A-Za-z]/', $tempPw) && preg_match('/[0-9]/', $tempPw) && !isset($users[$newEmail])) {
            $users[$newEmail] = ['password_hash'=>password_hash($tempPw, PASSWORD_BCRYPT, ['cost'=>12]), 'role'=>$newRole, 'classes'=>array_values($newClasses), 'must_change_password'=>true, 'password_changed_at'=>0];
            file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            $msg = "Added $newEmail";
        } else { $msg = "Add failed"; }
    }
    $users = json_decode(file_get_contents($usersFile), true);
}
?>
<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>KSG Live Admin</title>
<style>body{font-family:system-ui;background:#111;color:#eee;padding:24px}table{border-collapse:collapse;width:100%}th,td{padding:10px;text-align:left;border-bottom:1px solid #333}th{background:#1a1a1a}input,select,button{padding:8px;border-radius:5px;border:1px solid #444;background:#0d0d0d;color:#eee}button{background:#2d6cdf;border:0;cursor:pointer;color:#fff}button.danger{background:#a33}.card{background:#1c1c1c;border:1px solid #333;border-radius:10px;padding:20px;margin-bottom:20px;max-width:1100px}</style>
</head><body>
<div style="display:flex;align-items:center;gap:14px;margin-bottom:24px;">
  <img src="logo.png" style="height:50px;">
  <div style="font-size:22px;font-weight:600;letter-spacing:1px;">KSG LIVE ADMIN</div>
</div>
<?php if ($msg): ?><p style="color:#8f8"><?= htmlspecialchars($msg) ?></p><?php endif; ?>
<div class="card"><h2>Add user</h2>
<form method="post" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
<input type="hidden" name="action" value="add">
<div><label style="font-size:12px;opacity:0.7;display:block;">Email</label><input type="email" name="new_email" required></div>
<div><label style="font-size:12px;opacity:0.7;display:block;">Role</label><select name="new_role"><option value="viewer">viewer</option><option value="presenter">presenter</option><option value="admin">admin</option></select></div>
<div><label style="font-size:12px;opacity:0.7;display:block;">Classes (comma-separated)</label><input type="text" name="new_classes" placeholder="Class 1, Class 2" style="min-width:220px;"></div>
<div><label style="font-size:12px;opacity:0.7;display:block;">Temp password</label><input type="text" name="temp_pw" value="KSG@2026"></div>
<button type="submit">Add user</button>
</form></div>
<div class="card"><h2>Users</h2>
<table><tr><th>Email</th><th>Role</th><th>Classes</th><th>Must change</th><th>Actions</th></tr>
<?php foreach ($users as $em => $u): ?>
<tr><td><?= htmlspecialchars($em) ?></td><td><?= htmlspecialchars($u['role']) ?></td><td><?= htmlspecialchars(implode(', ', $u['classes'] ?? [])) ?></td><td><?= !empty($u['must_change_password']) ? 'Yes' : 'No' ?></td>
<td>
<form method="post" style="display:inline"><input type="hidden" name="action" value="force_reset"><input type="hidden" name="email" value="<?= htmlspecialchars($em) ?>"><button>Reset</button></form>
<form method="post" style="display:inline" onsubmit="return confirm('Delete?')"><input type="hidden" name="action" value="delete"><input type="hidden" name="email" value="<?= htmlspecialchars($em) ?>"><button class="danger">Delete</button></form>
</td></tr>
<?php endforeach; ?>
</table></div>
<p><a href="logout.php" style="color:#8cf">Sign out</a></p>
</body></html>