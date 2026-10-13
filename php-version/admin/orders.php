<?php
$root = '../'; $pageTitle = 'Заказы — управление | Artos';
require_once dirname(__DIR__) . '/inc/functions.php'; require_admin();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $oid = (int)$_POST['order_id']; $status = $_POST['status'];
    if (isset(ORDER_STATUSES[$status])) {
        db()->prepare("UPDATE orders SET status=? WHERE id=?")->execute([$status, $oid]);
        if ($status === 'cancelled') {
            $o = db()->prepare("SELECT painting_id FROM orders WHERE id=?"); $o->execute([$oid]); $row = $o->fetch();
            if ($row) db()->prepare("UPDATE paintings SET status='available' WHERE id=? AND status='reserved'")->execute([$row['painting_id']]);
        }
        flash('Статус заказа обновлён.');
    }
    header('Location: orders.php'); exit;
}
require_once dirname(__DIR__) . '/inc/header.php';
$rows = db()->query("SELECT o.*, p.title, p.price FROM orders o LEFT JOIN paintings p ON p.id=o.painting_id ORDER BY o.id DESC")->fetchAll();
?>
<main class="wrap"><section><div class="kicker">Администратор</div><h2>Заказы (заявки)</h2>
<div class="tabs"><a class="tab" href="index.php">Обзор</a><a class="tab" href="paintings.php">Картины</a><a class="tab" href="artists.php">Художники</a><a class="tab on" href="orders.php">Заказы</a></div>
<?php if(!$rows): ?><div class="empty">Заказов пока нет.</div><?php else: ?>
<table><tr><th>Дата</th><th>Картина</th><th>Покупатель</th><th>Сумма</th><th>Статус</th></tr>
<?php foreach($rows as $o): ?><tr><td><?= e($o['created_at']) ?></td><td><b><?= e($o['title'] ?? '—') ?></b><?php if($o['comment']): ?><br><span class="meta"><?= e($o['comment']) ?></span><?php endif; ?><?php if($o['address']): ?><br><span class="meta">Доставка: <?= e($o['address']) ?></span><?php endif; ?></td><td><?= e($o['customer_name']) ?><br><?= e($o['phone']) ?><br><?= e($o['email']) ?></td><td><?= money($o['price'] ?? 0) ?></td>
<td><form method="post" style="display:flex;gap:6px"><input type="hidden" name="csrf" value="<?= csrf() ?>"><input type="hidden" name="order_id" value="<?= (int)$o['id'] ?>"><select name="status" style="width:auto"><?php foreach(ORDER_STATUSES as $k=>$t): ?><option value="<?= $k ?>" <?= $o['status']===$k?'selected':'' ?>><?= $t ?></option><?php endforeach; ?></select><button class="btn small">OK</button></form></td></tr><?php endforeach; ?>
</table><?php endif; ?>
</section></main>
<?php require_once dirname(__DIR__) . '/inc/footer.php'; ?>
