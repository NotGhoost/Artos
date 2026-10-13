<?php
require_once __DIR__ . '/inc/functions.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }
check_csrf();
$pid = (int)($_POST['painting_id'] ?? 0);
$name = trim($_POST['name'] ?? ''); $phone = trim($_POST['phone'] ?? '');
if ($pid <= 0 || $name === '' || $phone === '') { flash('Заполните имя и телефон.'); header('Location: painting.php?id=' . $pid); exit; }
$st = db()->prepare("SELECT * FROM paintings WHERE id=?"); $st->execute([$pid]); $p = $st->fetch();
if (!$p) { flash('Картина не найдена.'); header('Location: index.php'); exit; }
$u = user();
db()->prepare("INSERT INTO orders(painting_id,user_id,customer_name,phone,email,address,comment,status) VALUES(?,?,?,?,?,?,?,'new')")
    ->execute([$pid, $u['id'] ?? null, $name, $phone, trim($_POST['email'] ?? ''), trim($_POST['address'] ?? ''), trim($_POST['comment'] ?? '')]);
if ($p['status'] === 'available') db()->prepare("UPDATE paintings SET status='reserved' WHERE id=?")->execute([$pid]);
flash('Спасибо, ' . $name . '! Заявка по картине «' . $p['title'] . '» принята. Мы позвоним на ' . $phone . ' и подтвердим заказ.');
header('Location: ' . ($u ? 'my-orders.php' : 'index.php')); exit;
