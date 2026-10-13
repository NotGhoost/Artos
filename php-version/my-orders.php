<?php
require_once __DIR__ . '/inc/functions.php';
if (!user()) { flash('Войдите, чтобы посмотреть свои заказы.'); header('Location: login.php'); exit; }
$pageTitle = 'Мои заказы | Artos'; require_once __DIR__ . '/inc/header.php';
$st = db()->prepare("SELECT o.*, p.title, p.price, p.photo FROM orders o LEFT JOIN paintings p ON p.id=o.painting_id WHERE o.user_id=? OR o.email=? ORDER BY o.id DESC");
$st->execute([user()['id'], user()['email']]); $orders = $st->fetchAll();
?>
<main class="wrap"><section><div class="kicker">Личный раздел</div><h2>Мои заказы</h2>
<?php if(!$orders): ?><div class="empty">У вас пока нет заказов. Выберите картину в <a href="index.php#catalog">каталоге</a> и оставьте заявку.</div>
<?php else: foreach($orders as $o): ?>
<div class="panel" style="margin-bottom:12px"><b><?= e($o['title'] ?? 'Картина удалена') ?></b> — <?= money($o['price'] ?? 0) ?><br>
<span class="meta">Заявка от <?= e($o['created_at']) ?> · статус: <b><?= ORDER_STATUSES[$o['status']] ?? e($o['status']) ?></b> · <?= e($o['customer_name']) ?>, <?= e($o['phone']) ?></span>
<?php if($o['address']): ?><br><span class="meta">Доставка: <?= e($o['address']) ?></span><?php endif; ?></div>
<?php endforeach; endif; ?>
</section></main>
<?php require_once __DIR__ . '/inc/footer.php'; ?>
