<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="page-title"><?= $product ? 'Edit Product' : 'Add Product' ?></h1>
        <p class="text-muted mb-0"><?= $product ? 'Update your product details' : 'Add a new product to your catalog' ?></p>
    </div>
    <?php if (isset($adminMode) && $adminMode && isset($supplier)): ?>
        <a href="<?= base_url('dashboard/suppliers/edit/' . $supplier['id']) ?>" class="btn btn-outline-secondary px-4 py-2">Back to Supplier</a>
    <?php else: ?>
        <a href="<?= base_url('dashboard/supplier/products') ?>" class="btn btn-outline-secondary px-4 py-2">Back to Products</a>
    <?php endif; ?>
</div>

<?php if (session()->getFlashdata('error')): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?= session()->getFlashdata('error') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card card-custom">
    <div class="card-body">
        <?php
            if (isset($adminMode) && $adminMode && isset($supplier)) {
                if ($product) {
                    $formAction = base_url('dashboard/suppliers/' . $supplier['id'] . '/edit-product/' . $product['id']);
                } else {
                    $formAction = base_url('dashboard/suppliers/' . $supplier['id'] . '/add-product');
                }
            } elseif ($product) {
                $formAction = base_url('dashboard/supplier/products/edit/' . $product['id']);
            } else {
                $formAction = base_url('dashboard/supplier/products/add');
            }
        ?>
        <form action="<?= $formAction ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold">Product Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= esc($product['name'] ?? old('name')) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" <?= (($product['category_id'] ?? old('category_id')) == $category['id']) ? 'selected' : '' ?>>
                                    <?= esc($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-bold">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="5"><?= esc($product['description'] ?? old('description')) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="specifications" class="form-label fw-bold">Specifications</label>
                        <textarea class="form-control" id="specifications" name="specifications" rows="4"><?= esc($product['specifications'] ?? old('specifications')) ?></textarea>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="main_image" class="form-label fw-bold">Main Image</label>
                        <?php if ($product && !empty($product['main_image'])): ?>
                            <div class="mb-2">
                                <img src="<?= base_url('uploads/products/' . $product['main_image']) ?>" alt="Current image" style="width: 100%; max-width: 200px; border-radius: 8px;">
                            </div>
                            <small class="text-muted d-block mb-2">Upload a new image to replace the current one.</small>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="main_image" name="main_image" accept="image/*">
                    </div>

                    <div class="mb-3">
                        <label for="price_range" class="form-label fw-bold">Price Range</label>
                        <input type="text" class="form-control" id="price_range" name="price_range" value="<?= esc($product['price_range'] ?? old('price_range')) ?>" placeholder="e.g. $100-$500">
                    </div>

                    <div class="mb-3">
                        <label for="min_order_quantity" class="form-label fw-bold">Min Order Quantity</label>
                        <input type="number" class="form-control" id="min_order_quantity" name="min_order_quantity" value="<?= esc($product['min_order_quantity'] ?? old('min_order_quantity')) ?>" min="1">
                    </div>

                    <div class="mb-3">
                        <label for="min_order_unit" class="form-label fw-bold">Min Order Unit</label>
                        <input type="text" class="form-control" id="min_order_unit" name="min_order_unit" value="<?= esc($product['min_order_unit'] ?? old('min_order_unit')) ?>" placeholder="e.g. Pieces, Sets">
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="supply_ability" class="form-label fw-bold">Supply Ability</label>
                    <input type="text" class="form-control" id="supply_ability" name="supply_ability" value="<?= esc($product['supply_ability'] ?? old('supply_ability')) ?>" placeholder="e.g. 10000 Pieces/Month">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="delivery_time" class="form-label fw-bold">Delivery Time</label>
                    <input type="text" class="form-control" id="delivery_time" name="delivery_time" value="<?= esc($product['delivery_time'] ?? old('delivery_time')) ?>" placeholder="e.g. 15-30 days">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="packaging" class="form-label fw-bold">Packaging</label>
                    <input type="text" class="form-control" id="packaging" name="packaging" value="<?= esc($product['packaging'] ?? old('packaging')) ?>" placeholder="e.g. Standard export packaging">
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="port" class="form-label fw-bold">Port</label>
                    <input type="text" class="form-control" id="port" name="port" value="<?= esc($product['port'] ?? old('port')) ?>" placeholder="e.g. Shanghai, Shenzhen">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="payment_terms" class="form-label fw-bold">Payment Terms</label>
                    <input type="text" class="form-control" id="payment_terms" name="payment_terms" value="<?= esc($product['payment_terms'] ?? old('payment_terms')) ?>" placeholder="e.g. T/T, L/C, PayPal">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="certifications" class="form-label fw-bold">Certifications</label>
                    <input type="text" class="form-control" id="certifications" name="certifications" value="<?= esc($product['certifications'] ?? old('certifications')) ?>" placeholder="e.g. ISO 9001, CE, RoHS">
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-success px-5 py-2 me-2"><?= $product ? 'Update Product' : 'Add Product' ?></button>
                <?php if (isset($adminMode) && $adminMode && isset($supplier)): ?>
                    <a href="<?= base_url('dashboard/suppliers/edit/' . $supplier['id']) ?>" class="btn btn-outline-secondary px-4 py-2">Cancel</a>
                <?php else: ?>
                    <a href="<?= base_url('dashboard/supplier/products') ?>" class="btn btn-outline-secondary px-4 py-2">Cancel</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
