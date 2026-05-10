-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Май 10 2026 г., 23:26
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `pravda_db`
--

-- --------------------------------------------------------

--
-- Структура таблицы `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `views` int(11) DEFAULT 0,
  `category` varchar(50) DEFAULT 'НОВИНИ',
  `guid` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `news`
--

INSERT INTO `news` (`id`, `title`, `image`, `content`, `created_at`, `views`, `category`, `guid`) VALUES
(6, 'Зеленський розповів, з ким контактує Україна щодо експорту зброї', '1777404729_ba6aadbc0f4ed648812348cd8bd929f7.webp', 'Президент Володимир Зеленський розповів, що Україна контактує з країнами Близького Сходу та Затоки, Європи, Кавказу та американськими партнерами щодо потенційного експорту зброї, яку в ОП назвали Drone Deals.\r\n\r\nПряма мова: \"Наш особливий формат роботи з партнерами, які допомагають Україні, – Drone Deals – уже в роботі з країнами із трьох частин світу: Близький Схід та Затока, Європа та Кавказ.\r\n\r\nЄ пропозиція на столі в наших американських партнерів.\r\n\r\nІ це про дрони, про системи оборонні, наші інші види зброї, які потрібні, щоб у повітрі, на землі та на морі можна було будувати реальний захист\".\r\n\r\nДеталі: Зеленський наголосив, що умови мають бути вигідні Україні, має бути чіткий контроль, і гроші за експорт мають допомагати країні захищатись.\r\n\r\nНагадаємо:\r\n\r\n- 28 квітня президент Володимир Зеленський провів нараду щодо експорту української зброї і запропонував партнерам Drone Deals. Україна також готує список країн, до яких експорт буде неможливим через співпрацю з РФ.\r\n\r\n- На думку голови Офісу президента Кирила Буданова український ОПК наразі не може вільно працювати на експорт, однак Україна може продавати те, що є в надлишку, як-от морські дрони.', '2026-04-28 19:32:09', 21, 'НОВИНИ', NULL);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `guid` (`guid`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
