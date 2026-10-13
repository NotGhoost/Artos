<?php
require_once __DIR__ . '/inc/functions.php';
$id = (int)($_GET['id'] ?? 0);
$st = db()->prepare("SELECT p.*, a.name artist_name, a.id artist_id2 FROM paintings p LEFT JOIN artists a ON a.id=p.artist_id WHERE p.id=?");
$st->execute([$id]); $p = $st->fetch();
if (!$p) { http_response_code(404); $pageTitle='Картина не найдена'; require __DIR__.'/inc/header.php'; echo '<main class="wrap"><section><h2>Такой картины нет</h2><p><a href="index.php">← Вернуться в каталог</a></p></section></main>'; require __DIR__.'/inc/footer.php'; exit; }
$pageTitle = $p['title'] . ' — ' . ($p['artist_name'] ?? '') . ' | Artos';
$pageDesc = function_exists('mb_substr') ? mb_substr(strip_tags((string)$p['description']), 0, 160) : substr(strip_tags((string)$p['description']), 0, 160);
require_once __DIR__ . '/inc/header.php'; $u = user();
?>
<main class="detail-wrap"><p class="breadcrumb"><a href="index.php">Каталог</a> / <?= e($p['genre']) ?> / <?= e($p['title']) ?></p>
<div class="detail" style="padding:18px 0">
<div><img class="main" src="<?= e($p['photo']) ?>" alt="<?= e($p['title']) ?>"><p class="meta" style="margin-top:10px">Фотография передаёт цвет приблизительно — картину лучше посмотреть вживую в галерее или запросить дополнительные снимки.</p></div>
<div><div class="kicker"><?= e($p['genre']) ?> · <?= e($p['style']) ?></div><h2><?= e($p['title']) ?></h2>
<p class="meta" style="font-size:14px"><a href="artist.php?id=<?= (int)$p['artist_id2'] ?>"><?= e($p['artist_name'] ?? 'Художник не указан') ?></a>, <?= e((string)$p['year']) ?></p>
<p class="desc"><?= nl2br(e($p['description'])) ?></p>
<div class="specs">
<div><span>Художник</span><b><?= e($p['artist_name'] ?? '—') ?></b></div><div><span>Стиль</span><b><?= e($p['style'] ?: '—') ?></b></div><div><span>Жанр</span><b><?= e($p['genre'] ?: '—') ?></b></div><div><span>Год</span><b><?= e((string)$p['year'] ?: '—') ?></b></div><div><span>Техника</span><b><?= e($p['technique'] ?: '—') ?></b></div><div><span>Основа</span><b><?= e($p['material'] ?: '—') ?></b></div><div><span>Размер</span><b><?= e($p['size'] ?: '—') ?> см</b></div><div><span>Рама</span><b><?= e($p['frame'] ?: '—') ?></b></div><div><span>Подойдёт для</span><b><?= e($p['room'] ?: '—') ?></b></div><div><span>Подлинность</span><b><?= e($p['certificate'] ?: 'Сертификат галереи') ?></b></div><div><span>Наличие</span><b><?= STATUSES[$p['status']] ?? '' ?></b></div></div>
<div class="price-row"><span class="price" style="font-size:26px"><?= money($p['price']) ?></span></div>

<div class="panel" style="margin-top:22px"><h3 style="margin-top:0"><?= $p['status']==='sold' ? 'Обсудить авторскую вариацию' : 'Оставить заявку на картину' ?></h3>
<?php if($p['status']==='reserved'): ?><p class="meta">Картина сейчас забронирована. Оставьте заявку — сообщим, если бронь снимется.</p><?php endif; ?>
<form method="post" action="order.php"><input type="hidden" name="csrf" value="<?= csrf() ?>"><input type="hidden" name="painting_id" value="<?= (int)$p['id'] ?>">
<div class="row"><div><label>Ваше имя *</label><input name="name" required value="<?= e($u['name'] ?? '') ?>"></div><div><label>Телефон *</label><input name="phone" required placeholder="+375 …"></div></div>
<label>Электронная почта</label><input name="email" type="email" value="<?= e($u['email'] ?? '') ?>">
<label>Город и адрес доставки</label><input name="address" placeholder="Оставьте пустым, если заберёте из галереи">
<label>Комментарий</label><textarea name="comment" style="min-height:60px" placeholder="Например: нужна примерка дома…"></textarea>
<p class="meta">Это заявка, а не оплата: менеджер позвонит, подтвердит наличие и обсудит оплату и доставку. Бронь — 3 дня.</p>
<button class="btn">Отправить заявку</button></form></div>
</div></div></main>
<?php require_once __DIR__ . '/inc/footer.php'; ?>
