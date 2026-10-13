<?php
// Скопируйте этот файл в config.php и впишите данные базы с хостинга (раздел «Базы данных MySQL» в панели).
return [
    'db_driver' => 'mysql',                 // на хостинге: mysql
    'db_host'   => 'localhost',
    'db_name'   => 'ИМЯ_БАЗЫ',
    'db_user'   => 'ПОЛЬЗОВАТЕЛЬ_БАЗЫ',
    'db_pass'   => 'ПАРОЛЬ_БАЗЫ',
    'db_sqlite' => __DIR__ . '/../data/artos.sqlite', // используется только при db_driver=sqlite (локальная разработка)
    'site_name' => 'Artos — галерея живописи',
    'base_url'  => '', // обычно пусто, если сайт в корне домена artos.by
];
