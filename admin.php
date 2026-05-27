<?php require_once 'db.php'; ?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Панель керування | UA News</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="admin-body">
<div class="admin-container">
    <h1 class="admin-title">Додати нову публікацію</h1>
    <form class="admin-form" method="POST" enctype="multipart/form-data">
        <label>Заголовок новини</label>
        <input type="text" name="title" required placeholder="Про що новина?">
        <label>Категорія матеріалу</label>
        <select name="category" required>
            <option value="НОВИНИ">НОВИНИ</option>
            <option value="ПУБЛІКАЦІЇ">ПУБЛІКАЦІЇ</option>
            <option value="КОЛОНКИ">КОЛОНКИ</option>
            <option value="БЕСІДА">БЕСІДА</option>
            <option value="СПЕЦПРОЄКТИ">СПЕЦПРОЄКТИ</option>
            <option value="БЛОГИ">БЛОГИ</option>
            <option value="АРХІВ">АРХІВ</option>
        </select>
        <label>Головне зображення</label>
        <input type="file" name="image" accept="image/*">
        <label>Текст статті</label>
        <textarea name="content" rows="10" required placeholder="Пишіть тут..."></textarea>
        <button type="submit" name="add" class="btn-publish">ОПУБЛІКУВАТИ</button>
    </form>
    <hr class="admin-hr">
    <div class="admin-news-section">
        <div class="admin-header-flex">
            <h3 class="section-label">Список усіх новин</h3>
            <form method="GET" class="admin-filter-form">
                <select name="f_cat" onchange="this.form.submit()">
                    <option value="">Усі категорії</option>
                    <option value="НОВИНИ" <?php if(@$_GET['f_cat'] == 'НОВИНИ') echo 'selected'; ?>>НОВИНИ</option>
                    <option value="ПУБЛІКАЦІЇ" <?php if(@$_GET['f_cat'] == 'ПУБЛІКАЦІЇ') echo 'selected'; ?>>ПУБЛІКАЦІЇ</option>
                    <option value="КОЛОНКИ" <?php if(@$_GET['f_cat'] == 'КОЛОНКИ') echo 'selected'; ?>>КОЛОНКИ</option>
                    <option value="БЕСІДА" <?php if(@$_GET['f_cat'] == "БЕСІДА") echo 'selected'; ?>>БЕСІДА</option>
                    <option value="БЛОГИ" <?php if(@$_GET['f_cat'] == 'БЛОГИ') echo 'selected'; ?>>БЛОГИ</option>
                </select>
            </form>
        </div>
        <div class="admin-news-list">
            <?php
            if (isset($_POST['add'])) {
                $t = mysqli_real_escape_string($link, $_POST['title']);
                $c = mysqli_real_escape_string($link, $_POST['content']);
                $cat = mysqli_real_escape_string($link, $_POST['category']);
                $imgName = "";
                if (!empty($_FILES['image']['name'])) {
                    $imgName = time() . "_" . $_FILES['image']['name'];
                    move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $imgName);
                }
                $sql = "INSERT INTO news (title, content, image, category, views) VALUES ('$t', '$c', '$imgName', '$cat', 0)";
                mysqli_query($link, $sql);
                echo "<script>window.location.href='admin.php';</script>";
            }
            if (isset($_GET['delete'])) {
                $id = intval($_GET['delete']);
                mysqli_query($link, "DELETE FROM news WHERE id = $id");
                echo "<script>window.location.href='admin.php';</script>";
            }
            $where_clause = "";
            if (!empty($_GET['f_cat'])) {
                $f_cat = mysqli_real_escape_string($link, $_GET['f_cat']);
                $where_clause = " WHERE category = '$f_cat' ";
            }
            $res = mysqli_query($link, "SELECT id, title, category FROM news $where_clause ORDER BY id DESC");
            if (mysqli_num_rows($res) > 0) {
                while($row = mysqli_fetch_assoc($res)) {
                    echo "
                    <div class='admin-news-item'>
                        <div class='admin-news-info'>
                            <span class='cat-tag'>{$row['category']}</span>
                            <span class='news-title-preview'>{$row['title']}</span>
                        </div>
                        <a href='admin.php?delete={$row['id']}' class='btn-delete' onclick=\"return confirm('Видалити?')\">Видалити</a>
                    </div>";
                }
            } else {
                echo "<p style='padding:20px; color:#666;'>У цій категорії поки що немає новин.</p>";
            }
            ?>
        </div>
    </div>
</div>
</body>
</html>