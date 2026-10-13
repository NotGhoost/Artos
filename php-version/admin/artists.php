<?php
$root = '../'; $pageTitle = 'Художники — управление | Artos';
require_once dirname(__DIR__) . '/inc/functions.php'; require_admin();
if (isset($_GET['del'])) {
    $id = (int)$_GET['del'];
    $c = db()->prepare("SELECT COUNT(*) c FROM paintings WHERE artist_id=?"); $c->execute([$id]);
    if ((int)$c->fetch()['c'] > 0) flash('Нельзя удалить художника, у которого есть картины. Сначала удалите или переназначьте его работы.');
    else { db()->prepare("DELETE FROM artists WHERE id=?")->execute([$id]); flash('Художник удалён.'); }
    header('Location: artists.php'); exit;
}
require_once dirname(__DIR__) . '/inc/header.php';
$rows = db()->query("SELECT a.*, (SELECT COUNT(*) FROM paintings p WHERE p.artist_id=a.id) cnt FROM artists a ORDER BY a.name")->fetchAll();
?>
<main class="wrap"><section><div class="kicker">Администратор</div><h2>Художники</h2>
<div class="tabs"><a class="tab" href="index.php">Обзор</a><a class="tab" href="paintings.php">Картины</a><a class="tab on" href="artists.php">Художники</a><a class="tab" href="orders.php">Заказы</a></div>
<p><a class="btn" href="artist_edit.php">+ Добавить художника</a></p>
<table><tr><th>Фото</th><th>Художник</th><th>Стиль</th><th>Работ</th><th></th></tr>
<?php foreach($rows as $a): ?><tr><td><img src="../<?= e($a['photo']) ?>" style="width:56px;height:56px;object-fit:cover" alt=""></td><td><b><?= e($a['name']) ?></b><br><span class="meta"><?= e($a['years']) ?> · <?= e($a['place']) ?></span></td><td><?= e($a['style']) ?></td><td><?= (int)$a['cnt'] ?></td><td><a href="artist_edit.php?id=<?= (int)$a['id'] ?>">Изменить</a> · <a href="artists.php?del=<?= (int)$a['id'] ?>" onclick="return confirm('Удалить художника?')">Удалить</a></td></tr><?php endforeach; ?>
</table></section></main>
<?php require_once dirname(__DIR__) . '/inc/footer.php'; ?>
