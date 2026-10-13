<?php require_once __DIR__ . '/functions.php'; $cfg = require __DIR__ . '/config.php'; $u = user(); ?>
<!doctype html><html lang="ru"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($pageTitle ?? $cfg['site_name']) ?></title><meta name="description" content="<?= e($pageDesc ?? 'Галерея живописи Artos: оригинальные картины белорусских и российских художников, биографии авторов, заказ с доставкой.') ?>">
<link rel="stylesheet" href="<?= e($root ?? '') ?>assets/css/style.css"></head><body>
<div class="top"><div class="top-in"><span>Минск, пр. Независимости, 12 · ежедневно 11:00–20:00</span><span>+375 (29) 000-00-00 · hello@example.by</span></div></div>
<header><div class="head-in">
<a class="logo" href="<?= e($root ?? '') ?>index.php">Artos<small>галерея живописи · artos.by</small></a>
<nav><a href="<?= e($root ?? '') ?>index.php#catalog">Картины</a><a href="<?= e($root ?? '') ?>index.php#artists">Художники</a><a href="<?= e($root ?? '') ?>index.php#about">О галерее</a><a href="<?= e($root ?? '') ?>index.php#contacts">Контакты</a><?php if($u): ?><a href="<?= e($root ?? '') ?>my-orders.php">Мои заказы</a><?php endif; ?><?php if(is_admin()): ?><a href="<?= e($root ?? '') ?>admin/index.php">Управление</a><?php endif; ?></nav>
<div class="head-actions"><span class="userbar"><?= $u ? e($u['name']) . (is_admin() ? ' (администратор)' : '') : '' ?></span>
<?php if($u): ?><a class="btn light small" href="<?= e($root ?? '') ?>logout.php">Выйти</a><?php else: ?><a class="btn light small" href="<?= e($root ?? '') ?>login.php">Войти</a><?php endif; ?></div>
</div></header>
<?php if($m = flash()): ?><div class="flash"><?= e($m) ?></div><?php endif; ?>
