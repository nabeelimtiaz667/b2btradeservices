<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="page-title"><?= $inquiry ? 'Edit Inquiry' : 'Post New Inquiry' ?></h1>
        <p class="text-muted mb-0"><?= $inquiry ? 'Update your inquiry details' : 'Submit a new product inquiry' ?></p>
    </div>
    <a href="<?= base_url('dashboard/buyer/inquiries') ?>" class="btn btn-outline-secondary px-4 py-2">
        Back to Inquiries
    </a>
</div>

<?php if (session()->getFlashdata('error')): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?= session()->getFlashdata('error') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card card-custom">
    <div class="card-body">
        <form action="<?= $inquiry ? base_url('dashboard/buyer/inquiries/edit/' . $inquiry['id']) : base_url('dashboard/buyer/inquiries/add') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="title" class="form-label fw-bold">Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="title" name="title" value="<?= esc($inquiry['title'] ?? old('title')) ?>" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="product_name" class="form-label fw-bold">Product Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="product_name" name="product_name" value="<?= esc($inquiry['product_name'] ?? old('product_name')) ?>" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="category_id" class="form-label fw-bold">Category</label>
                    <select class="form-select" id="category_id" name="category_id">
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>" <?= (($inquiry['category_id'] ?? old('category_id')) == $category['id']) ? 'selected' : '' ?>>
                                <?= esc($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="country_id" class="form-label fw-bold">Country</label>
                    <select class="form-select" id="country_id" name="country_id">
                        <option value="">Select Country</option>
                        <?php foreach ($countries as $country): ?>
                            <option value="<?= $country['id'] ?>" <?= (($inquiry['country_id'] ?? old('country_id')) == $country['id']) ? 'selected' : '' ?>>
                                <?= esc($country['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="quantity" class="form-label fw-bold">Quantity</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" value="<?= esc($inquiry['quantity'] ?? old('quantity')) ?>" min="1">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="unit" class="form-label fw-bold">Unit</label>
                    <input type="text" class="form-control" id="unit" name="unit" value="<?= esc($inquiry['unit'] ?? old('unit')) ?>" placeholder="e.g. Pieces, MT, Cartons">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="target_price" class="form-label fw-bold">Target Price</label>
                    <input type="text" class="form-control" id="target_price" name="target_price" value="<?= esc($inquiry['target_price'] ?? old('target_price')) ?>" placeholder="e.g. $5.00/piece">
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label fw-bold">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4"><?= esc($inquiry['description'] ?? old('description')) ?></textarea>
            </div>

            <hr class="my-4">

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="shipping_terms" class="form-label fw-bold">Shipping Terms</label>
                    <input type="text" class="form-control" id="shipping_terms" name="shipping_terms" value="<?= esc($inquiry['shipping_terms'] ?? old('shipping_terms')) ?>" placeholder="e.g. FOB, CIF, EXW">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="payment_terms" class="form-label fw-bold">Payment Terms</label>
                    <input type="text" class="form-control" id="payment_terms" name="payment_terms" value="<?= esc($inquiry['payment_terms'] ?? old('payment_terms')) ?>" placeholder="e.g. T/T, L/C, PayPal">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="destination_port" class="form-label fw-bold">Destination Port</label>
                    <input type="text" class="form-control" id="destination_port" name="destination_port" value="<?= esc($inquiry['destination_port'] ?? old('destination_port')) ?>" placeholder="e.g. Los Angeles, Rotterdam">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="validity_date" class="form-label fw-bold">Validity Date</label>
                    <input type="date" class="form-control" id="validity_date" name="validity_date" value="<?= esc($inquiry['validity_date'] ?? old('validity_date')) ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="attachment" class="form-label fw-bold">Reference Image</label>
                    <small class="text-muted d-block mb-2">Upload a reference image to help suppliers provide accurate quotations. Max size: 999 KB. (JPG, PNG, WEBP)</small>
                    <?php if ($inquiry && !empty($inquiry['attachment'])): ?>
                        <div class="mb-2">
                            <img src="<?= base_url('uploads/inquiries/' . $inquiry['attachment']) ?>" alt="Reference Image" style="width: 120px; height: 120px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd;">
                        </div>
                        <small class="text-muted d-block mb-2">Upload a new image to replace the current one.</small>
                    <?php endif; ?>
                    <input type="file" class="form-control" id="attachment" name="attachment" accept="image/jpeg,image/png,image/webp">
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-success px-5 py-2 me-2"><?= $inquiry ? 'Update Inquiry' : 'Post Inquiry' ?></button>
                <a href="<?= base_url('dashboard/buyer/inquiries') ?>" class="btn btn-outline-secondary px-4 py-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
