<?= $this->extend('layouts/inner') ?>

<?= $this->section('content') ?>
<section class="mt-4 mb-5">
    <div class="container">
        <h1 class="h2">Search Results for "<?= esc($keyword) ?>"</h1>

        <?php if (!empty($suppliers)): ?>
        <div class="mt-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h2 class="h3">Suppliers (<?= count($suppliers) ?>)</h2>
                <a href="<?= base_url('supplier/search/' . search_slug_encode($keyword)) ?>" class="view-all-link d-flex align-items-center gap-2">View All <img src="<?= base_url('assets/images/arrow-right.svg') ?>"></a>
            </div>
            <div class="row">
                <?php foreach ($suppliers as $supplier): ?>
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h3 class="card-title h5"><?= esc($supplier['company_name'] ?? $supplier['name']) ?></h3>
                            <p class="text-muted mb-1"><?= esc($supplier['selling_products'] ?? '') ?></p>
                            <p class="mb-2"><small><?= isset($supplier['country']['name']) ? esc($supplier['country']['name']) : '' ?></small></p>
                            <a href="<?= base_url('supplier/profile/' . ($supplier['slug'] ?? $supplier['id'])) ?>" class="outline-btn contact-btn btn btn-sm">View Profile</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($products)): ?>
        <div class="mt-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h2 class="h3">Products (<?= count($products) ?>)</h2>
                <a href="<?= base_url('product/search/' . search_slug_encode($keyword)) ?>" class="view-all-link d-flex align-items-center gap-2">View All <img src="<?= base_url('assets/images/arrow-right.svg') ?>"></a>
            </div>
            <div class="row">
                <?php foreach ($products as $product): ?>
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <?php if (!empty($product['main_image'])): ?>
                            <img src="<?= base_url('uploads/products/' . $product['main_image']) ?>" class="card-img-top" alt="<?= esc($product['name']) ?>" style="height: 180px; object-fit: cover;">
                        <?php else: ?>
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
                                <i class="fas fa-box fa-3x text-muted"></i>
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h3 class="card-title h5"><?= esc($product['name']) ?></h3>
                            <p class="text-muted mb-1"><?= isset($product['supplier']) ? esc($product['supplier']['company_name'] ?? $product['supplier']['name']) : '' ?></p>
                            <a href="<?= base_url('product/detail/' . $product['id']) ?>" class="outline-btn contact-btn btn btn-sm">View Details</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($inquiries)): ?>
        <div class="mt-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h2 class="h3">Buyer Inquiries (<?= count($inquiries) ?>)</h2>
                <a href="<?= base_url('buyer/search/' . search_slug_encode($keyword)) ?>" class="view-all-link d-flex align-items-center gap-2">View All <img src="<?= base_url('assets/images/arrow-right.svg') ?>"></a>
            </div>
            <div class="row">
                <?php foreach ($inquiries as $inquiry): ?>
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h3 class="card-title h5"><?= esc($inquiry['title'] ?? $inquiry['product_name'] ?? 'Buy Offer') ?></h3>
                            <p class="text-muted mb-1"><?= isset($inquiry['category']) && $inquiry['category'] ? esc($inquiry['category']['name']) : '' ?></p>
                            <p class="mb-2"><small><?= date('M d, Y', strtotime($inquiry['created_at'])) ?></small></p>
                            <a href="<?= inquiry_url($inquiry) ?>" class="outline-btn contact-btn btn btn-sm">View Details</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (empty($suppliers) && empty($products) && empty($inquiries)): ?>
        <div class="text-center mt-5 mb-5">
            <h2 class="h4">No results found for "<?= esc($keyword) ?>"</h2>
            <p class="text-muted">Try different keywords or browse our categories.</p>
            <div class="mt-3">
                <a href="<?= base_url('supplier') ?>" class="btn outline-btn me-2">Browse Suppliers</a>
                <a href="<?= base_url('product') ?>" class="btn outline-btn me-2">Browse Products</a>
                <a href="<?= base_url('buyers') ?>" class="btn outline-btn">Browse Buyers</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>
<?= $this->endSection() ?>
