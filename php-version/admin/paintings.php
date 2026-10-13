<?php
$root = '../'; $pageTitle = 'Картины — управление | Artos';
require_once dirname(__DIR__) . '/inc/functions.php'; require_admin();
if (isset($_GET['del'])) { db()->prepare("DELETE FROM paintings WHERE id=?")->execute([(int)$_GET['del']]); flash('Картина удалена.'); header('Location: paintings.php'); exit; }
require_once dirname(__DIR__) . '/inc/header.php';
$rows = db()->query("SELECT p.*, a.name artist_name FROM paintings p LEFT JOIN artists a ON a.id=p.artist_id ORDER BY p.id DESC")->fetchAll();
?>
<main class="wrap"><section><div class="kicker">Администратор</div><h2>Картины</h2>
<div class="tabs"><a class="tab" href="index.php">Обзор</a><a class="tab on" href="paintings.php">Картины</a><a class="tab" href="artists.php">Художники</a><a class="tab" href="orders.php">Заказы</a></div>
<p><a class="btn" href="painting_edit.php">+ Добавить картину</a></p>
<table><tr><th>Фото</th><th>Картина</th><th>Художник</th><th>Цена</th><th>Статус</th><th></th></tr>
<?php foreach($rows as $p): ?><tr><td><img src="../<?= e($p['photo']) ?>" style="width:64px;height:48px;object-fit:cover" alt=""></td><td><b><?= e($p['title']) ?></b><br><span class="meta"><?= e((string)$p['year']) ?> · <?= e($p['size']) ?> · <?= e($p['style']) ?></span></td><td><?= e($p['artist_name'] ?? '—') ?></td><td><?= money($p['price']) ?></td><td><?= STATUSES[$p['status']] ?? '' ?></td><td><a href="painting_edit.php?id=<?= (int)$p['id'] ?>">Изменить</a> · <a href="paintings.php?del=<?= (int)$p['id'] ?>" onclick="return confirm('Удалить картину?')">Удалить</a></td></tr><?php endforeach; ?>
</table></section></main>
<?php require_once dirname(__DIR__) . '/inc/footer.php'; ?>
