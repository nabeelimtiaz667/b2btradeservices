<?= $this->extend('layouts/inner') ?>

<?= $this->section('content') ?>
<section class="inner-banner-sec mt-4">
    <div class="container">
        <div class="inner-banner-img">
            <img src="<?= base_url('assets/images/contact-us-banner.webp') ?>" class="w-100">
        </div>
    </div>
</section>

<section class="supplier-page-sec mt-5">
    <div class="container">
        <h1 class="text-center h2">Find Verified Exporters, Global Suppliers and Worldwide recognized Companies</h1>
        <div class="searchbar-box mb-5">
            <form action="<?= base_url('supplier/search') ?>" method="get">
                <div class="searchbar-input">
                    <img src="<?= base_url('assets/images/search.svg') ?>">
                    <input type="search" name="q" placeholder="What are you looking for?" value="<?= isset($searchKeyword) ? esc($searchKeyword) : '' ?>">
                    <button type="submit" class="outline-btn search-btn btn">Find Supplier</button>
                </div>
                <div id="moreOptions" class="options" style="display: none;">
                    <div class="tripple-input">
                        <div class="form-input filter-icon-select filter-by-membership">
                            <select name="membership">
                                <option value="" disabled selected>Show Premium members only</option>
                                <option value="starter">Starter</option>
                                <option value="Gold">Gold</option>
                                <option value="Platinum">Platinum</option>
                                <option value="VIP">VIP</option>
                            </select>
                        </div>
                        <div class="form-input">
                            <select class="form-control country-select country-icon-select filter-icon-select" name="country">
                                <option value="" selected>Country</option>
                                <?php if (isset($countries)): ?>
                                    <?php foreach ($countries as $country): ?>
                                        <option value="<?= $country['id'] ?>"><?= esc($country['name']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="form-input filter-icon-select filter-by-category">
                            <select name="category">
                                <option disabled selected>Category</option>
                                <?php if (isset($categories)): ?>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>"><?= esc($cat['name']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <button id="toggleBtn" class="toggle-btn" type="button" onclick="toggleOptions()">
                    <span id="btnText">More Options</span>
                    <img id="arrowIcon" src="<?= base_url('assets/images/arrow-down.svg') ?>" width="12" alt="arrow" />
                </button>
            </form>
        </div>

        <?php if (isset($searchKeyword) && !empty($searchKeyword)): ?>
        <div class="mb-5">
            <h2 class="text-center custom-h3 h3">Search Results for "<?= esc($searchKeyword) ?>"</h2>
            <div class="supplier-product-list">
                <?php if (isset($resultsTotal)): ?>
                    <p class="text-muted mb-0">Showing <?= count($suppliers ?? []) ?> results out of <?= $resultsTotal ?></p>
                <?php endif; ?>
                <div class="row mt-4">
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
                                    <h3 class="h4">
                                        <?php $usedCompanyName = !empty($supplier['company_name']); ?>
                                        <?= highlight_keyword($usedCompanyName ? $supplier['company_name'] : $supplier['name'], $searchKeyword) ?>
                                        <?php if (!empty($supplier['country']['flag'] ?? '')): ?>
                                            <img src="<?= base_url('assets/images/flags/' . $supplier['country']['flag']) ?>" width="20" onerror="this.style.display='none'">
                                        <?php endif; ?>
                                    </h3>
                                    <p>Products: <?= !empty($supplier['selling_products']) ? highlight_keyword($supplier['selling_products'], $searchKeyword) : 'Various' ?><br>
                                    Country: <?= isset($supplier['country']['name']) ? esc($supplier['country']['name']) : 'N/A' ?></p>
                                    <?php
                                        $hiddenMatches = $usedCompanyName ? count_keyword_occurrences($supplier['name'] ?? '', $searchKeyword) : 0;
                                        if ($hiddenMatches > 0):
                                    ?>
                                        <p class="small mb-2" style="background:#bfff4fd9; padding:4px 8px; border-radius:4px;">
                                            "<?= esc($searchKeyword) ?>" appeared <?= $hiddenMatches ?> more time<?= $hiddenMatches > 1 ? 's' : '' ?> in this record.
                                        </p>
                                    <?php endif; ?>
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
                            <p>No suppliers found. Please try a different search.</p>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if (isset($searchPager)): ?>
                    <div class="d-flex justify-content-center mb-100 mb-2">
                        <?= $this->include('partials/search-pager') ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>


        <?php if (!isset($searchKeyword) || empty($searchKeyword)): ?>
<section class="row mt-md-5 mt-4 align-items-start sp-page">
    <div class="container">
        <h2 class="text-center custom-h3 h3">All Suppliers</h2>
        <div class="supplier-product-list supplier-product-list-main">
            <?php if (isset($resultsTotal)): ?>
                <p class="text-muted text-center mb-0">Showing <?= count($suppliers ?? []) ?> results out of <?= $resultsTotal ?></p>
            <?php endif; ?>
            <div class="row mt-4">
                <?php if (isset($suppliers) && count($suppliers) > 0): ?>
                    <?php foreach ($suppliers as $supplier): ?>
                        <div class="supplier-product-list-box move-on-hover">
                            <?php // print_r($suppliers); exit();?>
                            <div class="supplier-logo-cover">
                                <img src="<?= !empty($supplier['company_logo']) ? base_url('uploads/suppliers/'. $supplier['company_logo'])  : base_url('assets/images/supplier-product-list-img.webp') ?>" class="" onerror="this.onerror=null;this.src='<?= base_url('assets/images/supplier-product-list-img.webp') ?>'">
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
                                <h3 class="h4">
                                    <?= esc($supplier['company_name'] ?? $supplier['name']) ?>
                                    <?php if (!empty($supplier['country']['flag'] ?? '')): ?>
                                        <img src="<?= base_url('assets/images/flags/' . $supplier['country']['flag']) ?>" width="20" onerror="this.style.display='none'">
                                    <?php endif; ?>
                                </h3>
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
                        <p>No suppliers found. Please try a different search.</p>
                    </div>
                <?php endif; ?>
            </div>
            <?php if (isset($searchPager)): ?>
                <div class="d-flex justify-content-center mb-100 mb-2">
                    <?= $this->include('partials/search-pager') ?>
                </div>
            <?php elseif (isset($pager)): ?>
                <div class="d-flex justify-content-center mb-100 mb-2">
                    <?= $pager->links('supplier', 'default_full') ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

        <h2 class="text-center custom-h3 h3">Find Suppliers <br>By Country/Region</h2>

        <div class="row mt-5 align-items-start">
            <div class="supplier-side-form">
                <div class="multiple-quote-form">
                    <h2 class="text-white text-center">Connect with Top Suppliers for FREE</h2>
                    <form action="<?= base_url('register') ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="role" value="supplier">
                        <div class="form-input">
                            <input type="text" placeholder="Name*" name="name" required>
                        </div>
                        <div class="form-input">
                            <input type="email" placeholder="Email*" name="email" required>
                        </div>
                        <div class="form-input">
                            <input type="tel" placeholder="Phone*" class="phone" name="phone" required>
                        </div>
                        <div class="form-input password-input mt-2">
                            <input type="password" class="password" placeholder="Password*" name="password" required>
                            <i class="eye mt-2 pt-1" onclick="togglePassword()"><svg style="fill: #DBDBDB" class="eye-icon" height="20" viewBox="0 0 24 24" width="20" xmlns="http://www.w3.org/2000/svg"><path clip-rule="evenodd" d="m12 4.5c-5 0-9.27 3.11-11 7.5 1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zm0 12.5c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"></path></svg></i>
                        </div>
                        <div class="submit-btn">
                            <button type="submit">Submit Now</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="supplier-page-flag">
                <div class="flags-grid">
                    <?php if (isset($countries) && count($countries) > 0): ?>
                        <?php foreach (array_slice($countries, 0, 32) as $c): ?>
                            <a href="<?= base_url('supplier-country/' . ($c['code'] ?? strtolower(str_replace(' ', '-', $c['name'])))) ?>" class="flag-item">
                                <img src="<?= base_url('assets/images/flags/' . strtolower(str_replace(' ', '-', $c['name'])) . '.svg') ?>" alt="<?= esc($c['name']) ?>" onerror="this.style.display='none'"> <?= esc($c['name']) ?>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="mt-5 pt-md-5 pb-4 mb-5">
    <div class="container">
        <h2 class="text-center custom-h3 h3">Find Suppliers <br>By Industry</h2>
        <div class="category-slider-sec mt-md-5 mt-3">
            <div class="category-slider">
                <div>
                    <div class="row">
                        <?php if (isset($categories)): ?>
                            <?php foreach ($categories as $idx => $cat): ?>
                                <div class="category-icon-box justify-content-center d-flex align-items-center gap-2">
                                    <img src="<?= base_url('assets/images/category-icon-' . (($idx % 10) + 1) . '.svg') ?>"><?= esc($cat['name']) ?>
                                    <a href="<?= base_url('supplier-category/' . $cat['slug']) ?>"></a>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<style>
    .supplier-product-list-box {
    width: 32%;
}
@media(max-width: 767.5px) {
    .supplier-product-list-box {
    width: 100%;
}
    
}
</style>

<?= $this->endSection() ?>
