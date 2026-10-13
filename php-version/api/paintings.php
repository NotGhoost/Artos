<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/inc/db.php';
header('Content-Type: application/json; charset=utf-8');
$q = trim($_GET['q'] ?? ''); $style = $_GET['style'] ?? ''; $artist = $_GET['artist'] ?? ''; $sort = $_GET['sort'] ?? 'new'; $avail = !empty($_GET['avail']);
$sql = "SELECT p.*, a.name AS artist_name FROM paintings p LEFT JOIN artists a ON a.id=p.artist_id WHERE 1=1"; $args = [];
if ($q !== '') { $sql .= " AND (p.title LIKE ? OR p.genre LIKE ? OR p.style LIKE ? OR a.name LIKE ?)"; array_push($args, "%$q%", "%$q%", "%$q%", "%$q%"); }
if ($style !== '') { $sql .= " AND p.style=?"; $args[] = $style; }
if ($artist !== '') { $sql .= " AND p.artist_id=?"; $args[] = (int)$artist; }
if ($avail) $sql .= " AND p.status='available'";
$sql .= match($sort) { 'cheap' => " ORDER BY p.price ASC", 'exp' => " ORDER BY p.price DESC", 'title' => " ORDER BY p.title", default => " ORDER BY p.year DESC, p.id DESC" };
$st = db()->prepare($sql); $st->execute($args);
echo json_encode($st->fetchAll(), JSON_UNESCAPED_UNICODE);
