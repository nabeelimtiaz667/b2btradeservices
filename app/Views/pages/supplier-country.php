<?= $this->extend('layouts/inner') ?>

<?= $this->section('content') ?>
<section class="inner-banner-sec mt-4">
    <div class="container">
        <div class="inner-banner-img">
            <img src="<?= base_url('assets/images/supplier-category-banner.webp') ?>" class="w-100">
        </div>
    </div>
</section>

<section class="supplier-page-sec mt-5">
    <div class="container">
        <h1 class="text-center h2">Find Suppliers <br>By Country/Region</h1>
        <div class="searchbar-box mb-5">
            <form action="<?= base_url('supplier/search') ?>" method="get">
                <div class="searchbar-input">
                    <img src="<?= base_url('assets/images/search.svg') ?>">
                    <input type="search" name="q" placeholder="What are you looking for?">
                    <button type="submit" class="outline-btn search-btn btn">Find Supplier</button>
                </div>
            </form>
        </div>

        <div class="row mt-md-5 mt-4 align-items-start">
            <div class="supplier-side-form supplier-side-form-sub-page">
                <div class="multiple-quote-form mb-5">
                    <h2 class="text-white text-center mb-1">Let us Connect you</h2>
                    <h3 class="text-white text-center mb-3">with Relevant Buyers</h3>
                    <form action="<?= base_url('register') ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="role" value="supplier">
                        <div class="form-input">
                            <input type="text" placeholder="Name" name="name" required>
                        </div>
                        <div class="form-input">
                            <input type="email" placeholder="Email" name="email" required>
                        </div>
                        <div class="form-input">
                            <input type="tel" placeholder="Phone" class="phone" name="phone">
                        </div>
                        <div class="form-input password-input mt-2">
                            <input type="password" class="password" placeholder="Password" name="password" required>
                            <i class="eye mt-2 pt-1" onclick="togglePassword()"><svg style="fill: #DBDBDB" class="eye-icon" height="20" viewBox="0 0 24 24" width="20" xmlns="http://www.w3.org/2000/svg"><path clip-rule="evenodd" d="m12 4.5c-5 0-9.27 3.11-11 7.5 1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zm0 12.5c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"></path></svg></i>
                        </div>
                        <div class="submit-btn">
                            <button type="submit">Submit Now</button>
                        </div>
                    </form>
                </div>
                 <div class="sup-cat-flag">
                <h3 class="custom-h3 mb-2">Find Suppliers <br>By Country/Region</h3>
                <div class="flags-grid mt-0 mb-5">
                    <?php if (isset($countries)): ?>
                        <?php foreach ($countries as $c): ?>
                            <a href="<?= base_url('supplier-country/' . ($c['code'] ?? strtolower(str_replace(' ', '-', $c['name'])))) ?>" class="flag-item mt-3 <?= (isset($country) && $country['id'] == $c['id']) ? 'fw-bold' : '' ?>">
                                <img src="<?= base_url('assets/images/flags/' . strtolower(str_replace(' ', '-', $c['name'])) . '.svg') ?>" alt="<?= esc($c['name']) ?>" onerror="this.style.display='none'">
                                <?= esc($c['name']) ?>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                </div>
            </div>
            <div class="supplier-product-list">
                <?php if (isset($resultsTotal)): ?>
                    <p class="text-muted mb-3">Showing <?= count($suppliers ?? []) ?> results out of <?= $resultsTotal ?></p>
                <?php endif; ?>
                <div class="row">
                    <?php if (isset($suppliers) && count($suppliers) > 0): ?>
                        <?php foreach ($suppliers as $supplier): ?>
                            <div class="supplier-product-list-box move-on-hover">
                                <div class="supplier-logo-cover">
                                    <img src="<?= !empty($supplier['company_logo']) ? base_url('uploads/suppliers/' . $supplier['company_logo']) : base_url('assets/images/supplier-product-list-img.webp') ?>" class="" onerror="this.onerror=null;this.src='<?= base_url('assets/images/supplier-product-list-img.webp') ?>'">
                                </div>
                                <div class="supplier-product-list-content">
                                    <div class="sp-membership-icon">
                                        <?php if (isset($supplier['membership_level']) && $supplier['membership_level'] == 'free'): ?>
                                            <img src="<?= base_url('assets/images/free-membership-coin.webp') ?>" style="width: 50px; margin-bottom: 10px;">
                                        <?php elseif (isset($supplier['membership_level']) && $supplier['membership_level'] == 'starter'): ?>
                                            <img src="<?= base_url('assets/images/starter-coin.webp') ?>" style="width: 50px; margin-bottom: 10px;">
                                        <?php elseif (isset($supplier['membership_level']) && $supplier['membership_level'] == 'gold'): ?>
                                            <img src="<?= base_url('assets/images/gold-coin.webp') ?>" style="width: 50px; margin-bottom: 10px;">
                                        <?php elseif (isset($supplier['membership_level']) && $supplier['membership_level'] == 'platinum'): ?>
                                            <img src="<?= base_url('assets/images/palti-coin.webp') ?>" style="width: 50px; margin-bottom: 10px;">
                                        <?php elseif (isset($supplier['membership_level']) && $supplier['membership_level'] == 'vip'): ?>
                                            <img src="<?= base_url('assets/images/vip-coin.webp') ?>" style="width: 50px; margin-bottom: 10px;">
                                        <?php endif; ?>
                                    </div>
                                    <h4>
                                        <?= esc($supplier['company_name'] ?? $supplier['name']) ?>
                                        <?php if (!empty($supplier['country']['flag'] ?? '')): ?>
                                            <img src="<?= base_url('assets/images/flags/' . $supplier['country']['flag']) ?>" width="20" onerror="this.style.display='none'">
                                        <?php endif; ?>
                                    </h4>
                                    <p>Products: <?= esc($supplier['selling_products'] ?? 'Various') ?><br>
                                    Country: <?= isset($supplier['country']['name']) ? esc($supplier['country']['name']) : 'N/A' ?></p>
                                    <div class="supplier-product-list-box-img">
                                        <?php if (isset($supplier['products']) && count($supplier['products']) > 0): ?>
                                            <?php foreach (array_slice($supplier['products'], 0, 2) as $product): ?>
                                                <img src="<?= !empty($product['main_image']) ? base_url('uploads/products/' . $product['main_image']) : base_url('assets/images/supplier-product-img-1.webp') ?>">
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <img src="<?= base_url('assets/images/supplier-product-img-1.webp') ?>">
                                            <img src="<?= base_url('assets/images/supplier-product-img-2.webp') ?>">
                                        <?php endif; ?>
                                    </div>
                                    <a class="bg-dark-btn" href="<?= base_url('supplier/profile/' . ($supplier['slug'] ?? $supplier['id'])) ?>">View Profile</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center w-100">
                            <p>No suppliers found<?= isset($country) ? ' in ' . esc($country['name']) : '' ?>.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php if (isset($pager)): ?>
            <div class="d-flex justify-content-center mt-4 mb-2">
                <?= $pager->links('supplier', 'default_full') ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="success-stories-sec mt-md-5 mt-2 pt-lg-4">
    <div class="container">
        <div class="row align-items-end">
            <div class="multiple-quote-form">
                <h2 class="text-white text-center">Find us</h2>
                <form action="<?= base_url('contact/submit') ?>" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="form_type" value="contact">
                    <input type="hidden" name="source_page" value="supplier-country">
                    <input type="hidden" name="lead_type" value="supplier">
                    <div class="form-input">
                        <input type="text" name="name" placeholder="Name" required>
                    </div>
                    <div class="dual-input">
                        <div class="form-input">
                            <input type="email" name="email" placeholder="Email" required>
                        </div>
                        <div class="form-input">
                            <input type="tel" name="phone" class="phone" placeholder="Phone">
                        </div>
                    </div>
                    <div class="dual-input">
                        <div class="form-input">
                            <input type="text" name="industry" placeholder="Select Industry">
                        </div>
                        <div class="form-input">
                            <input type="number" name="quantity" placeholder="Quantity">
                        </div>
                    </div>
                    <div class="form-textarea">
                        <textarea name="message" placeholder="What are you looking for?"></textarea>
                    </div>
                    <div class="submit-btn">
                        <button type="submit">Submit Now</button>
                    </div>
                </form>
            </div>
            <div class="success-stories-col">
                <div class="d-flex heading align-items-center justify-content-between">
                    <h2 class="mb-0">Success Stories</h2>
                    <a href="#" class="view-all-link d-flex align-items-center gap-2"> View All <img src="<?= base_url('assets/images/arrow-right.svg') ?>"></a>
                </div>
                <div class="success-stories-slider-sec">
                    <div class="success-stories-slider">
                        <div>
                            <p>B2B Trade Services helped us connect with verified suppliers across the globe. Our procurement process has become seamless and efficient. The platform's lead management system made it easy to track every interaction.</p>
                            <p class="client-name mb-1">Ahmed Khan</p>
                            <p class="success-stories-compay-name mb-0">Global Imports LLC</p>
                        </div>
                        <div>
                            <p>We found reliable buyers for our agricultural products within weeks of joining. The platform's country-specific search made it easy to target the right markets. Highly recommended for any supplier looking to expand globally.</p>
                            <p class="client-name mb-1">Sarah Mitchell</p>
                            <p class="success-stories-compay-name mb-0">AgriExport Co.</p>
                        </div>
                        <div>
                            <p>The B2B marketplace transformed our export business. We connected with premium buyers from multiple countries and our revenue grew significantly. The verification system gives us confidence in every trade partner.</p>
                            <p class="client-name mb-1">Rajesh Patel</p>
                            <p class="success-stories-compay-name mb-0">Patel Trading Corp</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="text-center mt-5 mb-5 pt-md-4 pb-md-4">
    <div class="container">
        <h2>The World's Leading Global B2B <br> Trading Platform</h2>
        <p>B2B Trade Services LLC is a global B2B marketplace with an extensive experience in wholesale trade solution. The platform connects verified buyers and suppliers worldwide, simplifying bulk sourcing through innovative e-commerce solutions. With intuitive web and mobile experiences, dedicated support, and trusted traders, B2B Trade Services delivers a seamless, reliable, and efficient global trading experience.</p>
        <a href="<?= base_url('about-us') ?>" class="read_more_link">Read More</a>
    </div>
</section>
<?= $this->endSection() ?>
