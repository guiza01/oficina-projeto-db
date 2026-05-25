<?php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'oficina_mecanica');
define('DB_PORT', 3306);

define('APP_NAME', 'Oficina do Guizão');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/oficina');
define('TIME_ZONE', 'America/Recife');

date_default_timezone_set(TIME_ZONE);

define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

define('STATUS_ABERTA', 'Aberta');
define('STATUS_EM_PROGRESSO', 'Em Progresso');
define('STATUS_CONCLUIDA', 'Concluida');
define('STATUS_CANCELADA', 'Cancelada');

define('ITEM_TIPO_SERVICO', 'SERVIÇO');
define('ITEM_TIPO_PECA', 'PEÇA');

session_start();
