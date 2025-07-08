let rtCurrencyConverter = {
    init: async function () {
        await this.ensureCurrencyData();
        await this.loadCurrencyConfig();
        await this.convertAllPricesCurrency();
        await this.renderPriceOptions();
        await this.getBaseCurrency();
    },

    ensureCurrencyData: async function () {
        const today = new Date().toISOString().split('T')[0];
        const lastFetchedDate = localStorage.getItem('lastFetchedDate');
        if (lastFetchedDate !== today) {
            await this.fetchAndStoreCurrency();
        }
    },

    fetchAndStoreCurrency: async function () {
        try {
            const response = await fetch('assets/js/currency/usd.json');
            const data = await response.json();
            localStorage.setItem('currencies', JSON.stringify(data.usd));
            localStorage.setItem('lastFetchedDate', new Date().toISOString().split('T')[0]);
        } catch (error) {
            console.error('Error fetching currencies:', error);
        }
    },

    getCurrencies: function () {
        const stored = localStorage.getItem('currencies');
        return stored ? JSON.parse(stored) : {};
    },

    getCurrencyConfig: async function () {
        try {
            const response = await fetch('assets/js/currency/configue.json');
            return await response.json();
        } catch (error) {
            console.error('Error loading currency config:', error);
            return {
                baseCurrency: 'usd',
                currency_thousand_separator: '.',
                currency_decimal_separator: ',',
                supportedCurrencies: ['usd']
            };
        }
    },

    getBaseCurrency: async function () {
        let stored = localStorage.getItem('defaultCurrency');
        if (stored) return stored;

        try {
            const config = await this.getCurrencyConfig();
            const base = config.defaultCurrency || 'usd';
            localStorage.setItem('defaultCurrency', base);
            return base;
        } catch {
            return 'usd';
        }
    },

    updateUserPreferedCurrency: function (selected = 'usd') {
        localStorage.setItem('updateUserPreferedCurrency', selected.toLowerCase());
    },

    getUserPreferedCurrency: async function () {
        let preferred = localStorage.getItem('updateUserPreferedCurrency');
        if (preferred) return preferred.toLowerCase();

        const config = await this.getCurrencyConfig();
        return config.defaultCurrency?.toLowerCase() || 'usd';
    },

    getCurrencySymbol: async function (currencyCode) {
        try {
            const response = await fetch('assets/js/currency/currencies.json');
            const data = await response.json();
            const match = data.find(item => item.cc.toLowerCase() === currencyCode.toLowerCase());
            return match?.symbol || '$';
        } catch {
            return '$';
        }
    },

    detectThousandSeparator: function (value) {
        let clean = value.replace(/[^\d.,]/g, '');
        if (clean.includes(',') && clean.includes('.')) {
            return clean.lastIndexOf(',') < clean.lastIndexOf('.') ? ',' : '.';
        } else if (clean.includes(',')) {
            return ',';
        } else if (clean.includes('.')) {
            return '.';
        }
        return null;
    },

    convertCurrency: async function (_, toCurrency, value) {
        const config = await this.getCurrencyConfig();
        const rates = this.getCurrencies();
        const target = toCurrency.toLowerCase();

        if (!value || !rates[target]) return value;

        let input = value.toString();
        const detectedSep = this.detectThousandSeparator(input);
        if (detectedSep === ',') input = input.replace(/,/g, '.');

        let num = parseFloat(input);
        if (isNaN(num)) return value;

        let converted = num * rates[target];

        // Format result
        return converted.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).replace('.', config.currency_decimal_separator || ',');
    },

    loadCurrencyConfig: async function () {
        try {
            const config = await this.getCurrencyConfig();

            const selectEl = document.querySelector('.easy-currency-switcher-select');
            if (!selectEl || !config.supportedCurrencies) return;

            config.supportedCurrencies.forEach(code => {
                const li = document.createElement('li');
                li.setAttribute('data-value', code.toLowerCase());
                li.className = 'option';
                li.textContent = code.toUpperCase();
                selectEl.appendChild(li);
            });

            document.querySelectorAll('.easy-currency-switcher-select li').forEach(option => {
                option.addEventListener('click', function () {
                    const selected = this.getAttribute('data-value');
                    rtCurrencyConverter.updateUserPreferedCurrency(selected);
                    location.reload();
                });
            });

            const userCurrency = await this.getUserPreferedCurrency();
            const toggleEl = document.querySelector('.easy-currency-switcher-toggle .currency-code');
            if (toggleEl) toggleEl.textContent = userCurrency.toUpperCase();

        } catch (error) {
            console.error('Error rendering currency config:', error);
        }
    },

    convertAllPricesCurrency: async function () {
        const userCurrency = await this.getUserPreferedCurrency();
        const symbol = await this.getCurrencySymbol(userCurrency);

        document.querySelectorAll('[rt-currency-symbol]').forEach(el => {
            el.textContent = symbol;
        });

        const elements = document.querySelectorAll('[rt-price]');
        for (let el of elements) {
            // console.log('Converting:', el.textContent);
            const original = el.textContent;
            const result = await this.convertCurrency('usd', userCurrency, original);
            el.textContent = result;
        }
    },

    renderPriceOptions: async function () {
        const userCurrency = await this.getUserPreferedCurrency();
        const symbol = await this.getCurrencySymbol(userCurrency);
        const selects = document.querySelectorAll('.price__select');

        if (!selects.length) {
            console.warn('No .option_select found in DOM when renderPriceOptions is called.');
        }

        // Untuk semua select
        for (let select of selects) {
            const pricesData = select.getAttribute('data-prices');
            if (!pricesData) continue;

            const basePrices = pricesData.split(',').map(p => parseFloat(p.trim()));
            if (!basePrices.length) continue;

            // Kosongkan isi select
            select.innerHTML = '';

            for (let usdPrice of basePrices) {
                // console.log('Converting price:', usdPrice);
                const converted = await this.convertCurrency('usd', userCurrency, usdPrice);
                const option = document.createElement('option');
                option.value = usdPrice;
                option.textContent = `${symbol} ${converted}/mo`;
                // console.log('Adding option:', option.textContent);
                select.style.display = '';
                select.appendChild(option);
            }
        }

        // ✅ Re-initialize or update the plugin after modifying the select
        $('.price__select').each(function () {
            if ($(this).next('.nice-select').length) {
                $(this).next('.nice-select').remove(); // clean up if already exists
            }
            $(this).niceSelect(); // initialize again
        });
    }
};

// Initialize
// Pastikan inisialisasi setelah DOM siap
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => rtCurrencyConverter.init());
} else {
    rtCurrencyConverter.init();
}
