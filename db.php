<?php
$host = 'localhost';
$user = 'root';
$pass = ''; 
$db_name = 'pravda_db';

$link = mysqli_connect($host, $user, $pass, $db_name);

if (!$link) {
    die("Помилка підключення до бази");
}

mysqli_set_charset($link, "utf8mb4");

// Отримання новин
if (isset($_GET['action']) && $_GET['action'] == 'get_news') {
    // Перевіряємо, чи передана конкретна дата через GET
    $date = isset($_GET['date']) ? mysqli_real_escape_string($link, $_GET['date']) : null;

    if ($date) {
        // Вибираємо новини ТІЛЬКИ за вказану дату (ігноруючи час)
        $query = "SELECT * FROM news WHERE DATE(created_at) = '$date' ORDER BY created_at DESC";
    } else {
        // Якщо дати немає (наприклад, на головній), беремо останні новини взагалі
        $query = "SELECT * FROM news ORDER BY created_at DESC LIMIT 50";
    }

    $result = mysqli_query($link, $query);
    $news = mysqli_fetch_all($result, MYSQLI_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode($news);
    exit;
}

// Оновлення переглядів
if (isset($_GET['action']) && $_GET['action'] == 'update_views' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    mysqli_query($link, "UPDATE news SET views = views + 1 WHERE id = $id");
    exit;
}
?>