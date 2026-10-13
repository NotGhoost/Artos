<?php
$root = '../'; $pageTitle = 'Художник — управление | Artos';
require_once dirname(__DIR__) . '/inc/functions.php'; require_admin();
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0); $a = null;
if ($id) { $st = db()->prepare("SELECT * FROM artists WHERE id=?"); $st->execute([$id]); $a = $st->fetch(); }
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    try {
        $photo = upload_photo('photo', $a['photo'] ?? 'assets/img/a-studio.jpg');
        $data = [trim($_POST['name']), trim($_POST['years']), trim($_POST['place']), trim($_POST['style']), trim($_POST['education']), $photo, trim($_POST['bio']), trim($_POST['fact'])];
        if ($id) { $data[] = $id; db()->prepare("UPDATE artists SET name=?,years=?,place=?,style=?,education=?,photo=?,bio=?,fact=? WHERE id=?")->execute($data); }
        else db()->prepare("INSERT INTO artists(name,years,place,style,education,photo,bio,fact) VALUES(?,?,?,?,?,?,?,?)")->execute($data);
        flash('Художник сохранён. Теперь он доступен в списке при добавлении картины.'); header('Location: artists.php'); exit;
    } catch (Throwable $ex) { $err = $ex->getMessage(); }
}
require_once dirname(__DIR__) . '/inc/header.php';
$v = fn($k) => e($a[$k] ?? ($_POST[$k] ?? ''));
?>
<main class="wrap"><section style="max-width:820px"><div class="kicker">Администратор</div><h2><?= $id ? 'Изменить художника' : 'Добавить художника' ?></h2>
<?php if($err): ?><div class="err"><?= e($err) ?></div><?php endif; ?>
<form method="post" enctype="multipart/form-data"><input type="hidden" name="csrf" value="<?= csrf() ?>"><input type="hidden" name="id" value="<?= $id ?>">
<label>Имя и фамилия *</label><input name="name" required value="<?= $v('name') ?>">
<div class="row"><div><label>Годы жизни / год рождения</label><input name="years" value="<?= $v('years') ?>" placeholder="род. 1978"></div><div><label>Город, страна</label><input name="place" value="<?= $v('place') ?>" placeholder="Минск, Беларусь"></div></div>
<div class="row"><div><label>Основной стиль</label><input name="style" value="<?= $v('style') ?>" placeholder="Импрессионизм, пейзаж"></div><div><label>Образование</label><input name="education" value="<?= $v('education') ?>" placeholder="БГАИ, факультет живописи…"></div></div>
<label>Фотография художника</label><?php if($a['photo'] ?? ''): ?><img src="../<?= e($a['photo']) ?>" style="width:140px;display:block;margin-bottom:8px" alt=""><?php endif; ?><input name="photo" type="file" accept="image/*">
<label>Биография *</label><textarea name="bio" required placeholder="Где учился, где выставлялся, о чём его живопись…"><?= $v('bio') ?></textarea>
<label>Выставки / интересный факт</label><textarea name="fact" style="min-height:60px"><?= $v('fact') ?></textarea>
<p><button class="btn">Сохранить художника</button> <a class="btn light" href="artists.php">Отмена</a></p></form>
</section></main>
<?php require_once dirname(__DIR__) . '/inc/footer.php'; ?>
