<?php

// Nap cac file cau hinh he thong (duong dan, thong so DB).
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php'; 

// Nap cac lop nen tang cua MVC tu thu cong (chua dung autoload).
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Router.php';

// Nap cac lop ung dung hien dang duoc route su dung.
require_once __DIR__ . '/../app/controllers/PageController.php';

// Dang ky tat ca route web.
require_once __DIR__ . '/../routes/web.php';

// Dieu huong request hien tai toi dung handler da khai bao trong Router.
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
