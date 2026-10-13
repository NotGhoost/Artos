<?php
require_once __DIR__ . '/inc/functions.php';
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $st = db()->prepare("SELECT * FROM users WHERE email=?"); $st->execute([strtolower(trim($_POST["email"] ?? ""))]); $u = $st->fetch();
    if ($u && password_verify($_POST['pass'] ?? '', $u['pass_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => (int)$u['id'], 'name' => $u['name'], 'email' => $u['email'], 'role' => $u['role']];
        flash('Здравствуйте, ' . $u['name'] . '!');
        header('Location: ' . ($u['role'] === 'admin' ? 'admin/index.php' : 'index.php')); exit;
    }
    $err = 'Неверная почта или пароль.';
}
$pageTitle = 'Вход | Artos'; require_once __DIR__ . '/inc/header.php';
?>
<main class="wrap"><section style="max-width:520px"><div class="kicker">Личный раздел</div><h2>Вход</h2>
<?php if($err): ?><div class="err"><?= e($err) ?></div><?php endif; ?>
<form method="post"><input type="hidden" name="csrf" value="<?= csrf() ?>">
<label>Электронная почта</label><input name="email" type="email" required>
<label>Пароль</label><input name="pass" type="password" required>
<p><button class="btn">Войти</button></p>
<p class="meta">Нет аккаунта? <a href="register.php">Зарегистрируйтесь</a> — сможете отслеживать заявки.<br>Демо-доступ администратора: admin@example.ru / dub2024 (на боевом сайте поменяйте пароль!)</p></form>
</section></main>
<?php require_once __DIR__ . '/inc/footer.php'; ?>
