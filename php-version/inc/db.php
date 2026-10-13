<?php
declare(strict_types=1);

function db(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;
    $cfg = require __DIR__ . '/config.php';
    $opt = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false];
    if (($cfg['db_driver'] ?? 'mysql') === 'sqlite') {
        $pdo = new PDO('sqlite:' . $cfg['db_sqlite'], null, null, $opt);
        $pdo->exec('PRAGMA foreign_keys = ON');
        sqlite_init($pdo);
    } else {
        $dsn = "mysql:host={$cfg['db_host']};dbname={$cfg['db_name']};charset=utf8mb4";
        $pdo = new PDO($dsn, $cfg['db_user'], $cfg['db_pass'], $opt);
    }
    return $pdo;
}

// Только для локальной SQLite-демонстрации: создаёт таблицы и демо-данные, если база пустая.
// На MySQL таблицы и данные создаются импортом database/schema.sql — эта функция там не вызывается.
function sqlite_init(PDO $pdo): void {
    $pdo->exec("CREATE TABLE IF NOT EXISTS users(id INTEGER PRIMARY KEY AUTOINCREMENT,name TEXT NOT NULL,email TEXT NOT NULL UNIQUE,pass_hash TEXT NOT NULL,role TEXT NOT NULL DEFAULT 'user',created_at TEXT DEFAULT CURRENT_TIMESTAMP);
    CREATE TABLE IF NOT EXISTS artists(id INTEGER PRIMARY KEY AUTOINCREMENT,name TEXT NOT NULL,years TEXT,place TEXT,style TEXT,education TEXT,photo TEXT,bio TEXT,fact TEXT,created_at TEXT DEFAULT CURRENT_TIMESTAMP);
    CREATE TABLE IF NOT EXISTS paintings(id INTEGER PRIMARY KEY AUTOINCREMENT,artist_id INTEGER,title TEXT NOT NULL,style TEXT,genre TEXT,year INTEGER,technique TEXT,material TEXT,size TEXT,frame TEXT,price INTEGER NOT NULL DEFAULT 0,status TEXT NOT NULL DEFAULT 'available',room TEXT,certificate TEXT,photo TEXT,description TEXT,created_at TEXT DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(artist_id) REFERENCES artists(id) ON DELETE SET NULL);
    CREATE TABLE IF NOT EXISTS orders(id INTEGER PRIMARY KEY AUTOINCREMENT,painting_id INTEGER,user_id INTEGER,customer_name TEXT NOT NULL,phone TEXT NOT NULL,email TEXT,address TEXT,comment TEXT,status TEXT NOT NULL DEFAULT 'new',created_at TEXT DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(painting_id) REFERENCES paintings(id) ON DELETE SET NULL,FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL);");
    if ((int)$pdo->query("SELECT COUNT(*) c FROM artists")->fetch()['c'] > 0) return;
    $hash = password_hash('dub2024', PASSWORD_DEFAULT);
    $pdo->prepare("INSERT INTO users(name,email,pass_hash,role) VALUES('Администратор','admin@example.ru',?,'admin')")->execute([$hash]);
    $artists = [
        ['Елена Воробьёва','род. 1974','Санкт-Петербург, Россия','Пейзаж, импрессионизм','Институт живописи им. И. Е. Репина','assets/img/a-elena.jpg','Елена Воробьёва пишет пейзажи уже больше двадцати лет. Начинала с этюдов на Неве, потом много ездила по северу России. Её манеру легко узнать по спокойному свету: художница почти всегда работает рано утром или перед закатом.','Персональные выставки в Санкт-Петербурге (2019, 2023). Член Союза художников России.'],
        ['Иван Соколов','1948–2019','Москва, Россия','Академический портрет','МГАХИ им. В. И. Сурикова','assets/img/a-ivan.jpg','Иван Соколов — портретист старой школы. Писал медленно, по много сеансов, и считал, что сходство начинается не с черт лица, а с характера. После смерти художника галерея работает с его наследниками.','Работы хранятся в музеях Твери и Ярославля. К каждой картине — заключение из архива наследников.'],
        ['Мария Лебедева','род. 1989','Казань, Россия','Натюрморт, современная живопись','Казанское художественное училище, стажировка в Праге','assets/img/a-maria.jpg','Мария Лебедева пишет натюрморты и городские сцены плотным, почти скульптурным мазком. Её цветы — попытка поймать момент, когда букет уже начал увядать и оттого стал только интереснее.','Участница выставок в Казани, Москве и Чехии (Прага, 2022). Пишет только с натуры.'],
    ];
    $st = $pdo->prepare("INSERT INTO artists(name,years,place,style,education,photo,bio,fact) VALUES(?,?,?,?,?,?,?,?)");
    foreach ($artists as $a) $st->execute($a);
    $paintings = [
        [1,'Тихая заводь','Импрессионизм','Пейзаж',2023,'Масло','Холст на подрамнике','70 × 90','Деревянный багет, тёмный дуб',148000,'available','Гостиная, спальня','Подпись на лицевой стороне, сертификат галереи № 114','assets/img/p-river.jpg','Утро на небольшой реке под Вологдой. Воробьёва писала работу с натуры в несколько сеансов: вода в заводь почти не движется, поэтому небо отражается в ней целиком.'],
        [2,'Портрет незнакомки','Академическая живопись','Портрет',1987,'Масло','Холст','55 × 46','Багет под бронзу',260000,'available','Кабинет, библиотека','Подпись и дата на обороте, заключение архива наследников','assets/img/p-portrait.jpg','Один из «домашних» портретов Соколова: моделью была знакомая семьи художника. Работа написана сдержанно, почти монохромно, и именно поэтому лицо держит внимание.'],
        [3,'Пионы на старом столе','Современная живопись','Натюрморт',2024,'Масло, мастихин','Холст на подрамнике','60 × 60','Без рамы (торцы прокрашены)',86000,'available','Кухня-гостиная, спальня','Подпись на обороте, сертификат галереи № 121','assets/img/p-flowers.jpg','Пионы Соколова пишет каждый июнь — и каждый раз успевает всего несколько работ, пока цветы не осыпались. Мазок крупный, вблизи картина почти абстрактная.'],
        [1,'Море перед грозой','Импрессионизм','Марина',2021,'Масло','Холст на картоне','50 × 70','Светлый деревянный багет',92000,'reserved','Гостиная, офис','Подпись на обороте, сертификат галереи № 098','assets/img/p-sea.jpg','Этюд, написанный в Крыму, Карелии, за один сеанс: небо уже потемнело, а море ещё держит свет.'],
        [3,'Улица с трамваем','Постимпрессионизм','Городской пейзаж',2022,'Масло, мастихин','Холст на подрамнике','80 × 65','Тёмный деревянный багет',118000,'available','Прихожая, гостиная','Подпись на лицевой стороне, сертификат галереи № 107','assets/img/p-city.jpg','Старый район Санкт-Петербурга после дождя. Тот самый трамвайный маршрут, по которому художница ездила в училище.'],
        [2,'Дама в интерьере','Академическая живопись','Портрет',1991,'Масло','Холст','65 × 54','Багет под бронзу',230000,'available','Кабинет, гостиная','Заключение архива наследников, подпись на обороте','assets/img/p-lady.jpg','Поздний портрет Соколова, написанный свободнее ранних работ: художнику под семьдесят, и он позволяет себе не выписывать каждую деталь.'],
        [1,'Поле в конце августа','Реализм','Пейзаж',2020,'Масло','Холст на подрамнике','45 × 60','Деревянный багет, тёмный дуб',64000,'available','Спальня, детская','Подпись на обороте, сертификат галереи № 091','assets/img/p-field.jpg','Небольшой тёплый пейзаж: убранное поле, стога и высокое небо конца лета. Хороший вариант первой картины в дом.'],
        [3,'Кафе на углу','Постимпрессионизм','Городской пейзаж',2023,'Масло, мастихин','Холст на подрамнике','70 × 85','Тёмный деревянный багет',132000,'sold','Гостиная, кафе, офис','Подпись на лицевой стороне, сертификат галереи № 116','assets/img/p-cafe.jpg','Вечернее кафе в старой части города. Работа продана, но оставлена в каталоге как пример: художница может написать вариацию под заказ.'],
        [3,'Натюрморт с белыми цветами','Современная живопись','Натюрморт',2021,'Масло','Холст на картоне','50 × 50','Светлый багет',54000,'available','Спальня, кухня','Подпись на обороте, сертификат галереи № 088','assets/img/p-still.jpg','Ранняя работа Соколовой, ещё до её фирменного крупного мазка. Тихий натюрморт в светлых тонах.'],
    ];
    $st = $pdo->prepare("INSERT INTO paintings(artist_id,title,style,genre,year,technique,material,size,frame,price,status,room,certificate,photo,description) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
    foreach ($paintings as $p) $st->execute($p);
}
