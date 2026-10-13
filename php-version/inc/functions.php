<?php
declare(strict_types=1);
require_once __DIR__ . '/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();

function e(?string $s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function money(int|float $n): string { return number_format((float)$n, 0, '', ' ') . ' ₽'; }
function user(): ?array { return $_SESSION['user'] ?? null; }
function is_admin(): bool { return (user()['role'] ?? '') === 'admin'; }
function require_admin(): void { if (!is_admin()) { $_SESSION['flash'] = 'Войдите как администратор.'; header('Location: ../login.php'); exit; } }
function csrf(): string { if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16)); return $_SESSION['csrf']; }
function check_csrf(): void { if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { http_response_code(403); exit('Ошибка безопасности формы. Вернитесь назад и попробуйте ещё раз.'); } }
function flash(?string $msg = null): ?string { if ($msg !== null) { $_SESSION['flash'] = $msg; return null; } $m = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $m; }
const STATUSES = ['available' => 'В наличии', 'reserved' => 'Забронирована', 'sold' => 'Продана'];
const ORDER_STATUSES = ['new' => 'Новый', 'confirmed' => 'Подтверждён', 'paid' => 'Оплачен', 'shipped' => 'Отправлен', 'cancelled' => 'Отменён'];

// Загрузка фото в /uploads. Возвращает относительный путь или null, если файл не прислали.
function upload_photo(string $field, ?string $old): ?string {
    if (empty($_FILES[$field]) || ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return $old;
    $f = $_FILES[$field];
    if ($f['error'] !== UPLOAD_ERR_OK || $f['size'] > 8 * 1024 * 1024) throw new RuntimeException('Файл слишком большой (максимум 8 МБ) или загрузился с ошибкой.');
    $info = @getimagesize($f['tmp_name']);
    $exts = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp'];
    if (!$info || !isset($exts[$info[2]])) throw new RuntimeException('Нужна картинка JPG, PNG или WebP.');
    $name = 'img_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $exts[$info[2]];
    $dir = dirname(__DIR__) . '/uploads';
    if (!move_uploaded_file($f['tmp_name'], $dir . '/' . $name)) throw new RuntimeException('Не удалось сохранить файл. Проверьте права на папку uploads.');
    return 'uploads/' . $name;
}
