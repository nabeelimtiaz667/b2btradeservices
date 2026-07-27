<div id="sticky-bar" style="display: none;">
    <div class="container">
        <div class="row ">
            <div class="main-heading-head ">
                <p class="text-white m-0"><b><a class="exl-btn "  href="<?= base_url('premium-services') ?>">Exclusive Services</a></b></p>
                  <a href="<?= base_url('/') ?>"><img src="<?= base_url('assets/images/logo.svg') ?>"></a>
            </div>
            <div class="sticky-search-bar">
                <form action="<?= base_url('search') ?>" method="get" class="search-form">
                <div class="searchbar-box">
                    <div class="searchbar-input">
                        <select name="type" class="mobile-filter-select">
                         
                            <option value="suppliers" selected>Suppliers</option>
                            <option value="buyers">Buyers</option>
                            <option value="products">Products</option>
                        </select>
                        <div class="border-center"></div>
                        <img src="<?= base_url('assets/images/search.svg') ?>">
                        <input type="search" name="q" placeholder="What are you looking for?">
                        <button type="submit" class="outline-btn search-btn btn">Search</button>
                    </div>
                </div>
                <div class="radio-search-btn">
                    <div class="filter-group">
                        
                        <label><input type="radio" name="type" value="suppliers" checked="checked"><span class="radio"></span> Suppliers</label>
                        <label><input type="radio" name="type" value="buyers"><span class="radio"></span> Buyers</label>
                        <label><input type="radio" name="type" value="products"><span class="radio"></span> Products</label>
                    </div>
                </div>
                </form>
            </div>
            <div class="sticky-bar-icon">
                  
                <div class="header-icon-sec">
                    <div class="d-flex gap-2 justify-content-end">
                        <?php if (session()->get('logged_in')): ?>
                            <div class="sign-btn gap-1 d-flex align-items-center">
                                <a href="<?= base_url('dashboard') ?>" class="text-white text-decoration-none"><i class="fas fa-user-circle me-1"></i> Hi, <?= esc(session()->get('name') ?? 'User') ?></a>
                            </div>
                            <span class="text-white ">|</span>
                            <div class="sign-btn gap-1 d-flex align-items-center">
                                <a href="<?= base_url('dashboard') ?>" class="text-white text-decoration-none"><i class="fas fa-tachometer-alt me-1"></i> Dashboard</a>
                            </div>
                            <span class="text-white ">|</span>
                            <div class="sign-btn gap-1 text-white d-flex align-items-center">
                                <a href="<?= base_url('logout') ?>" class="text-white text-decoration-none"><i class="fas fa-sign-out-alt me-1"></i> Logout</a>
                            </div>
                        <?php else: ?>
                            <div class="sign-btn gap-2 d-flex align-items-center">
                                <a href="<?= base_url('login') ?>" class="text-white"><img src="<?= base_url('assets/images/sign-in-icon.svg') ?>"> Sign in</a>
                            </div>
                            <span class="text-white">|</span>
                            <div class="sign-btn gap-2 text-white d-flex align-items-center">
                                <a href="<?= base_url('register') ?>" class="text-white"><img src="<?= base_url('assets/images/sign-up-icon.svg') ?>"> Sign Up</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="mobile_menu_custom" style="display: none;">
                <div class="burger-menu" onclick="toggleNavigation()">
                    <img src="<?= base_url('assets/images/hamberger-menu.svg') ?>" width="25px" decoding="sync" fetchpriority="high" alt="button">
                </div>
                <nav class="navigation">
                    <a class="close-btn" onclick="toggleNavigation()">X</a>
                    <ul class="navbar-nav">
                         <li class="nav-item"><a class="nav-link text-white exl-btn " href="<?= base_url('premium-services') ?>">Exclusive Services</a></li>
                        <li class="nav-item"><a class="nav-link active text-white" aria-current="page" href="<?= base_url('/') ?>">Home</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="<?= base_url('supplier') ?>">Suppliers</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="<?= base_url('buyers') ?>">Buyers</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="<?= base_url('product') ?>">Products Search</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="<?= base_url('buyer/post-rfq') ?>">Post Buy Offer</a></li>
                    </ul>
                    <div class="d-flex gap-3 mt-3">
                        <?php if (session()->get('logged_in')): ?>
                            <div class="sign-btn gap-2 d-flex align-items-center">
                                <a href="<?= base_url('dashboard') ?>" class="text-white text-decoration-none"><i class="fas fa-user-circle me-1"></i> Hi, <?= esc(session()->get('name') ?? 'User') ?></a>
                            </div>
                            <span class="text-white">|</span>
                            <div class="sign-btn gap-1 d-flex align-items-center">
                                <a href="<?= base_url('dashboard') ?>" class="text-white text-decoration-none"><i class="fas fa-tachometer-alt me-1"></i> Dashboard</a>
                            </div>
                            <span class="text-white ">|</span>
                            <div class="sign-btn gap-1 text-white d-flex align-items-center">
                                <a href="<?= base_url('logout') ?>" class="text-white text-decoration-none"><i class="fas fa-sign-out-alt me-1"></i> Logout</a>
                            </div>
                        <?php else: ?>
                            <div class="sign-btn gap-1 d-flex align-items-center">
                                <a href="<?= base_url('login') ?>" class="text-white"><img src="<?= base_url('assets/images/sign-in-icon.svg') ?>"> Sign in</a>
                            </div>
                            <span class="text-white">|</span>
                            <div class="sign-btn gap-2 text-white d-flex align-items-center">
                                <a href="<?= base_url('register') ?>" class="text-white"><img src="<?= base_url('assets/images/sign-up-icon.svg') ?>"> Sign Up</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </nav>
            </div>
        </div>
    </div>
</div>

<section class="pt-3 pb-3 topbar">
    <div class="container">
        <div class="d-flex align-items-center">
            <div class="main-heading-head">
                <p class="text-white m-0"><b><a class="exl-btn "  href="<?= base_url('premium-services') ?>">Exclusive Services</a></b></p>
                  <a href="<?= base_url('/') ?>"><img src="<?= base_url('assets/images/logo.svg') ?>"></a>
            </div>
            <div class="links">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link active text-white" aria-current="page" href="<?= base_url('supplier-category') ?>">Categories</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="<?= base_url('buyers') ?>">Buyers</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="<?= base_url('supplier') ?>">Suppliers</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="<?= base_url('product') ?>">Products Search</a></li> 
                    <li class="nav-item"><a class="nav-link text-white"  href="<?= base_url('buyer/post-rfq') ?>">Post Buy Offer</a></li>
                </ul>
            </div>
            <div class="header-icon-sec">
                <div class="d-flex gap-1 justify-content-end">
                    <?php if (session()->get('logged_in')): ?>
                        <div class="sign-btn gap-2 d-flex align-items-center">
                            <a href="<?= base_url('dashboard') ?>" class="text-white text-decoration-none"><i class="fas fa-user-circle me-1"></i> Hi, <?= esc(session()->get('name') ?? 'User') ?></a>
                        </div>
                        <span class="text-white ">|</span>
                        <div class="sign-btn gap-2 d-flex align-items-center">
                            <a href="<?= base_url('dashboard') ?>" class="text-white text-decoration-none"><i class="fas fa-tachometer-alt me-1"></i> Dashboard</a>
                        </div>
                        <span class="text-white">|</span>
                        <div class="sign-btn gap-2 text-white d-flex align-items-center">
                            <a href="<?= base_url('logout') ?>" class="text-white text-decoration-none"><i class="fas fa-sign-out-alt me-1"></i> Logout</a>
                        </div>
                    <?php else: ?>
                        <div class="sign-btn gap-2 d-flex align-items-center">
                            <a href="<?= base_url('login') ?>" class="text-white"><img src="<?= base_url('assets/images/sign-in-icon.svg') ?>"> Sign in</a>
                        </div>
                        <span class="text-white">|</span>
                        <div class="sign-btn gap-2 text-white d-flex align-items-center">
                            <a href="<?= base_url('register') ?>" class="text-white"><img src="<?= base_url('assets/images/sign-up-icon.svg') ?>"> Sign Up</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
               <p class="text-white m-0 d-lg-none"><b><a class="exl-btn "  href="<?= base_url('premium-services') ?>">Exclusive Services</a></b></p>
        </div>
    </div>
</section>

<section class="main-header mt-3">
    <div class="container">
        <div class="row align-items-center">
            <div class="logo">
                <a href="<?= base_url('/') ?>"><img src="<?= base_url('assets/images/logo.svg') ?>"></a>
            </div>
            <div class="inner-header-links">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="<?= base_url('about-us') ?>">About us</a></li>
                     
                    <li class="nav-item"><a class="nav-link" href="<?= base_url('become-our-agent-partner') ?>">Become Our Partner</a></li>
                   
                    <li class="nav-item"><a class="nav-link" href="#">Blog</a></li>
                </ul>
            </div>
        </div>
    </div>
</section>
