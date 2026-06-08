<?php
date_default_timezone_set('Europe/Kyiv');
$host = 'localhost';
$user = 'root';
$pass = ''; 
$db_name = 'pravda_db';
$link = mysqli_connect($host, $user, $pass, $db_name);
if (!$link) {
    die("Помилка підключення до бази");
}
mysqli_set_charset($link, "utf8mb4");
$year = intval(date('Y')); 
$dates_query = "SELECT DISTINCT DATE(created_at) as news_date FROM news WHERE YEAR(created_at) = $year";
$dates_result = mysqli_query($link, $dates_query);
$active_dates = [];
while ($row = mysqli_fetch_assoc($dates_result)) {
    $active_dates[] = $row['news_date'];
}
$months = [
    1 => "Січень", 2 => "Лютий", 3 => "Березень", 4 => "Квітень",
    5 => "Травень", 6 => "Червень", 7 => "Липень", 8 => "Серпень",
    9 => "Вересень", 10 => "Жовтень", 11 => "Листопад", 12 => "Грудень"
];
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>АРХІВ <?= $year ?> — НОВИНИ</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .archive-container { margin-top: 30px; margin-bottom: 50px; }
        .archive-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 25px; margin-top: 30px; }
        .calendar-month { border: 1px solid var(--border-color); padding: 15px; border-radius: 8px; transition: var(--transition); }
        .month-title { color: var(--accent-color); text-transform: uppercase; font-weight: 800; font-size: 14px; margin-bottom: 12px; border-bottom: 1px solid var(--border-color); padding-bottom: 5px; text-align: left; }
        .days-grid { display: grid; grid-template-columns: repeat(7, 1fr); text-align: center; }
        .day-name { color: var(--text-secondary); font-size: 11px; margin-bottom: 8px; font-weight: bold; text-transform: uppercase; }
        .day { padding: 8px 0; border-radius: 4px; transition: 0.2s; color: var(--text-color); font-size: 13px; font-weight: 500; }
        .day.has-news { 
            font-weight: 900 !important; 
            color: var(--accent-color); 
            cursor: pointer; 
        }
        .day.has-news:hover { 
            background: var(--accent-color); 
            color: #fff !important; 
        }
        .day.no-news { 
            color: #ccc; 
            cursor: default; 
            opacity: 0.4; 
            pointer-events: none; 
        }
        .day.today { 
            border: 2px solid var(--accent-color);
            font-weight: bold;
        }
        .empty { padding: 8px 0; }
        .year-btn { padding: 6px 25px; background: var(--accent-color); border-radius: 20px; color: #fff; font-weight: 800; font-size: 14px; }
        body.dark-mode .calendar-month { background: #1a1a1a; }
        .lang-link.active { color: var(--accent-color); text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <div style="padding: 20px 0; display: flex; justify-content: space-between; align-items: center;">
            <a href="index.html" id="back-link" style="color: var(--text-secondary); text-decoration: none; font-size: 14px; font-weight: bold;">← НА ГОЛОВНУ</a>
            <div class="lang-switcher">
                <a href="javascript:void(0)" class="lang-link" data-lang="uk" onclick="setLang('uk')">УКР</a>
                <a href="javascript:void(0)" class="lang-link" data-lang="en" onclick="setLang('en')">ENG</a>
            </div>
        </div>
        <div class="archive-container">
            <h1 id="archive-main-title" style="text-align:center; color: var(--text-color); font-weight: 900; letter-spacing: 1px;">АРХІВ <?= $year ?></h1>
            <div class="years-nav" style="text-align:center; margin: 20px 0;">
                <span class="year-btn"><?= $year ?></span>
            </div>
            <div class="archive-grid">
                <?php foreach($months as $num => $name): 
                    $firstDay = date('N', strtotime("$year-$num-01"));
                    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $num, $year);
                    $todayDate = date('Y-m-d');
                ?>
                    <div class="calendar-month">
                        <div class="month-title" data-month="<?= $num ?>"><?= mb_strtoupper($name) ?></div>
                        <div class="days-grid">
                            <div class="day-name" data-day="1">пн</div>
                            <div class="day-name" data-day="2">вт</div>
                            <div class="day-name" data-day="3">ср</div>
                            <div class="day-name" data-day="4">чт</div>
                            <div class="day-name" data-day="5">пт</div>
                            <div class="day-name" data-day="6">сб</div>
                            <div class="day-name" data-day="7">нд</div>
                            <?php 
                            for($i = 1; $i < $firstDay; $i++) echo '<div class="empty"></div>';
                            for($d = 1; $d <= $daysInMonth; $d++): 
                                $fullDate = sprintf("%04d-%02d-%02d", $year, $num, $d);
                                $hasNews = in_array($fullDate, $active_dates);
                                $class = ($hasNews) ? 'day has-news' : 'day no-news';
                                if ($fullDate == $todayDate) $class .= ' today';
                                $on_click = ($hasNews) ? "location.href='index.html?date=$fullDate'" : "";
                            ?>
                                <div class="<?= $class ?>" onclick="<?= $on_click ?>"><?= $d ?></div>
                            <?php endfor; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <script>
        const currentYear = <?= $year ?>;
        const archiveTranslations = {
            'uk': { 
                'title': `АРХІВ ${currentYear}`, 
                'back': '← НА ГОЛОВНУ', 
                'months': ['СІЧЕНЬ', 'ЛЮТИЙ', 'БЕРЕЗЕНЬ', 'КВІТЕНЬ', 'ТРАВЕНЬ', 'ЧЕРВЕНЬ', 'ЛИПЕНЬ', 'СЕРПЕНЬ', 'ВЕРЕСЕНЬ', 'ЖОВТЕНЬ', 'ЛИСТОПАД', 'ГРУДЕНЬ'], 
                'days': ['пн', 'вт', 'ср', 'чт', 'пт', 'сб', 'нд'] 
            },
            'en': { 
                'title': `ARCHIVE ${currentYear}`, 
                'back': '← BACK TO MAIN', 
                'months': ['JANUARY', 'FEBRUARY', 'MARCH', 'APRIL', 'MAY', 'JUNE', 'JULY', 'AUGUST', 'SEPTEMBER', 'OCTOBER', 'NOVEMBER', 'DECEMBER'], 
                'days': ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'] 
            }
        };
        function setLang(lang) {
            localStorage.setItem('lang', lang);
            applyArchiveTranslation();
        }
        function applyArchiveTranslation() {
            const lang = localStorage.getItem('lang') || 'uk';
            const t = archiveTranslations[lang];
            document.getElementById('archive-main-title').innerText = t.title;
            document.getElementById('back-link').innerText = t.back;
            document.querySelectorAll('.month-title').forEach(el => { 
                const mIndex = parseInt(el.getAttribute('data-month')) - 1;
                el.innerText = t.months[mIndex]; 
            });
            document.querySelectorAll('.day-name').forEach(el => { 
                const dIndex = parseInt(el.getAttribute('data-day')) - 1;
                el.innerText = t.days[dIndex]; 
            });
            document.querySelectorAll('.lang-link').forEach(link => {
                link.classList.toggle('active', link.getAttribute('data-lang') === lang);
            });
        }
        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark-mode');
        }
        document.addEventListener('DOMContentLoaded', applyArchiveTranslation);
    </script>
</body>
</html>