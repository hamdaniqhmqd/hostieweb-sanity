let rtLanguageManager = {
    init: async function () {
        await this.ensureLanguageData();
        await this.loadLanguageConfig();
        await this.translateAllText();
        await this.getBaseLanguage();
    },

    ensureLanguageData: async function () {
        const today = new Date().toISOString().split('T')[0];
        const lastFetchedDate = localStorage.getItem('lastLangFetchedDate');
        if (lastFetchedDate !== today) {
            await this.fetchAndStoreLanguages();
        }
    },

    fetchAndStoreLanguages: async function () {
        try {
            const lang = await this.getUserPreferredLanguage();
            const response = await fetch(`assets/js/language/${lang}.json`);
            const data = await response.json();
            localStorage.setItem('translations', JSON.stringify(data));
            localStorage.setItem('lastLangFetchedDate', new Date().toISOString().split('T')[0]);
        } catch (error) {
            console.error('Error fetching languages:', error);
        }
    },

    getTranslations: function () {
        const stored = localStorage.getItem('translations');
        return stored ? JSON.parse(stored) : {};
    },

    getLanguageConfig: async function () {
        try {
            const response = await fetch('assets/js/language/configue.json');
            return await response.json();
        } catch (error) {
            console.error('Error loading language config:', error);
            return {
                baseLanguage: 'en',
                defaultLanguage: 'en',
                supportedLanguages: ['en']
            };
        }
    },

    getBaseLanguage: async function () {
        let stored = localStorage.getItem('defaultLanguage');
        if (stored) return stored;

        try {
            const config = await this.getLanguageConfig();
            const base = config.defaultLanguage || 'en';
            localStorage.setItem('defaultLanguage', base);
            return base;
        } catch {
            return 'en';
        }
    },

    updateUserPreferredLanguage: function (selected = 'en') {
        localStorage.setItem('userPreferredLanguage', selected.toLowerCase());
    },

    getUserPreferredLanguage: async function () {
        let preferred = localStorage.getItem('userPreferredLanguage');
        if (preferred) return preferred.toLowerCase();

        const config = await this.getLanguageConfig();
        return config.defaultLanguage?.toLowerCase() || 'en';
    },

    loadLanguageConfig: async function () {
        try {
            const config = await this.getLanguageConfig();
            const userLang = await this.getUserPreferredLanguage();

            const selectEl = document.querySelector('.easy-language-switcher-select');
            const toggleEl = document.querySelector('.easy-language-switcher-toggle .language-code');

            if (!selectEl || !config.supportedLanguages) return;

            // Kosongkan jika ada isi lama
            selectEl.innerHTML = '';

            config.supportedLanguages.forEach(code => {
                const li = document.createElement('li');
                li.setAttribute('data-value', code.toLowerCase());
                li.className = 'option-language';
                li.textContent = code.toUpperCase();
                selectEl.appendChild(li);
            });

            // Tambahkan event listener
            document.querySelectorAll('.easy-language-switcher-select li').forEach(option => {
                option.addEventListener('click', function () {
                    const selected = this.getAttribute('data-value');
                    rtLanguageManager.updateUserPreferredLanguage(selected);
                    location.reload(); // Refresh halaman setelah pilih bahasa
                });
            });

            if (toggleEl) toggleEl.textContent = userLang.toUpperCase();
        } catch (error) {
            console.error('Error rendering language config:', error);
        }
    },

    translateAllText: async function () {
        const lang = await this.getUserPreferredLanguage();
        try {
            const response = await fetch(`assets/js/language/${lang}.json`);
            const translations = await response.json();
            const elements = document.querySelectorAll('[rt-lang]');
            for (let el of elements) {
                const key = el.getAttribute('rt-lang');
                if (translations[key]) {
                    el.textContent = translations[key];
                }
            }
            localStorage.setItem('translations', JSON.stringify(translations));
        } catch (error) {
            console.error('Error translating text:', error);
        }
    }
};

// Inisialisasi saat DOM siap
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => rtLanguageManager.init());
} else {
    rtLanguageManager.init();
}
