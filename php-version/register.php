<?php
require_once __DIR__ . '/inc/functions.php';
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $name = trim($_POST['name'] ?? ''); $email = strtolower(trim($_POST["email"] ?? "")); $pass = $_POST['pass'] ?? '';
    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($pass) < 6) $err = 'Заполните имя, корректную почту и пароль от 6 символов.';
    else {
        try {
            db()->prepare("INSERT INTO users(name,email,pass_hash,role) VALUES(?,?,?,'user')")->execute([$name, $email, password_hash($pass, PASSWORD_DEFAULT)]);
            $id = (int)db()->lastInsertId();
            $_SESSION['user'] = ['id' => $id, 'name' => $name, 'email' => $email, 'role' => 'user'];
            flash('Аккаунт создан. Здравствуйте, ' . $name . '!'); header('Location: index.php'); exit;
        } catch (PDOException $ex) { $err = 'Аккаунт с такой почтой уже есть. Войдите.'; }
    }
}
$pageTitle = 'Регистрация | Artos'; require_once __DIR__ . '/inc/header.php';
?>
<main class="wrap"><section style="max-width:520px"><div class="kicker">Личный раздел</div><h2>Регистрация</h2>
<?php if($err): ?><div class="err"><?= e($err) ?></div><?php endif; ?>
<form method="post"><input type="hidden" name="csrf" value="<?= csrf() ?>">
<label>Ваше имя</label><input name="name" required>
<label>Электронная почта</label><input name="email" type="email" required>
<label>Пароль (от 6 символов)</label><input name="pass" type="password" required>
<p><button class="btn">Создать аккаунт</button></p><p class="meta">Уже есть аккаунт? <a href="login.php">Войдите</a>.</p></form>
</section></main>
<?php require_once __DIR__ . '/inc/footer.php'; ?>
