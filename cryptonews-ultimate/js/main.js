// CryptoNews v4.3 - Полностью переписанный JavaScript
// Все функции работают надежно

// ===== ПЕРЕВОДЫ =====
const translations = {
    en: {popular:'🔥 Popular News',views:'views',share:'Share this article',read_also:'📰 Read Also',search:'Search',search_title:'Search',no_posts:'No posts found',no_results:'No results found',results:'results',footer_links:'Links',footer_about:'About',footer_contact:'Contact',footer_powered:'Powered by CryptoNews v4.3'},
    ru: {popular:'🔥 Популярные новости',views:'просмотров',share:'Поделиться статьей',read_also:'📰 Читайте также',search:'Поиск',search_title:'Поиск',no_posts:'Записей не найдено',no_results:'Результатов не найдено',results:'результатов',footer_links:'Ссылки',footer_about:'О нас',footer_contact:'Контакты',footer_powered:'Работает на CryptoNews v4.3'},
    es: {popular:'🔥 Noticias populares',views:'vistas',share:'Compartir este artículo',read_also:'📰 Leer también',search:'Buscar',search_title:'Búsqueda',no_posts:'No se encontraron publicaciones',no_results:'No se encontraron resultados',results:'resultados',footer_links:'Enlaces',footer_about:'Acerca de',footer_contact:'Contacto',footer_powered:'Funciona con CryptoNews v4.3'},
    de: {popular:'🔥 Beliebte Nachrichten',views:'Aufrufe',share:'Diesen Artikel teilen',read_also:'📰 Lesen Sie auch',search:'Suche',search_title:'Suche',no_posts:'Keine Beiträge gefunden',no_results:'Keine Ergebnisse gefunden',results:'Ergebnisse',footer_links:'Links',footer_about:'Über uns',footer_contact:'Kontakt',footer_powered:'Betrieben von CryptoNews v4.3'},
    fr: {popular:'🔥 Actualités populaires',views:'vues',share:'Partager cet article',read_also:'📰 Lire aussi',search:'Recherche',search_title:'Recherche',no_posts:'Aucun article trouvé',no_results:'Aucun résultat trouvé',results:'résultats',footer_links:'Liens',footer_about:'À propos',footer_contact:'Contact',footer_powered:'Propulsé par CryptoNews v4.3'}
};

let contentCache = {};
let currentLang = 'en';

// ===== ФУНКЦИЯ ПЕРЕВОДА СТРАНИЦЫ =====
function translatePage(lang) {
    currentLang = lang;

    // Переводим элементы с data-translate
    document.querySelectorAll('[data-translate]').forEach(function(el) {
        const key = el.getAttribute('data-translate');
        if (translations[lang] && translations[lang][key]) {
            el.textContent = translations[lang][key];
        }
    });

    // Переводим placeholder
    document.querySelectorAll('[data-translate-placeholder]').forEach(function(el) {
        const key = el.getAttribute('data-translate-placeholder');
        if (translations[lang] && translations[lang][key]) {
            el.placeholder = translations[lang][key] + '...';
        }
    });

    // Контент постов и даты/время не переводим — только интерфейс

    console.log('Page translated to: ' + lang);
}

function translateContent(text, targetLang, callback) {
    const maxLength = 4500;
    const textToTranslate = text.replace(/<[^>]*>/g, ' ').substring(0, maxLength);
    const langMap = {'ru':'ru-RU','es':'es-ES','de':'de-DE','fr':'fr-FR'};
    const targetCode = langMap[targetLang] || targetLang;
    const apiUrl = 'https://api.mymemory.translated.net/get?q=' + encodeURIComponent(textToTranslate) + '&langpair=en|' + targetCode;

    fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            if (data.responseData && data.responseData.translatedText) {
                const translated = data.responseData.translatedText;
                const formatted = '<p>' + translated.replace(/\n/g, '</p><p>') + '</p>';
                callback(formatted);
            } else {
                callback(text);
            }
        })
        .catch(err => {
            console.log('Translation error:', err);
            callback(text);
        });
}

// ===== КНОПКА ПЕРЕКЛЮЧЕНИЯ ТЕМЫ =====
function initThemeToggle() {
    const themeBtn = document.getElementById('themeToggle');
    if (!themeBtn) return;

    // Загружаем сохраненную тему
    if (document.cookie.indexOf('theme_mode=night') !== -1) {
        document.body.classList.add('night-mode');
        themeBtn.textContent = '☀️';
    }

    // Обработчик клика
    themeBtn.addEventListener('click', function() {
        if (document.body.classList.contains('night-mode')) {
            document.body.classList.remove('night-mode');
            themeBtn.textContent = '🌙';
            document.cookie = 'theme_mode=day;path=/;max-age=31536000';
        } else {
            document.body.classList.add('night-mode');
            themeBtn.textContent = '☀️';
            document.cookie = 'theme_mode=night;path=/;max-age=31536000';
        }
    });
}

// ===== КНОПКА ПЕРЕКЛЮЧЕНИЯ ЯЗЫКА =====
function initLangSwitch() {
    const langSwitcher = document.getElementById('langSwitcher');
    const langCurrent = document.getElementById('langCurrent');
    const langBtns = document.querySelectorAll('.lang-btn');

    if (!langSwitcher || !langCurrent) return;

    // Загружаем сохраненный язык
    const savedLangMatch = document.cookie.match(/site_lang=([^;]+)/);
    if (savedLangMatch) {
        currentLang = savedLangMatch[1];
        document.querySelector('.current-lang-code').textContent = currentLang.toUpperCase();
        langBtns.forEach(function(btn) {
            btn.classList.remove('active');
            if (btn.getAttribute('data-lang') === currentLang) {
                btn.classList.add('active');
            }
        });
        document.body.setAttribute('data-lang', currentLang);
    }

    // Клик по кнопке текущего языка
    langCurrent.addEventListener('click', function(e) {
        e.stopPropagation();
        langSwitcher.classList.toggle('active');
    });

    // Клики по кнопкам выбора языка
    langBtns.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const lang = this.getAttribute('data-lang');
            const langCode = lang.toUpperCase();

            document.querySelector('.current-lang-code').textContent = langCode;
            langBtns.forEach(function(b) { b.classList.remove('active'); });
            this.classList.add('active');
            langSwitcher.classList.remove('active');

            document.cookie = 'site_lang=' + lang + ';path=/;max-age=31536000';
            document.body.setAttribute('data-lang', lang);

            translatePage(lang);
        });
    });

    // Закрытие при клике вне
    document.addEventListener('click', function() {
        langSwitcher.classList.remove('active');
    });
}

// ===== ОБНОВЛЕНИЕ ЦЕН КРИПТОВАЛЮТ =====
function updateCryptoPrices() {
    const coins = ['bitcoin','ethereum','tether','binancecoin','ripple','solana','cardano','dogecoin','tron','matic-network','polkadot','litecoin','shiba-inu','avalanche-2','chainlink'];
    const apiUrl = 'https://api.coingecko.com/api/v3/simple/price?ids=' + coins.join(',') + '&vs_currencies=usd&include_24hr_change=true';

    fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            document.querySelectorAll('.ticker-item').forEach(function(item) {
                const crypto = item.getAttribute('data-crypto');
                if (data[crypto]) {
                    const price = data[crypto].usd;
                    const change = data[crypto].usd_24h_change;

                    const priceEl = item.querySelector('.price-value');
                    const changeEl = item.querySelector('.change-value');
                    const changeWrap = item.querySelector('.ticker-change');

                    if (priceEl) {
                        priceEl.textContent = price < 1 ? price.toFixed(6) : price.toFixed(2);
                    }
                    if (changeEl) {
                        changeEl.textContent = change.toFixed(2);
                    }
                    if (changeWrap) {
                        changeWrap.classList.remove('up', 'down');
                        changeWrap.classList.add(change >= 0 ? 'up' : 'down');
                    }
                }
            });
            console.log('Crypto prices updated');
        })
        .catch(err => console.log('Price update error:', err));
}

// ===== РЕЙТИНГ ПОСТОВ =====
function initRating() {
    if (typeof jQuery === 'undefined') return;

    jQuery('.rating-stars.interactive .star').on('click', function() {
        const rating = jQuery(this).closest('.post-rating');
        const postId = rating.data('post-id');
        const vote = jQuery(this).data('value');

        jQuery.ajax({
            url: cryptoAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'vote_rating',
                post_id: postId,
                vote: vote,
                nonce: cryptoAjax.nonce
            },
            success: function(response) {
                if (response.success) {
                    const newRating = response.data.rating;
                    const newCount = response.data.count;
                    const full = Math.floor(newRating);
                    let stars = '';
                    for (let i = 1; i <= 5; i++) {
                        stars += '<span class="star" data-value="' + i + '">' + (i <= full ? '★' : '☆') + '</span>';
                    }
                    rating.find('.rating-stars').html(stars);
                    rating.find('.rating-count').text('(' + newCount + ')');
                }
            }
        });
    });
}

// ===== АНИМАЦИЯ КАРТОЧЕК =====
function initCardAnimation() {
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.post-card, .featured-card').forEach(function(card) {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.5s, transform 0.5s';
        observer.observe(card);
    });
}

// ===== ИНИЦИАЛИЗАЦИЯ ПРИ ЗАГРУЗКЕ =====
document.addEventListener('DOMContentLoaded', function() {
    // Инициализируем все функции
    initThemeToggle();
    initLangSwitch();
    initCardAnimation();

    // Переводим страницу на сохраненный язык
    translatePage(currentLang);

    // Обновляем цены криптовалют
    if (document.getElementById('cryptoTicker')) {
        updateCryptoPrices();
        setInterval(updateCryptoPrices, 60000); // Каждую минуту
    }

    // Инициализируем рейтинг (если jQuery доступен)
    if (typeof jQuery !== 'undefined') {
        jQuery(document).ready(function() {
            initRating();
        });
    }

    console.log('CryptoNews v4.3 initialized');
});
