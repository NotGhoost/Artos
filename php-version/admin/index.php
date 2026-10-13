<?php
$root = '../'; $pageTitle = 'Управление | Artos';
require_once dirname(__DIR__) . '/inc/functions.php'; require_admin();
require_once dirname(__DIR__) . '/inc/header.php';
$c = db()->query("SELECT (SELECT COUNT(*) FROM paintings) p,(SELECT COUNT(*) FROM artists) a,(SELECT COUNT(*) FROM orders WHERE status='new') o")->fetch();
?>
<main class="wrap"><section><div class="kicker">Администратор</div><h2>Управление галереей</h2>
<div class="tabs"><a class="tab on" href="index.php">Обзор</a><a class="tab" href="paintings.php">Картины</a><a class="tab" href="artists.php">Художники</a><a class="tab" href="orders.php">Заказы</a></div>
<div class="grid">
<div class="panel"><h3>Картин в каталоге</h3><p style="font-size:32px;margin:0"><?= (int)$c['p'] ?></p><p><a href="painting_edit.php">+ Добавить картину</a></p></div>
<div class="panel"><h3>Художников</h3><p style="font-size:32px;margin:0"><?= (int)$c['a'] ?></p><p><a href="artist_edit.php">+ Добавить художника</a></p></div>
<div class="panel"><h3>Новых заявок</h3><p style="font-size:32px;margin:0"><?= (int)$c['o'] ?></p><p><a href="orders.php">Смотреть заказы →</a></p></div>
</div></section></main>
<?php require_once dirname(__DIR__) . '/inc/footer.php'; ?>
