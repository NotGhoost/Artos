<?php
$root = '../'; $pageTitle = 'Картина — управление | Artos';
require_once dirname(__DIR__) . '/inc/functions.php'; require_admin();
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$p = null;
if ($id) { $st = db()->prepare("SELECT * FROM paintings WHERE id=?"); $st->execute([$id]); $p = $st->fetch(); }
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    try {
        $photo = upload_photo('photo', $p['photo'] ?? 'assets/img/p-field.jpg');
        $data = [(int)$_POST['artist_id'], trim($_POST['title']), trim($_POST['style']), trim($_POST['genre']), (int)$_POST['year'] ?: null, trim($_POST['technique']), trim($_POST['material']), trim($_POST['size']), trim($_POST['frame']), (int)$_POST['price'], $_POST['status'], trim($_POST['room']), trim($_POST['certificate']), $photo, trim($_POST['description'])];
        if ($id) { $data[] = $id; db()->prepare("UPDATE paintings SET artist_id=?,title=?,style=?,genre=?,year=?,technique=?,material=?,size=?,frame=?,price=?,status=?,room=?,certificate=?,photo=?,description=? WHERE id=?")->execute($data); }
        else db()->prepare("INSERT INTO paintings(artist_id,title,style,genre,year,technique,material,size,frame,price,status,room,certificate,photo,description) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)")->execute($data);
        flash('Картина сохранена.'); header('Location: paintings.php'); exit;
    } catch (Throwable $ex) { $err = $ex->getMessage(); }
}
require_once dirname(__DIR__) . '/inc/header.php';
$artists = db()->query("SELECT * FROM artists ORDER BY name")->fetchAll();
$v = fn($k) => e($p[$k] ?? ($_POST[$k] ?? ''));
?>
<main class="wrap"><section style="max-width:820px"><div class="kicker">Администратор</div><h2><?= $id ? 'Изменить картину' : 'Добавить картину' ?></h2>
<?php if(!$artists): ?><div class="err">Сначала <a href="artist_edit.php" style="color:#fff;text-decoration:underline">добавьте художника</a> — картина выбирается из списка авторов.</div><?php endif; ?>
<?php if($err): ?><div class="err"><?= e($err) ?></div><?php endif; ?>
<form method="post" enctype="multipart/form-data"><input type="hidden" name="csrf" value="<?= csrf() ?>"><input type="hidden" name="id" value="<?= $id ?>">
<label>Название *</label><input name="title" required value="<?= $v('title') ?>">
<div class="row"><div><label>Художник * (выбор из списка)</label><select name="artist_id" required><?php foreach($artists as $a): ?><option value="<?= (int)$a['id'] ?>" <?= (int)($p['artist_id'] ?? 0)===(int)$a['id']?'selected':'' ?>><?= e($a['name']) ?></option><?php endforeach; ?></select></div>
<div><label>Стиль / направление *</label><input name="style" list="styles" required value="<?= $v('style') ?>" placeholder="Импрессионизм"><datalist id="styles"><option>Реализм</option><option>Импрессионизм</option><option>Постимпрессионизм</option><option>Современная живопись</option><option>Академическая живопись</option></datalist></div></div>
<div class="row"><div><label>Жанр</label><input name="genre" value="<?= $v('genre') ?>" placeholder="Пейзаж, портрет…"></div><div><label>Год создания</label><input name="year" type="number" min="1800" max="2100" value="<?= $v('year') ?>"></div></div>
<div class="row"><div><label>Техника</label><input name="technique" value="<?= $v('technique') ?>" placeholder="Масло, мастихин"></div><div><label>Основа / материал</label><input name="material" value="<?= $v('material') ?>" placeholder="Холст на подрамнике"></div></div>
<div class="row"><div><label>Размер, см</label><input name="size" value="<?= $v('size') ?>" placeholder="60 × 80"></div><div><label>Рама</label><input name="frame" value="<?= $v('frame') ?>" placeholder="Деревянный багет, тёмный дуб"></div></div>
<div class="row"><div><label>Цена, ₽ *</label><input name="price" type="number" min="0" required value="<?= $v('price') ?>"></div><div><label>Статус</label><select name="status"><?php foreach(STATUSES as $k=>$t): ?><option value="<?= $k ?>" <?= ($p['status']??'available')===$k?'selected':'' ?>><?= $t ?></option><?php endforeach; ?></select></div></div>
<div class="row"><div><label>Где будет уместна</label><input name="room" value="<?= $v('room') ?>" placeholder="Гостиная, кабинет…"></div><div><label>Подпись / сертификат</label><input name="certificate" value="<?= $v('certificate') ?>" placeholder="Подпись на обороте, сертификат №…"></div></div>
<label>Фотография картины (JPG/PNG/WebP, до 8 МБ)</label><?php if($p['photo'] ?? ''): ?><img src="../<?= e($p['photo']) ?>" style="width:160px;display:block;margin-bottom:8px" alt=""><?php endif; ?><input name="photo" type="file" accept="image/*">
<label>Описание *</label><textarea name="description" required placeholder="Что изображено, настроение, история создания…"><?= $v('description') ?></textarea>
<p><button class="btn">Сохранить картину</button> <a class="btn light" href="paintings.php">Отмена</a></p></form>
</section></main>
<?php require_once dirname(__DIR__) . '/inc/footer.php'; ?>
