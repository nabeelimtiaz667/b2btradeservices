<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="inner-banner-sec mt-4">
    <div class="container">
        <div class="inner-banner-img">
            <img src="<?= base_url('assets/images/welcome-banner.webp') ?>" class="w-100">
        </div>
    </div>
</section>

<section class="supplier-page-sec mt-5">
    <div class="container">
        <h2 class="text-center">Browse Products from Verified <br> Suppliers & Manufacturers</h2>
        <div class="searchbar-box mb-5">
            <form action="<?= base_url('product/search') ?>" method="get">
                <div class="searchbar-input">
                    <img src="<?= base_url('assets/images/search.svg') ?>">
                    <input type="search" name="q" placeholder="Search products by name, description..." value="<?= isset($searchKeyword) ? esc($searchKeyword) : '' ?>">
                    <button type="submit" class="outline-btn search-btn btn">Find Products</button>
                </div>
                <div id="moreOptions" class="options">
                    <div class="tripple-input">
                        <div class="form-input filter-icon-select">
                            <select name="category">
                                <option value="" <?= empty($selectedCategory ?? '') ? 'selected' : '' ?>>All Categories</option>
                                <?php if (isset($categories)): ?>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= esc($category['id']) ?>" <?= (isset($selectedCategory) && $selectedCategory == $category['id']) ? 'selected' : '' ?>><?= esc($category['name']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="row">
            <?php if (isset($products) && count($products) > 0): ?>
                <?php foreach ($products as $product): ?>
                    <div class="col-md-4 mb-4">
                        <div class="supplier-product-list-box move-on-hover">
                            <img src="<?= !empty($product['main_image']) ? base_url('uploads/products/' . $product['main_image']) : base_url('assets/images/supplier-product-img-1.webp') ?>" class="w-100" style="height: 220px; object-fit: cover;">
                            <div class="supplier-product-list-content">
                                <?php if (!empty($product['category'])): ?>
                                    <p class="supplier-list-info"><?= esc($product['category']['name']) ?></p>
                                <?php endif; ?>
                                <h4><?= esc($product['name']) ?></h4>
                                <?php if (!empty($product['supplier'])): ?>
                                    <p class="text-muted small mb-1">
                                        By: <a href="<?= base_url('supplier/profile/' . esc($product['supplier']['slug'] ?? '')) ?>"><?= esc($product['supplier']['company_name'] ?? 'N/A') ?></a>
                                    </p>
                                <?php endif; ?>
                                <p class="mb-1"><strong>Price:</strong> <?= esc($product['price_range'] ?? 'Contact for price') ?></p>
                                <p class="mb-2"><strong>MOQ:</strong> <?= esc($product['min_order_quantity'] ?? 'N/A') ?> <?= esc($product['min_order_unit'] ?? '') ?></p>
                                <a class="bg-dark-btn" href="<?= base_url('product/detail/' . $product['id']) ?>">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <h4>No products found</h4>
                    <p>Try adjusting your search criteria or browse all products.</p>
                    <a href="<?= base_url('product') ?>" class="btn btn-primary mt-2">View All Products</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php if (isset($categories) && count($categories) > 0): ?>
<section class="mt-5 mb-5 d-none">
    <div class="container">
        <h2 class="text-center">Browse by Category</h2>
        <div class="row mt-4">
            <?php foreach ($categories as $category): ?>
                <div class="col-md-3 col-6 mb-3">
                    <a href="<?= base_url('product/search?category=' . $category['id']) ?>" class="category-link">
                        <div class="category-box p-3 text-center border rounded">
                            <h5><?= esc($category['name']) ?></h5>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<style>
    .supplier-product-list-box {
    width: 100%;
}
</style>
<?php endif; ?>
<?= $this->endSection() ?>
