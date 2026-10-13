<?php
$pageTitle = 'Artos — галерея живописи | Купить картину в Минске';
require_once __DIR__ . '/inc/header.php';
$pdo = db();
// серверный вывод каталога (работает и без JavaScript); JS потом подменяет выдачу через api/paintings.php
$q = trim($_GET['q'] ?? ''); $style = $_GET['style'] ?? ''; $artist = $_GET['artist'] ?? ''; $sort = $_GET['sort'] ?? 'new'; $avail = !empty($_GET['avail']);
$sql = "SELECT p.*, a.name AS artist_name FROM paintings p LEFT JOIN artists a ON a.id=p.artist_id WHERE 1=1"; $args = [];
if ($q !== '') { $sql .= " AND (p.title LIKE ? OR p.genre LIKE ? OR p.style LIKE ? OR a.name LIKE ?)"; array_push($args, "%$q%", "%$q%", "%$q%", "%$q%"); }
if ($style !== '') { $sql .= " AND p.style=?"; $args[] = $style; }
if ($artist !== '') { $sql .= " AND p.artist_id=?"; $args[] = (int)$artist; }
if ($avail) $sql .= " AND p.status='available'";
$sql .= match($sort) { 'cheap' => " ORDER BY p.price ASC", 'exp' => " ORDER BY p.price DESC", 'title' => " ORDER BY p.title", default => " ORDER BY p.year DESC, p.id DESC" };
$st = $pdo->prepare($sql); $st->execute($args); $paintings = $st->fetchAll();
$artists = $pdo->query("SELECT a.*, (SELECT COUNT(*) FROM paintings p WHERE p.artist_id=a.id) cnt FROM artists a ORDER BY a.name")->fetchAll();
$styles = $pdo->query("SELECT DISTINCT style FROM paintings WHERE style<>'' ORDER BY style")->fetchAll(PDO::FETCH_COLUMN);
$availCount = (int)$pdo->query("SELECT COUNT(*) c FROM paintings WHERE status='available'")->fetch()['c'];
?>
<div class="hero"><div class="hero-in">
<div><div class="kicker">Живопись для дома и коллекции</div><h1>Картины, которые не кричат, <em>а остаются.</em></h1><p class="lead">Продаём оригиналы современных художников: пейзажи, портреты и натюрморты маслом. Каждую работу можно посмотреть вживую в галерее в Минске.</p><p><a class="btn" href="#catalog">Смотреть каталог</a> &nbsp;<a class="btn light" href="#artists">Наши художники</a></p>
<div class="hero-note"><span><b><?= $availCount ?></b>работ в наличии</span><span><b>14 дней</b>на возврат</span><span><b>Доставка</b>по Беларуси</span></div></div>
<figure class="frame"><img src="assets/img/p-river.jpg" alt="Пейзаж у реки"><figcaption><span>«Тихая заводь», Елена Воробьёва</span><span>2023</span></figcaption></figure>
</div></div>

<main class="wrap"><section id="catalog">
<div class="sec-head"><div><div class="kicker">Каталог</div><h2>Сейчас в галерее</h2></div><p>Нажмите на картину, чтобы прочитать описание, узнать размер и технику и оставить заявку.</p></div>
<form class="toolbar" id="filters" method="get" action="index.php">
<input name="q" type="search" placeholder="Поиск: название, художник, сюжет…" value="<?= e($q) ?>">
<select name="style"><option value="">Все стили</option><?php foreach($styles as $s): ?><option <?= $s===$style?'selected':'' ?>><?= e($s) ?></option><?php endforeach; ?></select>
<select name="artist"><option value="">Все художники</option><?php foreach($artists as $a): ?><option value="<?= (int)$a['id'] ?>" <?= (string)$a['id']===$artist?'selected':'' ?>><?= e($a['name']) ?></option><?php endforeach; ?></select>
<select name="sort"><option value="new" <?= $sort==='new'?'selected':'' ?>>Сначала новые</option><option value="cheap" <?= $sort==='cheap'?'selected':'' ?>>Цена: по возрастанию</option><option value="exp" <?= $sort==='exp'?'selected':'' ?>>Цена: по убыванию</option><option value="title" <?= $sort==='title'?'selected':'' ?>>По названию</option></select>
<label style="margin:0;display:flex;gap:7px;align-items:center"><input name="avail" type="checkbox" value="1" style="width:auto" <?= $avail?'checked':'' ?>> только в наличии</label>
<button class="btn small">Найти</button>
</form>
<div class="grid" id="grid"><?php foreach($paintings as $p): ?>
<article class="card"><a href="painting.php?id=<?= (int)$p['id'] ?>"><div class="imgbox"><img src="<?= e($p['photo']) ?>" alt="<?= e($p['title']) ?>" loading="lazy"></div><h3><?= e($p['title']) ?></h3><div class="meta"><?= e($p['artist_name'] ?? '') ?> · <?= e((string)$p['year']) ?> · <?= e($p['size']) ?></div><div class="price-row"><span class="price"><?= money($p['price']) ?></span><span class="status <?= e($p['status']) ?>"><?= STATUSES[$p['status']] ?? '' ?></span></div></a></article>
<?php endforeach; ?></div>
<div class="empty <?= $paintings?'hidden':'' ?>" id="empty">По этим условиям ничего не нашлось. Попробуйте убрать часть фильтров.</div>
</section></main>

<div class="artists" id="artists"><div class="wrap" style="padding-top:52px;padding-bottom:52px">
<div class="sec-head"><div><div class="kicker">Авторы</div><h2>Художники галереи</h2></div><p>Мы работаем с авторами напрямую и знаем историю каждой работы.</p></div>
<div class="artist-grid"><?php foreach($artists as $a): ?>
<article class="artist"><a href="artist.php?id=<?= (int)$a['id'] ?>"><img src="<?= e($a['photo']) ?>" alt="<?= e($a['name']) ?>" loading="lazy"><h3><?= e($a['name']) ?></h3><div class="meta"><?= e($a['years']) ?> · <?= e($a['place']) ?></div><p><?= e($a['style']) ?></p><div class="meta">Работ в галерее: <?= (int)$a['cnt'] ?></div></a></article>
<?php endforeach; ?></div></div></div>

<main class="wrap"><section id="about"><div class="about">
<div><div class="kicker">О галерее</div><h2>Небольшая галерея в центре Минска</h2><p>Artos открылся в 2016 году. Начинали с одной комнаты и работ знакомых художников, сейчас у нас два зала, но принцип остался прежним: мы не выставляем то, что не повесили бы у себя дома.</p><p>Поможем подобрать картину под комнату и бюджет, привезём на примерку по Минску и оформим все документы. К каждой работе прилагается сертификат подлинности с подписью художника.</p>
<div class="fact"><span>Примерка дома</span><b>по Минску — бесплатно</b></div><div class="fact"><span>Доставка</span><b>2–5 дней по Беларуси</b></div><div class="fact"><span>Возврат</span><b>14 дней, без объяснений</b></div></div>
<img src="assets/img/a-studio.jpg" alt="В мастерской художника">
</div></section></main>
<?php require_once __DIR__ . '/inc/footer.php'; ?>
