<?php
$publicBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');

$assetUrl = static function (string $path) use ($publicBase): string {
    return $publicBase . '/assets/admin/' . ltrim($path, '/');
};

$toUrl = static function (string $path) use ($publicBase): string {
    return $publicBase . '/' . ltrim($path, '/');
};

$adminName = htmlspecialchars($_SESSION['name'] ?? 'Admin', ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <title><?php echo htmlspecialchars($title ?? 'Admin', ENT_QUOTES, 'UTF-8'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />
    <link rel="icon" type="image/png" href="<?php echo $assetUrl('images/icon/rmbgwhite.png'); ?>" />
    <link rel="stylesheet" href="<?php echo $assetUrl('css/bootstrap.min.css'); ?>" />
    <link rel="stylesheet" href="<?php echo $assetUrl('css/fontawesome.min.css'); ?>" />
    <link rel="stylesheet" href="<?php echo $assetUrl('css/themify-icons.css'); ?>" />
    <link rel="stylesheet" href="<?php echo $assetUrl('css/metismenujs.min.css'); ?>" />
    <link rel="stylesheet" href="<?php echo $assetUrl('css/swiper-bundle.min.css'); ?>" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simple-datatables@10/dist/style.min.css" />
    <link rel="stylesheet" href="<?php echo $assetUrl('css/typography.css'); ?>" />
    <link rel="stylesheet" href="<?php echo $assetUrl('css/default-css.css'); ?>" />
    <link rel="stylesheet" href="<?php echo $assetUrl('css/styles.css'); ?>" />
    <link rel="stylesheet" href="<?php echo $assetUrl('css/responsive.css'); ?>" />
</head>

<body>
    <div id="preloader">
        <div class="loader"></div>
    </div>

    <div class="page-container">

        <!-- sidebar -->
        <div class="sidebar-menu">
            <div class="sidebar-header">
                <div class="logo">
                    <a href="<?php echo $toUrl('admin/users'); ?>">
                        <img src="<?php echo $assetUrl('images/icon/rmbgwhite.png'); ?>" alt="logo" />
                    </a>
                </div>
            </div>
            <div class="main-menu">
                <div class="menu-inner">
                    <nav>
                        <ul class="metismenu" id="menu">
                            <li><a href="<?php echo $toUrl('admin/users'); ?>"><i class="ti-user"></i><span>Quản lý tài
                                        khoản</span></a></li>
                            <li><a href="<?php echo $toUrl('admin/products'); ?>"><i class="ti-package"></i><span>Quản
                                        lý sản phẩm</span></a></li>
                            <li><a href="<?php echo $toUrl('admin/orders'); ?>"><i
                                        class="ti-shopping-cart"></i><span>Quản lý đơn hàng</span></a></li>
                            <li>
                                <a href="javascript:void(0)" aria-expanded="true"><i class="ti-agenda"></i><span>Quản lý
                                        tin tức</span></a>
                                <ul class="collapse">
                                    <li><a href="<?php echo $toUrl('admin/categories'); ?>">Danh mục</a></li>
                                    <li><a href="<?php echo $toUrl('admin/posts'); ?>">Bài viết</a></li>
                                </ul>
                            </li>

                            <li><a href="<?php echo $toUrl('admin/comments'); ?>"><i class="ti-comments"></i><span>Quản
                                        lý bình luận</span></a></li>
                            <li><a href="<?php echo $toUrl('admin/contacts'); ?>"><i class="ti-email"></i><span>Quản lý
                                        liên hệ</span></a></li>
                            <li><a href="<?php echo $toUrl('admin/qa'); ?>"><i class="ti-help-alt"></i><span>Quản lý hỏi
                                        đáp</span></a></li>
                            <li>
                                <a href="javascript:void(0)" aria-expanded="false">
                                    <i class="ti-write"></i><span>Quản lý nội dung</span>
                                </a>
                                <ul class="collapse">
                                    <li><a href="<?php echo $toUrl('admin/homepage'); ?>">Trang chủ</a></li>
                                    <li><a href="<?php echo $toUrl('admin/aboutpage'); ?>">Trang giới thiệu</a></li>
                                    <li><a href="<?php echo $toUrl('admin/contactpage'); ?>">Trang liên hệ</a></li>
                                    <li><a href="<?php echo $toUrl('admin/faqpage'); ?>">Trang hỏi đáp</a></li>
                                </ul>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

        <!-- main content -->
        <div class="main-content">

            <!-- header -->
            <div class="header-area">
                <div class="row align-items-center">
                    <div class="col-md-6 col-sm-8 clearfix">
                        <div class="nav-btn float-start"><span></span><span></span><span></span></div>
                        <div class="search-box float-start">
                            <form action="#"><input type="text" name="search" placeholder="Search..." /><i
                                    class="ti-search"></i></form>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-4 clearfix">
                        <ul class="notification-area float-end">
                            <li id="full-view"><i class="ti-fullscreen"></i></li>
                            <li id="full-view-exit"><i class="ti-zoom-out"></i></li>
                            <li class="settings-btn"><i class="ti-settings"></i></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- page title -->
            <div class="page-title-area">
                <div class="row align-items-center">
                    <div class="col-sm-6">
                        <div class="breadcrumbs-area clearfix">
                            <h1 class="page-title float-start">
                                <?php echo htmlspecialchars($title ?? '', ENT_QUOTES, 'UTF-8'); ?>
                            </h1>
                            <ul class="breadcrumbs float-start">
                                <li><a href="<?php echo $toUrl('admin/users'); ?>">Home</a></li>
                                <li><span><?php echo htmlspecialchars($title ?? '', ENT_QUOTES, 'UTF-8'); ?></span></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-6 clearfix">
                        <div class="user-profile float-end">
                            <h4 class="user-name dropdown-toggle" data-bs-toggle="dropdown">
                                <span><?php echo $adminName; ?></span>
                                <i class="fa-solid fa-angle-down"></i>
                            </h4>
                            <div class="dropdown-menu user-dropdown">
                                <a class="dropdown-item" href="<?php echo $toUrl('admin/profile'); ?>">
                                    <i class="fa-solid fa-user"></i> My Profile
                                </a>
                                <div class="dropdown-divider"></div>
                                <form action="<?php echo $toUrl('logout'); ?>" method="post" style="margin:0">
                                    <button type="submit" class="dropdown-item"
                                        style="background:none;border:none;width:100%;text-align:left;cursor:pointer;">
                                        <i class="fa-solid fa-right-from-bracket"></i> Log Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- nội dung trang con được nhúng vào đây -->
            <div class="main-content-inner" id="main-content">
                <?php echo $content; ?>
            </div>

        </div>

        <footer>
            <div class="footer-area">
                <p>© Copyright 2026. All right reserved.</p>
            </div>
        </footer>

    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="<?php echo $assetUrl('js/swiper-bundle.min.js'); ?>"></script>
    <script src="<?php echo $assetUrl('js/metismenujs.min.js'); ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@10/dist/umd/simple-datatables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.1/dist/chart.umd.min.js"></script>
    <script src="<?php echo $assetUrl('js/line-chart.js'); ?>"></script>
    <script src="<?php echo $assetUrl('js/pie-chart.js'); ?>"></script>
    <script src="<?php echo $assetUrl('js/scripts.js'); ?>"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Bootstrap 4 -> 5 compatibility for older admin partials.
        document.querySelectorAll('[data-toggle]').forEach(function(element) {
            if (!element.hasAttribute('data-bs-toggle')) {
                element.setAttribute('data-bs-toggle', element.getAttribute('data-toggle'));
            }
        });

        document.querySelectorAll('[data-target]').forEach(function(element) {
            if (!element.hasAttribute('data-bs-target')) {
                element.setAttribute('data-bs-target', element.getAttribute('data-target'));
            }
        });

        document.querySelectorAll('[data-dismiss]').forEach(function(element) {
            if (!element.hasAttribute('data-bs-dismiss')) {
                element.setAttribute('data-bs-dismiss', element.getAttribute('data-dismiss'));
            }
        });

        if (window.jQuery && !window.jQuery.fn.modal) {
            window.jQuery.fn.modal = function(action) {
                return this.each(function() {
                    var instance = bootstrap.Modal.getOrCreateInstance(this);

                    if (action === 'show') {
                        instance.show();
                    } else if (action === 'hide') {
                        instance.hide();
                    } else if (action === 'toggle') {
                        instance.toggle();
                    }
                });
            };
        }
    });

    document.addEventListener('DOMContentLoaded', function() {

        // Init DataTables
        ['dataTable', 'dataTable2', 'dataTable3'].forEach(function(id) {
            const element = document.getElementById(id);

            if (element) {
                new simpleDatatables.DataTable(element, {
                    perPage: 10
                });
            }
        });

        // Highlight menu item active
        const currentPath = window.location.pathname;

        document.querySelectorAll('#menu a').forEach(function(link) {

            const href = link.getAttribute('href');

            if (
                href &&
                currentPath.includes(href) &&
                href !== '<?php echo $publicBase; ?>/'
            ) {

                link.classList.add('active');

                const parentLi = link.closest('li');
                if (parentLi) {
                    parentLi.classList.add('active');
                }

                const parentCollapse = link.closest('ul.collapse');

                if (parentCollapse) {

                    parentCollapse.classList.add('show');

                    const grandParentLi = parentCollapse.closest('li');

                    if (grandParentLi) {
                        grandParentLi.classList.add('active');
                    }
                }
            }
        });
    });

    // Preloader
    $(window).on('load', function() {
        $('#preloader').fadeOut('slow', function() {
            $(this).remove();
        });
    });
    </script>
</body>

</html>
