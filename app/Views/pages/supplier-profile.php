<?= $this->extend('layouts/supplier-profile') ?>

<?= $this->section('content') ?>
<section class="main-header mt-3 mb-3">
    <div class="container">
        <div class="row align-items-center">
            <div class="logo">
                <a href="<?= base_url('/') ?>"><img src="<?= base_url('assets/images/logo.svg') ?>" alt="Logo"></a>
            </div>
            <div class="inner-header-links">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="https://k1g.337.mytemp.website/about-us">About us</a></li>
                     
                    <li class="nav-item"><a class="nav-link" href="https://k1g.337.mytemp.website/become-our-agent-partner">Become Our Partner</a></li>
                   
                    <li class="nav-item"><a class="nav-link" href="#">Blog</a></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="supplier-profile-banner">
    <div class="supplier-profile-slider-sec">
        <?php
            $banners = [];
            if (!empty($supplier['banner_image'])) $banners[] = base_url('uploads/suppliers/' . $supplier['banner_image']);
            if (!empty($supplier['banner_image_2'])) $banners[] = base_url('uploads/suppliers/' . $supplier['banner_image_2']);
            if (!empty($supplier['banner_image_3'])) $banners[] = base_url('uploads/suppliers/' . $supplier['banner_image_3']);

            if (empty($banners)) {
                $banners = [
                    base_url('assets/images/defaut-banner-supplier-profile.webp'),
                ];
            }
        ?>
        <?php foreach ($banners as $bannerUrl): ?>
        <div>
            <div class="supplier-profile-slider">
                <img src="<?= $bannerUrl ?>" alt="Banner">
                <h1><?= esc($supplier['company_name'] ?? $supplier['name'] ?? 'Company Name') ?></h1>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="profile-bar">
    <div class="container">
        <div class="row" style=" align-items: center;">
            <div class="proflie-img">
                <?php if (!empty($supplier['company_logo'])): ?>
                    <img src="<?= base_url('uploads/suppliers/' . $supplier['company_logo']) ?>" class="w-100" style="object-fit: contain;" alt="Company Logo">
                <?php else: ?>
                    <img src="<?= base_url('assets/images/profile-img.svg') ?>" class="w-100" alt="Profile Default">
                <?php endif; ?>
            </div>
            <div class="proflie-tabs">
                <ul class="nav">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Home</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="javascript:void(0)" role="button" data-bs-toggle="dropdown" aria-expanded="false">Products</a>
                        <ul class="dropdown-menu">
                            <?php if (isset($products) && count($products) > 0): ?>
                                <?php foreach (array_slice($products, 0, 5) as $p): ?>
                                    <li><a class="dropdown-item" href="<?= base_url('product/detail/' . $p['id']) ?>"><?= esc($p['name']) ?></a></li>
                                <?php endforeach; ?>
                                <?php if (count($products) > 5): ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?= base_url('product/supplier/' . $supplier['id']) ?>">View All Products</a></li>
                                <?php endif; ?>
                            <?php else: ?>
                                <li><a class="dropdown-item" href="#">No products</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                </ul>
            </div>
            <div class="profile-search-bar">
                <div class="profile-search-bar-div">
                    <select>
                        <option>In Company</option>
                    </select>
                    <input type="search" id="productSearchInput" placeholder="Search Products">
                    <button type="button" id="productSearchBtn">Find Product</button>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="profile-info mt-4" id="about">
    <div class="container">
        <div class="row">
            <div class="profile-info-right-col">
                <div class="d-flex justify-content-between">
                    <div>
                        <h2 class="d-flex align-items-center gap-2 text-white">
                            <?= esc($supplier['company_name'] ?? $supplier['name'] ?? 'Company Name') ?>
                            <?php if (isset($supplier['country']['name'])): ?>
                                <img src="<?= base_url('assets/images/flags/flag_' . str_replace(' ', '_', $supplier['country']['name']) . '.svg') ?>" width="35" onerror="this.style.display='none'">
                            <?php endif; ?>
                        </h2>
                        <p class="text-white mb-1"><b>Contact Person:</b> <?= esc($supplier['name'] ?? 'N/A') ?></p>
                        <p class="text-white">Category: <span><b><?= esc($supplier['selling_products'] ?? 'General') ?></b></span></p>
                    </div>
                    <div class="sp-membership-icon">
                        <?php if (isset($supplier['membership_level']) && $supplier['membership_level'] == 'free'): ?>
                            <img src="<?= base_url('assets/images/free-membership-coin.webp') ?>" width="100">
                        <?php elseif (isset($supplier['membership_level']) && $supplier['membership_level'] == 'starter'): ?>
                            <img src="<?= base_url('assets/images/starter-coin.webp') ?>" width="100">
                        <?php elseif (isset($supplier['membership_level']) && $supplier['membership_level'] == 'gold'): ?>
                            <img src="<?= base_url('assets/images/gold-coin.webp') ?>" width="100">
                        <?php elseif (isset($supplier['membership_level']) && $supplier['membership_level'] == 'platinum'): ?>
                            <img src="<?= base_url('assets/images/palti-coin.webp') ?>" width="100">
                        <?php elseif (isset($supplier['membership_level']) && $supplier['membership_level'] == 'vip'): ?>
                            <img src="<?= base_url('assets/images/vip-coin.webp') ?>" width="100">
                        <?php endif; ?>
                    </div>
                </div>
                <p class="text-white mb-0"><?= nl2br(esc($supplier['selling_products'] ?? 'No description available. Contact supplier for more information.')) ?></p>
            </div>
            <div class="profile-info-left-col">
                <div class="profile-contact-info">
                    <h2>Company Information</h2>
                    <div class="column-count-2 mt-3">
                        <p><b>Business Type:</b><br>Supplier</p>
                        <p><b>Main Products:</b><br><?= esc($supplier['selling_products'] ?? 'N/A') ?></p>
                        <p><b>City / State:</b><br><?= esc($supplier['city'] ?? 'N/A') ?></p>
                        <p><b>Country/Region:</b><br><?= isset($supplier['country']['name']) ? esc($supplier['country']['name']) : 'N/A' ?></p>
                        <p><b>Website:</b><br><a href="<?= esc($supplier['website'] ?? '#') ?>" target="_blank"><?= esc($supplier['website'] ?? 'N/A') ?></a></p>
                        <p><b>Membership:</b><br><span style="text-transform: uppercase;font-weight: bold;" class="bg-light" class="bg-<?= ($supplier['membership_level'] ?? 'Free') == 'VIP' ? 'danger' : (($supplier['membership_level'] ?? 'Free') == 'Platinum' ? 'secondary' : (($supplier['membership_level'] ?? 'Free') == 'Gold' ? 'warning' : (($supplier['membership_level'] ?? 'Free') == 'Silver' ? 'info' : 'light'))) ?>"><?= esc($supplier['membership_level'] ?? 'Free') ?></span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($supplier['company_introduction'])): ?>
<section class="mt-5">
    <div class="container">
        <h2>Company Introduction</h2>
        <div class="mt-3" style="line-height: 1.8; color: #555;">
            <?= nl2br(esc($supplier['company_introduction'])) ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="profile-product-category mt-5" id="products">
    <div class="container">
        <h2>Show Case</h2>
        <?php
            $allProductsWithImages = [];
            if (isset($products) && count($products) > 0) {
                $allProductsWithImages = array_filter($products, function($p) {
                    return !empty($p['main_image']);
                });
                $allProductsWithImages = array_values($allProductsWithImages);
            }
        ?>
        <?php if (!empty($allProductsWithImages)): ?>
            <div class="row mt-3" id="productShowcaseRow">
                <?php foreach ($allProductsWithImages as $product): ?>
                    <div class="col-6 col-md-4 col-lg-3 mb-3 product-item">
                        <a href="<?= base_url('product/detail/' . $product['id']) ?>" class="d-block">
                            <img src="<?= base_url('uploads/products/' . $product['main_image']) ?>" class="move-on-hover w-100" style="height: 200px; object-fit: cover; border-radius: 8px;">
                        </a>
                        <p class="mt-2 mb-0 text-center fw-bold product-name" style="font-size: 14px;"><?= esc($product['name']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div id="noSearchMatch" class="text-center mt-4" style="display: none;">
                <p>No products match your search.</p>
            </div>

            <a href="<?= base_url('product/supplier/' . $supplier['id']) ?>" class="view-all-link d-flex align-items-center gap-2 mt-3">View All Products <img src="<?= base_url('assets/images/arrow-right.svg') ?>"></a>
        <?php else: ?>
            <div class="text-center mt-4">
                <p>No product images uploaded yet.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php if (isset($interestKeywords) && count($interestKeywords) > 0): ?>
<section class="mt-5 pt-md-4">
    <div class="container">
        <h2 class="text-center f-18 light-green-h2-color">You may be interested In</h2>
        <div class="bullet-interest mt-3">
            <?php
                $colorClasses = ['bullet-interest-grey', 'bullet-interest-dark-grey', 'bullet-interest-green'];
            ?>
            <?php foreach ($interestKeywords as $idx => $keyword): ?>
                <a href="<?= base_url('supplier/search?q=' . urlencode(str_replace([' Buyers', ' Suppliers'], '', $keyword))) ?>" class="move-on-hover <?= $colorClasses[$idx % 3] ?>"><?= esc($keyword) ?></a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="mt-5 mb-5 pb-md-5 pt-md-4" id="contact">
    <div class="container">
        <div class="bg-profile-contact">
            <div class="row">
                <div class="profile-contact-info-bottom mt-md-4">
                    <h2 class="f-18">Contact Details:</h2>
                    <div class="mt-3">
                        <p class="mb-md-4"><b>Address:</b><br>
                            <a href="#"><?= esc($supplier['city'] ?? 'N/A') ?><?= isset($supplier['country']['name']) ? ', ' . esc($supplier['country']['name']) : '' ?></a></p>
                        <p class="mb-md-4"><b>Website:</b><br>
                            <a href="<?= esc($supplier['website'] ?? '#') ?>" target="_blank"><?= esc($supplier['website'] ?? 'N/A') ?></a></p>
                        <p class="mb-md-4"><b>Phone:</b><br>
                            <?php if (session()->get('user_type') === 'admin'): ?>
                                <a href="tel:<?= esc($supplier['phone'] ?? '') ?>"><?= esc($supplier['phone'] ?? 'N/A') ?></a>
                            <?php else: ?>
                                <span style="filter: blur(5px); user-select:none;">+1 234 567 890</span>
                            <?php endif; ?>
                        </p>
                        <p><b>Email:</b><br>
                            <?php if (session()->get('user_type') === 'admin'): ?>
                                <a href="mailto:<?= esc($supplier['email'] ?? '') ?>"><?= esc($supplier['email'] ?? 'N/A') ?></a>
                            <?php else: ?>
                                <span style="filter: blur(5px); user-select:none;">hidden@email.com</span>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
                <div class="supplier-contact-details-form">
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                    <?php endif; ?>
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                    <?php endif; ?>
                    <form action="<?= base_url('contact/submit') ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="form_type" value="supplier_inquiry">
                        <input type="hidden" name="source_page" value="supplier-profile">
                        <input type="hidden" name="source_id" value="<?= $supplier['id'] ?? '' ?>">
                        <input type="hidden" name="lead_type" value="buyer">
                        <div class="form-input">
                            <input type="text" name="name" placeholder="Name*" required>
                        </div>
                        <div class="form-input mb-10">
                            <input type="tel" name="phone" class="phone" placeholder="Phone*" required>
                        </div>
                        <div class="form-input">
                            <input type="email" name="email" placeholder="Email*" required>
                        </div>
                        <div class="form-textarea">
                            <textarea name="message" placeholder="What are you looking for?"></textarea>
                        </div>
                        <div class="submit-btn">
                            <button type="submit">Submit Now</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('productSearchInput');
    const searchBtn = document.getElementById('productSearchBtn');
    const productItems = document.querySelectorAll('.product-item');
    const noSearchMatchMsg = document.getElementById('noSearchMatch');
    const productsSection = document.getElementById('products');

    function filterAndScrollProducts() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        let matchCount = 0;

        // Filter the products
        if (productItems.length > 0) {
            productItems.forEach(item => {
                const productName = item.querySelector('.product-name').textContent.toLowerCase();
                
                // .includes() replicates the SQL %like% behavior
                if (productName.includes(searchTerm)) {
                    item.style.display = 'block'; 
                    matchCount++;
                } else {
                    item.style.display = 'none'; 
                }
            });

            // Show 'No Match' message if nothing is found
            if (noSearchMatchMsg) {
                noSearchMatchMsg.style.display = matchCount === 0 ? 'block' : 'none';
            }
        }

        // Scroll gracefully to the product section
        if (productsSection) {
            productsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    // Trigger on Button Click
    if (searchBtn) {
        searchBtn.addEventListener('click', filterAndScrollProducts);
    }

    // Trigger when hitting 'Enter' key inside the search input
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault(); 
                filterAndScrollProducts();
            }
        });
    }
});
</script>

<?= $this->endSection() ?>