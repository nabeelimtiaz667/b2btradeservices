<?= $this->extend('layouts/main') ?>

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
        <h1 class="text-center h2"><?= isset($category) ? esc($category['name']) : 'Find Suppliers' ?><br>By Category</h1>
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
            <div class="col-md-3">
                <div class="supplier-side-form supplier-side-form-sub-page sup-cat">
                    <div class="sup-cat-form">
                         <h2 class="custom-h3 mb-3 text-white h3">Categories</h2>
                    <ul class="list-unstyled">
                        <?php if (isset($categories)): ?>
                            <?php foreach ($categories as $cat): ?>
                                <li class="mb-2">
                                    <a href="<?= base_url('supplier-category/' . $cat['slug']) ?>" class="text-white <?= (isset($category) && $category['id'] == $cat['id']) ? 'fw-bold text-primary' : '' ?>">
                                        <?= esc($cat['name']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                    </div>
                    
                    <div class="sup-cat-flag d-none">
                        <h2 class="custom-h3 mb-3 mt-4 h3">Find Suppliers By Country/Region</h2>
                    <div class="flags-grid mt-0 mb-5">
                        <?php if (isset($countries)): ?>
                            <?php foreach (array_slice($countries, 0, 10) as $country): ?>
                                <a href="<?= base_url('supplier-country/' . $country['code']) ?>" class="flag-item mt-3">
                                    <img src="<?= esc($country['flag']) ?>" alt="<?= esc($country['name']) ?>" onerror="this.style.display='none'">
                                    <?= esc($country['name']) ?>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    </div>
                   
                    
                    
                </div>
            </div>
            
            <div class="col-md-9">
                <div class="supplier-product-list newss w-100">
                    <?php if (isset($resultsTotal)): ?>
                        <p class="text-muted mb-3">Showing <?= count($suppliers ?? []) ?> results out of <?= $resultsTotal ?></p>
                    <?php endif; ?>
                    <div class="row gap-0">
                        <?php if (isset($suppliers) && count($suppliers) > 0): ?>
                            <?php foreach ($suppliers as $supplier): ?>
                                <div class="col-md-4 mb-4">
                                    <div class="supplier-product-list-box move-on-hover">
                                        <div class="supplier-logo-cover">
                                            <img src="<?= !empty($supplier['company_logo']) ? base_url('uploads/suppliers/' . $supplier['company_logo']) : base_url('assets/images/supplier-product-list-img.webp') ?>" class="w-100" onerror="this.onerror=null;this.src='<?= base_url('assets/images/supplier-product-list-img.webp') ?>'">
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
                                            <p class="supplier-list-info"><?= isset($supplier['category']) ? esc($supplier['category']['name']) : 'Other' ?></p>
                                            <h3 class="h4">
                                                <?= esc($supplier['company_name']) ?>
                                                <?php if (isset($supplier['is_verified']) && $supplier['is_verified']): ?>
                                                    <img src="<?= base_url('assets/images/badge-icon.svg') ?>">
                                                <?php endif; ?>
                                            </h3>
                                            <p>Products: <?= esc(substr($supplier['main_products'] ?? 'Various', 0, 30)) ?><br>
                                            Country: <?= isset($supplier['country']['name']) ? esc($supplier['country']['name']) : 'N/A' ?></p>
                                            <div class="supplier-product-list-box-img">
                                                <?php if (isset($supplier['products']) && count($supplier['products']) > 0): ?>
                                                    <?php foreach ($supplier['products'] as $product): ?>
                                                        <img src="<?= isset($product['main_image']) && $product['main_image'] ? base_url('uploads/products/' . $product['main_image']) : base_url('assets/images/supplier-product-img-1.webp') ?>" class="w-100">
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <img src="<?= base_url('assets/images/supplier-product-img-1.webp') ?>" class="w-100">
                                                    <img src="<?= base_url('assets/images/supplier-product-img-1.webp') ?>" class="w-100">
                                                <?php endif; ?>
                                            </div>
                                            <a class="bg-dark-btn" href="<?= base_url('supplier/profile/' . $supplier['slug']) ?>">View Profile</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12 text-center">
                                <p>No suppliers found in this category.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php if (isset($pager)): ?>
            <div class="d-flex justify-content-center mb-2">
                <?= $pager->links('supplier', 'default_full') ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="text-center mt-5 mb-5 pt-md-4 pb-md-4">
    <div class="container">
        <h2>The World's Leading Global B2B <br> Trading Platform</h2>
        <p>B2B Trade Services LLC is a global B2B marketplace with an extensive experience in wholesale trade solution. The platform connects verified buyers and suppliers worldwide, simplifying bulk sourcing through innovative e-commerce solutions. With intuitive web and mobile experiences, dedicated support, and trusted traders, B2B Trade Services delivers a seamless, reliable, and efficient global trading experience.</p>
        <a href="<?= base_url('about-us') ?>" class="read_more_link">Read More</a>
    </div>
</section>

<style>
    .supplier-product-list-box {
    width: 100%;
}
</style>

<?= $this->endSection() ?>
