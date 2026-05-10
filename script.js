/**
 * Словник для статичних елементів інтерфейсу
 */
const translations = {
    'uk': {
        'all': 'УСІ',
        'news': 'НОВИНИ',
        'pub': 'ПУБЛІКАЦІЇ',
        'col': 'КОЛОНКИ',
        'int': 'БЕСІДА',
        'blog': 'БЛОГИ',
        'arch': 'АРХІВ',
        'support': 'ПІДТРИМАТИ',
        'latest': 'НОВИНИ ЗА',
        'published': 'Опубліковано',
        'not_found': 'Новин не знайдено'
    },
    'en': {
        'all': 'ALL',
        'news': 'NEWS',
        'pub': 'PUBLICATIONS',
        'col': 'COLUMNS',
        'int': 'CONVERSATIONS',
        'blog': 'BLOGS',
        'arch': 'ARCHIVE',
        'support': 'SUPPORT',
        'latest': 'NEWS FOR',
        'published': 'Published',
        'not_found': 'No news found'
    }
};

let currentLang = localStorage.getItem('lang') || 'uk';

/**
 * Перемикання теми (День/Ніч)
 */
function toggleTheme() {
    const body = document.body;
    const themeBtn = document.getElementById('theme-toggle');
    
    const isDark = body.classList.toggle('dark-mode');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
    
    if (themeBtn) {
        themeBtn.innerText = isDark ? '☀️' : '🌙';
    }
    // Оновлюємо колір активних категорій після зміни теми
    const activeLink = document.querySelector('.nav-container a[style*="rgb(224, 30, 30)"]');
    const currentCat = activeLink ? activeLink.innerText : 'all';
    updateActiveCategory(currentCat);
}

/**
 * Функція автоматичного перекладу тексту
 */
async function translateText(text, toLang = 'en') {
    if (!text || currentLang === 'uk') return text;
    try {
        const url = `https://translate.googleapis.com/translate_a/single?client=gtx&sl=uk&tl=${toLang}&dt=t&q=${encodeURI(text)}`;
        const response = await fetch(url);
        const data = await response.json();
        return data[0].map(item => item[0]).join('');
    } catch (e) {
        console.error("Translation error:", e);
        return text;
    }
}

/**
 * Перемикання мови
 */
function changeLanguage(lang) {
    currentLang = lang;
    localStorage.setItem('lang', lang);
    renderInterface();
    loadNews('all'); 
}

/**
 * Оновлення статичних написів
 */
function renderInterface() {
    const t = translations[currentLang];
    
    const btnSupport = document.getElementById('btn-support');
    if (btnSupport) {
        btnSupport.innerText = t.support;
    }

    const navContainer = document.getElementById('category-menu');
    if (navContainer) {
        navContainer.innerHTML = `
            <a href="#" onclick="loadNews('all')">${t.all}</a>
            <a href="#" onclick="loadNews('НОВИНИ')">${t.news}</a>
            <a href="#" onclick="loadNews('ПУБЛІКАЦІЇ')">${t.pub}</a>
            <a href="#" onclick="loadNews('КОЛОНКИ')">${t.col}</a>
            <a href="#" onclick="loadNews('БЕСІДА')">${t.int}</a> 
            <a href="#" onclick="loadNews('БЛОГИ')">${t.blog}</a>
            <a href="archive.php">${t.arch}</a>
        `;
    }

    document.querySelectorAll('.lang-link').forEach(link => {
        link.classList.remove('active');
        if (link.id === `lang-${currentLang}`) {
            link.classList.add('active');
        }
    });
}

/**
 * Завантаження списку новин
 */
async function loadNews(category = 'all') {
    const urlParams = new URLSearchParams(window.location.search);
    const today = new Date();
    const currentDay2026 = `2026-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
    const dateParam = urlParams.get('date') || currentDay2026;

    try {
        let url = `db.php?action=get_news&date=${dateParam}`;
        const response = await fetch(url);
        let news = await response.json();
        const feed = document.getElementById('news-feed');
        const sidebarTitle = document.querySelector('.sidebar-title');

        if (sidebarTitle) {
            sidebarTitle.innerText = `${translations[currentLang].latest} ${dateParam}`;
        }

        updateActiveCategory(category);

        if (category !== 'all') {
            news = news.filter(item => item.category === category);
        }

        if (!news || news.length === 0) {
            feed.innerHTML = `<p style="color:gray; padding:10px;">${translations[currentLang].not_found}</p>`;
            document.getElementById('a-title').innerText = "";
            document.getElementById('a-body').innerHTML = "";
            return;
        }

        feed.innerHTML = ''; 
        for (const [index, item] of news.entries()) {
            const div = document.createElement('div');
            div.className = 'news-item';
            const timeStr = item.created_at.substring(11, 16);

            const displayTitle = (currentLang === 'en') ? await translateText(item.title, 'en') : item.title;

            div.innerHTML = `
                <span class="time">${timeStr}</span>
                <a href="#" class="news-link">${displayTitle}</a>
            `;
            
            div.onclick = (e) => { 
                e.preventDefault(); 
                showFull(item); 
            };
            
            feed.appendChild(div);
            if(index === 0) showFull(item);
        }
    } catch (e) { 
        console.error("Помилка завантаження новин:", e); 
    }
}


/**
 * Відображення повної новини
 */
async function showFull(item) {
    const titleElem = document.getElementById('a-title');
    const infoElem = document.getElementById('a-info');
    const bodyElem = document.getElementById('a-body');

    if (!titleElem || !infoElem || !bodyElem) return;

    // Збільшуємо лічильник в БД
    incrementViews(item.id);
    const currentViews = parseInt(item.views || 0) + 1;

    let displayCategory = item.category || 'НОВИНИ';
    let displayTitle = item.title;
    let displayContent = item.content;

    if (currentLang === 'en') {
        titleElem.innerText = "Translating...";
        displayCategory = await translateText(displayCategory, 'en');
        displayTitle = await translateText(displayTitle, 'en');
        displayContent = await translateText(displayContent, 'en');
    }

    // Рендер заголовка
    titleElem.innerHTML = `
        <span style="color:#e01e1e; font-size:14px; display:block; text-transform:uppercase; margin-bottom:5px; font-weight:bold;">
            ${displayCategory}
        </span>
        ${displayTitle}`;

    infoElem.innerText = `${translations[currentLang].published}: ${item.created_at}`;
    
    let html = "";
    
    // Блок зображення з новим контейнером для CSS
    if (item.image) {
        html += `
            <div class="article-image-container">
                <img src="uploads/${item.image}" alt="Article image">
                <div class="photo-caption">${displayCategory} УКРАЇНИ</div>
            </div>
        `;
    }

    // Футер з іконками та переглядами (виправлений порядок для верстки)
    html += `
        <div class="news-footer">
            <div class="social-icons">
                <img src="icons/fb.png" class="icon-file" alt="FB">
                <img src="icons/x.png" class="icon-file" alt="X">
                <img src="icons/tg.png" class="icon-file" alt="TG">
                <img src="icons/link.png" class="icon-file" alt="Link">
            </div>
            <div class="views-box">
                <img src="icons/eye.png" class="eye-icon" alt="Views">
                <span class="views-num">${currentViews}</span>
            </div>
        </div>
        <div class="article-body-text">${displayContent}</div>
    `;
    
    bodyElem.innerHTML = html;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

/**
 * Оновлення активного стану навігації
 */
function updateActiveCategory(activeCat) {
    const links = document.querySelectorAll('.nav-container a');
    const isDark = document.body.classList.contains('dark-mode');
    
    links.forEach(link => {
        const onClickAttr = link.getAttribute('onclick');
        // Перевіряємо, чи це посилання на архів або чи містить категорію
        const isMatch = onClickAttr && onClickAttr.includes(`'${activeCat}'`);
        
        if (isMatch) {
            link.style.color = '#e01e1e';
        } else {
            link.style.color = isDark ? '#ffffff' : '#1a1a1a';
        }
    });
}

/**
 * Запит до БД для оновлення лічильника
 */
async function incrementViews(id) {
    try { 
        await fetch(`db.php?action=update_views&id=${id}`); 
    } catch (e) {
        console.warn("View increment failed");
    }
}

/**
 * Ініціалізація
 */
document.addEventListener('DOMContentLoaded', () => {
    const savedTheme = localStorage.getItem('theme');
    const themeBtn = document.getElementById('theme-toggle');
    
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-mode');
        if (themeBtn) themeBtn.innerText = '☀️';
    } else {
        document.body.classList.remove('dark-mode');
        if (themeBtn) themeBtn.innerText = '🌙';
    }

    renderInterface();
    loadNews('all');
});
