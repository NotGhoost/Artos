<?php
require_once __DIR__ . '/inc/functions.php';
$id = (int)($_GET['id'] ?? 0);
$st = db()->prepare("SELECT * FROM artists WHERE id=?"); $st->execute([$id]); $a = $st->fetch();
if (!$a) { http_response_code(404); $pageTitle='Художник не найден'; require __DIR__.'/inc/header.php'; echo '<main class="wrap"><section><h2>Такого художника нет</h2><p><a href="index.php#artists">← Все художники</a></p></section></main>'; require __DIR__.'/inc/footer.php'; exit; }
$pageTitle = $a['name'] . ' — биография и картины | Artos';
require_once __DIR__ . '/inc/header.php';
$st = db()->prepare("SELECT * FROM paintings WHERE artist_id=? ORDER BY year DESC"); $st->execute([$id]); $works = $st->fetchAll();
?>
<main class="detail-wrap"><p class="breadcrumb"><a href="index.php#artists">Художники</a> / <?= e($a['name']) ?></p>
<div class="detail" style="padding:18px 0"><div><img class="main" src="<?= e($a['photo']) ?>" alt="<?= e($a['name']) ?>"></div>
<div><div class="kicker"><?= e($a['style']) ?></div><h2><?= e($a['name']) ?></h2><p class="meta" style="font-size:14px"><?= e($a['years']) ?> · <?= e($a['place']) ?></p>
<p class="desc"><?= nl2br(e($a['bio'])) ?></p>
<div class="specs"><div><span>Образование</span><b><?= e($a['education'] ?: '—') ?></b></div><div><span>Направление</span><b><?= e($a['style'] ?: '—') ?></b></div><div><span>Работ в галерее</span><b><?= count($works) ?></b></div></div>
<?php if($a['fact']): ?><p class="desc"><?= nl2br(e($a['fact'])) ?></p><?php endif; ?>
<h3>Картины художника</h3>
<?php if($works): foreach($works as $p): ?><p><a href="painting.php?id=<?= (int)$p['id'] ?>"><?= e($p['title']) ?></a> <span class="meta">· <?= e((string)$p['year']) ?> · <?= money($p['price']) ?> · <?= STATUSES[$p['status']] ?? '' ?></span></p><?php endforeach; else: ?><p class="meta">Пока нет работ в наличии.</p><?php endif; ?>
</div></div></main>
<?php require_once __DIR__ . '/inc/footer.php'; ?>
