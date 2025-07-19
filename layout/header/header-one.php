<?php
include './layout/partials/sanityClient.php';

// Contoh query GROQ: ambil semua post
$query = '*[_type == "layout"][0]{
                    name_layout,
                    header {
                        logo,
                        nama_header,
                        client_area {
                            client_area_en,
                            client_area_in,
                            path_client_area
                        },
                        menu_header[] {
                            title_menu {
                                title_menu_en,
                                title_menu_in
                            },
                            path_menu,
                            dropdown_type,
                            dropdown_menu[] {
                                dropdown_icon_menu,
                                dropdown_title_menu {
                                    dropdown_title_menu_en,
                                    dropdown_title_menu_in
                                },
                                dropdown_desc_menu {
                                    dropdown_desc_menu_en,
                                    dropdown_desc_menu_in
                                },
                                dropdown_path_menu
                            }
                        }
                    },
                    top_header {
                        email_top_header,
                        path_email_top_header,
                        flash_price_top_header{
                            flash_price_top_header_en,
                            flash_price_top_header_in,
                        },
                        path_live_chat_top_header,
                        path_login_top_header
                    }
                }';

$data = fetchFromSanity($query);

// Fungsi untuk mendapatkan bahasa saat ini (contoh sederhana)
function getCurrentLanguage()
{
    return isset($_GET['lang']) && $_GET['lang'] === 'id' ? 'id' : 'en';
}

$menuData = $data['result']['header']['menu_header'];
$currentLang = getCurrentLanguage();
?>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const userLang = localStorage.getItem("userPreferredLanguage");
        const currentUrl = new URL(window.location.href);

        if (userLang && currentUrl.searchParams.get("lang") !== userLang) {
            currentUrl.searchParams.set("lang", userLang);
            window.location.href = currentUrl.href; // reload dengan parameter lang
        }
    });
</script>

<header class="rts-header style-one header__default">
    <!-- HEADER TOP AREA -->
    <div class="rts-ht rts-ht__bg">
        <div class="container">
            <div class="row">
                <div class="rts-ht__wrapper">
                    <div class="rts-ht__email">
                        <a href="<?php echo htmlspecialchars($data['result']['top_header']['path_email_top_header']); ?>">
                            <img src="assets/images/icon/email.svg" alt="" class="icon">
                            <?php echo htmlspecialchars($data['result']['top_header']['email_top_header']); ?>
                            <!-- contact@hostie.com -->
                        </a>
                    </div>
                    <div class="rts-ht__promo">
                        <p>
                            <img class="icon" src="assets/images/icon/tag--group.svg" alt="">
                            <?php if ($currentLang === 'id'): ?>
                                Hosting Flash Sale: Mulai dari <strong><span>Rp</span> <span><?php echo number_format($data['result']['top_header']['flash_price_top_header']['flash_price_top_header_in']); ?></span>/bulan</strong> untuk waktu terbatas
                            <?php else: ?>
                                Hosting Flash Sale: Starting at <strong><span>$</span> <span><?php echo $data['result']['top_header']['flash_price_top_header']['flash_price_top_header_en']; ?></span>/mo</strong> for a limited time
                            <?php endif; ?>
                            <!-- Hosting Flash Sale: Starting at
                            <strong>
                                <span rt-currency-symbol>$</span> <span rt-price>2.59</span>/mo
                            </strong> for a limited time -->
                        </p>
                    </div>
                    <div class="rts-ht__links">
                        <div class="live-chat-has-dropdown">
                            <a href="<?php echo htmlspecialchars($data['result']['top_header']['path_live_chat_top_header']); ?>"" class=" live__chat">
                                <img src="assets/images/icon/forum.svg" alt="" class="icon">
                                <?php if ($currentLang === 'id'): ?>
                                    <span>Obrolan Langsung</span>
                                <?php else: ?>
                                    <span>Live Chat</span>
                                <?php endif; ?>
                                <!-- <span>Live Chat</span> -->
                            </a>
                        </div>
                        <div class="login-btn-has-dropdown">
                            <a href="#" class="login__link">
                                <img src="assets/images/icon/person.svg" alt="" class="icon">
                                <?php if ($currentLang === 'id'): ?>
                                    <span>Masuk</span>
                                <?php else: ?>
                                    <span>Login</span>
                                <?php endif; ?>
                            </a>
                            <div class="login-submenu">
                                <form action="#">
                                    <div class="form-inner">
                                        <div class="single-wrapper">
                                            <input type="email" placeholder="<?php if ($currentLang === 'id'): ?>Email<?php else: ?>Email<?php endif; ?>" required>
                                        </div>
                                        <div class="single-wrapper">
                                            <input type="password" placeholder="<?php if ($currentLang === 'id'): ?>Kata Sandi<?php else: ?>Password<?php endif; ?>" required>
                                        </div>
                                        <div class="form-btn">
                                            <button type="submit" class="primary__btn">
                                                <?php if ($currentLang === 'id'): ?>
                                                    <span>Masuk</span>
                                                <?php else: ?>
                                                    <span>Log In</span>
                                                <?php endif; ?>
                                                <!-- Log In -->
                                            </button>
                                        </div>
                                        <a href="#" class="forgot-password">
                                            <?php if ($currentLang === 'id'): ?>
                                                Lupa kata sandi?
                                            <?php else: ?>
                                                Forgot password?
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- HEADER TOP AREA END -->
    <div class="container">
        <div class="row">
            <div class="rts-header__wrapper">
                <!-- FOR LOGO -->
                <div class="rts-header__logo">
                    <a href="index.php" class="site-logo">
                        <?php if (isset($data['result']['header']['logo']['secure_url'])): ?>
                            <img class="logo-white" src="<?php echo htmlspecialchars($data['result']['header']['logo']['secure_url']); ?>" alt="<?php echo htmlspecialchars($data['result']['header']['nama_header']); ?>">
                            <img class="logo-dark" src="<?php echo htmlspecialchars($data['result']['header']['logo']['secure_url']); ?>" alt="<?php echo htmlspecialchars($data['result']['header']['nama_header']); ?>">
                        <?php else: ?>
                            <img class="logo-white" src="assets/images/logo/logo-1.svg" alt="<?php echo htmlspecialchars($data['result']['header']['nama_header']); ?>">
                            <img class="logo-dark" src="assets/images/logo/logo-4.svg" alt="<?php echo htmlspecialchars($data['result']['header']['nama_header']); ?>">
                        <?php endif; ?>
                    </a>
                </div>
                <!-- FOR NAVIGATION MENU -->
                <nav class="rts-header__menu" id="mobile-menu">
                    <div class="hostie-menu">
                        <ul class="list-unstyled hostie-desktop-menu">
                            <?php foreach ($menuData as $menuItem): ?>
                                <li class="menu-item <?php echo !empty($menuItem['dropdown_menu']) ? 'hostie-has-dropdown' : ''; ?> <?php echo $menuItem['dropdown_type'] === 'megamenu' ? 'mega-menu big' : ''; ?>">
                                    <a href="<?php echo htmlspecialchars($menuItem['path_menu']); ?>" class="hostie-dropdown-main-element">
                                        <?php
                                        // Tampilkan title berdasarkan bahasa
                                        if ($currentLang === 'id') {
                                            echo htmlspecialchars($menuItem['title_menu']['title_menu_in']);
                                        } else {
                                            echo htmlspecialchars($menuItem['title_menu']['title_menu_en']);
                                        }
                                        ?>
                                    </a>

                                    <?php if (!empty($menuItem['dropdown_menu'])): ?>
                                        <?php if ($menuItem['dropdown_type'] === 'megamenu'): ?>
                                            <!-- Mega Menu -->
                                            <div class="rts-mega-menu">
                                                <div class="wrapper">
                                                    <div class="row g-0">
                                                        <?php
                                                        $dropdownItems = $menuItem['dropdown_menu'];
                                                        $itemsPerColumn = ceil(count($dropdownItems) / 2); // Bagi menjadi 2 kolom
                                                        ?>

                                                        <div class="col-lg-6">
                                                            <ul class="mega-menu-item">
                                                                <?php for ($i = 0; $i < $itemsPerColumn && $i < count($dropdownItems); $i++): ?>
                                                                    <?php $dropdownItem = $dropdownItems[$i]; ?>
                                                                    <li>
                                                                        <a href="<?php echo htmlspecialchars($dropdownItem['dropdown_path_menu']); ?>">
                                                                            <?php if (!empty($dropdownItem['dropdown_icon_menu']['secure_url'])): ?>
                                                                                <img src="<?php echo htmlspecialchars($dropdownItem['dropdown_icon_menu']['secure_url']); ?>" alt="icon">
                                                                            <?php endif; ?>
                                                                            <div class="info" style="display: inline-block; white-space: nowrap;">
                                                                                <p style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                                                    <?php
                                                                                    if ($currentLang === 'id') {
                                                                                        echo htmlspecialchars($dropdownItem['dropdown_title_menu']['dropdown_title_menu_in']);
                                                                                    } else {
                                                                                        echo htmlspecialchars($dropdownItem['dropdown_title_menu']['dropdown_title_menu_en']);
                                                                                    }
                                                                                    ?>
                                                                                </p>
                                                                                <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                                                    <?php
                                                                                    if ($currentLang === 'id') {
                                                                                        echo htmlspecialchars($dropdownItem['dropdown_desc_menu']['dropdown_desc_menu_in']);
                                                                                    } else {
                                                                                        echo htmlspecialchars($dropdownItem['dropdown_desc_menu']['dropdown_desc_menu_en']);
                                                                                    }
                                                                                    ?>
                                                                                </span>
                                                                            </div>

                                                                        </a>
                                                                    </li>
                                                                <?php endfor; ?>
                                                            </ul>
                                                        </div>

                                                        <div class="col-lg-6">
                                                            <ul class="mega-menu-item">
                                                                <?php for ($i = $itemsPerColumn; $i < count($dropdownItems); $i++): ?>
                                                                    <?php $dropdownItem = $dropdownItems[$i]; ?>
                                                                    <li>
                                                                        <a href="<?php echo htmlspecialchars($dropdownItem['dropdown_path_menu']); ?>">
                                                                            <?php if (!empty($dropdownItem['dropdown_icon_menu']['secure_url'])): ?>
                                                                                <img src="<?php echo htmlspecialchars($dropdownItem['dropdown_icon_menu']['secure_url']); ?>" alt="icon">
                                                                            <?php endif; ?>
                                                                            <div class="info">
                                                                                <p>
                                                                                    <?php
                                                                                    if ($currentLang === 'id') {
                                                                                        echo htmlspecialchars($dropdownItem['dropdown_title_menu']['dropdown_title_menu_in']);
                                                                                    } else {
                                                                                        echo htmlspecialchars($dropdownItem['dropdown_title_menu']['dropdown_title_menu_en']);
                                                                                    }
                                                                                    ?>
                                                                                </p>
                                                                                <span>
                                                                                    <?php
                                                                                    if ($currentLang === 'id') {
                                                                                        echo htmlspecialchars($dropdownItem['dropdown_desc_menu']['dropdown_desc_menu_in']);
                                                                                    } else {
                                                                                        echo htmlspecialchars($dropdownItem['dropdown_desc_menu']['dropdown_desc_menu_en']);
                                                                                    }
                                                                                    ?>
                                                                                </span>
                                                                            </div>
                                                                        </a>
                                                                    </li>
                                                                <?php endfor; ?>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <!-- Regular Dropdown -->
                                            <ul class="hostie-submenu list-unstyled menu-pages">
                                                <?php foreach ($menuItem['dropdown_menu'] as $dropdownItem): ?>
                                                    <li class="nav-item">
                                                        <a class="nav-link" href="<?php echo htmlspecialchars($dropdownItem['dropdown_path_menu']); ?>">
                                                            <?php
                                                            if ($currentLang === 'id') {
                                                                echo htmlspecialchars($dropdownItem['dropdown_title_menu']['dropdown_title_menu_in']);
                                                            } else {
                                                                echo htmlspecialchars($dropdownItem['dropdown_title_menu']['dropdown_title_menu_en']);
                                                            }
                                                            ?>
                                                        </a>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </nav>
                <!-- FOR HEADER RIGHT -->
                <div class="rts-header__right">
                    <!-- <div class="easy-currency-switcher switcher-list-content">
                        <form action="#" id="easy_currency_switcher_form" class="easy_currency_switcher_form">
                            <button type="button" class="easy-currency-switcher-toggle">
                                <span class="currency-code">usd</span>
                                <span class="dropdown-icon-currency"></span>
                            </button>
                            <ul class="easy-currency-switcher-select list has-flag">
                            </ul>
                        </form>
                    </div> -->
                    <div class="easy-language-switcher switcher-list-content">
                        <form action="#" id="easy_language_switcher_form" class="easy_language_switcher_form">
                            <button type="button" class="easy-language-switcher-toggle">
                                <span class="language-code">en</span>
                                <span class="dropdown-icon-language"></span>
                            </button>
                            <ul class="easy-language-switcher-select list has-flag">
                                <!-- Opsi bahasa akan di-render otomatis oleh JS -->
                            </ul>
                        </form>
                    </div>
                    <a href="<?php echo htmlspecialchars($data['result']['header']['client_area']['path_client_area']); ?>" class="login__btn" target="_blank">
                        <?php
                        if ($currentLang === 'id') {
                            echo htmlspecialchars($data['result']['header']['client_area']['client_area_in']);
                        } else {
                            echo htmlspecialchars($data['result']['header']['client_area']['client_area_en']);
                        }
                        ?>
                    </a>
                    <button id="menu-btn" aria-label="Menu" class="mobile__active menu-btn"><i class="fa-sharp fa-solid fa-bars"></i></button>
                </div>
            </div>
        </div>
    </div>
</header>