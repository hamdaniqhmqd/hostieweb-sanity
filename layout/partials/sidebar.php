<?php
include '../partials/sanityClient.php';

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
function getCurrentLanguageSidebar()
{
    return isset($_GET['lang']) && $_GET['lang'] === 'id' ? 'id' : 'en';
}

$menuData = $data['result']['header']['menu_header'];
$currentLang = getCurrentLanguageSidebar();
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

<div id="side-bar" class="side-bar header-two">
    <button class="close-icon-menu" aria-label="Close Menu"><i class="fa-sharp fa-thin fa-xmark"></i></button>
    <!-- mobile menu area start -->
    <div class="mobile-menu-main">
        <nav class="nav-main mainmenu-nav mt--30">
            <ul class="mainmenu metismenu" id="mobile-menu-active">
                <?php foreach ($menuData as $menuItem): ?>
                    <li class="<?php echo !empty($menuItem['dropdown_menu']) ? 'has-droupdown' : ''; ?>">
                        <?php if (!empty($menuItem['dropdown_menu'])): ?>
                            <a href="#" class="main">
                                <?php
                                if ($currentLang === 'id') {
                                    echo htmlspecialchars($menuItem['title_menu']['title_menu_in'] ?? '');
                                } else {
                                    echo htmlspecialchars($menuItem['title_menu']['title_menu_en'] ?? '');
                                }
                                ?>
                            </a>

                            <ul class="submenu mm-collapse">
                                <?php foreach ($menuItem['dropdown_menu'] as $dropdownItem): ?>
                                    <li>
                                        <a class="mobile-menu-link" href="<?php echo htmlspecialchars($dropdownItem['dropdown_path_menu'] ?? '#'); ?>">
                                            <?php
                                            // Tampilkan dropdown title berdasarkan bahasa
                                            if ($currentLang === 'id') {
                                                echo htmlspecialchars($dropdownItem['dropdown_title_menu']['dropdown_title_menu_in'] ?? '');
                                            } else {
                                                echo htmlspecialchars($dropdownItem['dropdown_title_menu']['dropdown_title_menu_en'] ?? '');
                                            }
                                            ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <a href="<?php echo htmlspecialchars($menuItem['path_menu'] ?? '#'); ?>" class="main">
                                <?php
                                if ($currentLang === 'id') {
                                    echo htmlspecialchars($menuItem['title_menu']['title_menu_in'] ?? '');
                                } else {
                                    echo htmlspecialchars($menuItem['title_menu']['title_menu_en'] ?? '');
                                }
                                ?>
                            </a>

                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>

                <li class="has-droupdown">
                    <a href="#" class="main">Pages</a>
                    <ul class="submenu mm-collapse">
                        <li><a class="mobile-menu-link" href="about.php">About</a></li>
                        <li><a class="mobile-menu-link" href="team.php">Affiliate</a></li>
                        <li><a class="mobile-menu-link" href="faq.php">Pricing</a></li>
                        <li><a class="mobile-menu-link" href="book-a-demo.php">Sign Up</a></li>
                        <li><a class="mobile-menu-link" href="free-audit.php">Whois</a></li>
                        <li><a class="mobile-menu-link" href="pricing.php">Partner</a></li>
                        <li><a class="mobile-menu-link" href="blog.php">Blog</a></li>
                        <li><a class="mobile-menu-link" href="blog-list.php">Blog List</a></li>
                        <li><a class="mobile-menu-link" href="blog-grid-2.php">Blog Grid</a></li>
                        <li><a class="mobile-menu-link" href="support.php">Support</a></li>
                        <li><a class="mobile-menu-link" href="pricing.php">Pricing</a></li>
                        <li><a class="mobile-menu-link" href="pricing-two.php">Pricing Package</a></li>
                        <li><a class="mobile-menu-link" href="pricing-three.php">Pricing Comparison</a></li>
                        <li><a class="mobile-menu-link" href="signin.php">Sign In</a></li>
                        <li><a class="mobile-menu-link" href="business-mail.php">Business Mail</a></li>
                        <li><a class="mobile-menu-link" href="knowledgebase.php">Knowledgebase</a></li>
                        <li><a class="mobile-menu-link" href="blog-details.php">Blog Details</a></li>
                        <li><a class="mobile-menu-link" href="domain-checker.php">Domain Checker</a></li>
                        <li><a class="mobile-menu-link" href="ssl-certificate.php">SSL Certificates</a></li>
                        <li><a class="mobile-menu-link" href="data-center.php">Data Centers</a></li>
                        <li><a class="mobile-menu-link" href="technology.php">Technology</a></li>
                        <li><a class="mobile-menu-link" href="contact.php">Contact</a></li>
                        <li><a class="mobile-menu-link" href="domain-transfer.php">Domain Transfer</a></li>
                        <li><a class="mobile-menu-link" href="payment-method.php">Payment Method</a></li>
                    </ul>
                </li>
            </ul>
        </nav>

        <ul class="social-area-one pl--20 mt--100">
            <li><a href="https://www.linkedin.com" aria-label="social-link" target="_blank"><i class="fa-brands fa-linkedin"></i></a></li>
            <li><a href="https://www.x.com" aria-label="social-link" target="_blank"><i class="fa-brands fa-twitter"></i></a></li>
            <li><a href="https://www.youtube.com" aria-label="social-link" target="_blank"><i class="fa-brands fa-youtube"></i></a></li>
            <li><a href="https://www.facebook.com" aria-label="social-link" target="_blank"><i class="fa-brands fa-facebook-f"></i></a></li>
        </ul>
    </div>
    <!-- mobile menu area end -->
</div>