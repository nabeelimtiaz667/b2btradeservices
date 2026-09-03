<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="page-header d-flex justify-content-between align-items-center">
    <h1 class="page-title"><?= isset($inquiry) && $inquiry ? 'Edit Buyer Inquiry' : 'Add Buyer Inquiry' ?></h1>
    <a href="<?= base_url('dashboard/inquiries') ?>" class="btn btn-outline-secondary">Back to Inquiries</a>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div class="card card-custom">
    <div class="card-body">
        <form action="<?= isset($inquiry) && $inquiry ? base_url('dashboard/inquiries/edit/' . $inquiry['id']) : base_url('dashboard/inquiries/add') ?>" method="post" enctype="multipart/form-data">
            <h5 class="mb-3">Inquiry Details</h5>
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Title *</label>
                    <input type="text" name="title" class="form-control" value="<?= isset($inquiry) ? esc($inquiry['title']) : old('title') ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Product Name *</label>
                    <input type="text" name="product_name" class="form-control" value="<?= isset($inquiry) ? esc($inquiry['product_name'] ?? '') : old('product_name') ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Category *</label>
                    <select name="category_id" class="form-control" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= (isset($inquiry) && $inquiry['category_id'] == $cat['id']) ? 'selected' : '' ?>><?= esc($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Quantity</label>
                    <input type="number" name="quantity" class="form-control" value="<?= isset($inquiry) ? esc($inquiry['quantity'] ?? '') : old('quantity') ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Unit</label>
                    <select name="unit" class="form-control">
                        <option value="">Select Unit</option>
                        <option value="pieces" <?= (isset($inquiry) && ($inquiry['unit'] ?? '') == 'pieces') ? 'selected' : '' ?>>Pieces</option>
                        <option value="kg" <?= (isset($inquiry) && ($inquiry['unit'] ?? '') == 'kg') ? 'selected' : '' ?>>Kilograms</option>
                        <option value="tons" <?= (isset($inquiry) && ($inquiry['unit'] ?? '') == 'tons') ? 'selected' : '' ?>>Tons</option>
                        <option value="meters" <?= (isset($inquiry) && ($inquiry['unit'] ?? '') == 'meters') ? 'selected' : '' ?>>Meters</option>
                        <option value="sets" <?= (isset($inquiry) && ($inquiry['unit'] ?? '') == 'sets') ? 'selected' : '' ?>>Sets</option>
                        <option value="containers" <?= (isset($inquiry) && ($inquiry['unit'] ?? '') == 'containers') ? 'selected' : '' ?>>Containers</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Target Price</label>
                    <input type="text" name="target_price" class="form-control" value="<?= isset($inquiry) ? esc($inquiry['target_price'] ?? '') : old('target_price') ?>" placeholder="e.g., $5.00/piece">
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4"><?= isset($inquiry) ? esc($inquiry['description'] ?? '') : old('description') ?></textarea>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Shipping Terms</label>
                    <select name="shipping_terms" class="form-control">
                        <option value="">Select Terms</option>
                        <option value="FOB" <?= (isset($inquiry) && ($inquiry['shipping_terms'] ?? '') == 'FOB') ? 'selected' : '' ?>>FOB</option>
                        <option value="CIF" <?= (isset($inquiry) && ($inquiry['shipping_terms'] ?? '') == 'CIF') ? 'selected' : '' ?>>CIF</option>
                        <option value="EXW" <?= (isset($inquiry) && ($inquiry['shipping_terms'] ?? '') == 'EXW') ? 'selected' : '' ?>>EXW</option>
                        <option value="DDP" <?= (isset($inquiry) && ($inquiry['shipping_terms'] ?? '') == 'DDP') ? 'selected' : '' ?>>DDP</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Payment Terms</label>
                    <select name="payment_terms" class="form-control">
                        <option value="">Select Terms</option>
                        <option value="T/T" <?= (isset($inquiry) && ($inquiry['payment_terms'] ?? '') == 'T/T') ? 'selected' : '' ?>>T/T</option>
                        <option value="L/C" <?= (isset($inquiry) && ($inquiry['payment_terms'] ?? '') == 'L/C') ? 'selected' : '' ?>>L/C</option>
                        <option value="D/P" <?= (isset($inquiry) && ($inquiry['payment_terms'] ?? '') == 'D/P') ? 'selected' : '' ?>>D/P</option>
                        <option value="PayPal" <?= (isset($inquiry) && ($inquiry['payment_terms'] ?? '') == 'PayPal') ? 'selected' : '' ?>>PayPal</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Destination Port</label>
                    <input type="text" name="destination_port" class="form-control" value="<?= isset($inquiry) ? esc($inquiry['destination_port'] ?? '') : old('destination_port') ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Date of Inquiry</label>
                    <input type="date" name="inquiry_date" class="form-control" value="<?= isset($inquiry) ? esc($inquiry['inquiry_date'] ?? '') : (old('inquiry_date') ?: date('Y-m-d')) ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Product Reference Image</label>
                    <input type="file" name="attachment" class="form-control" accept="image/jpeg,image/png,image/webp">
                    <small class="text-muted">Accepted formats: JPEG, PNG, WebP. Max size: 1 MB</small>
                    <?php if (isset($inquiry) && !empty($inquiry['attachment'])): ?>
                        <div class="mt-2">
                            <img src="<?= base_url('uploads/inquiries/' . $inquiry['attachment']) ?>" alt="Attachment" style="max-width: 150px; max-height: 150px; border: 1px solid #ddd; border-radius: 4px;">
                            <div class="form-check mt-1">
                                <input type="checkbox" name="remove_attachment" value="1" class="form-check-input" id="remove_attachment">
                                <label class="form-check-label" for="remove_attachment">Remove current image</label>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <hr>
            <h5 class="mb-3">Buyer Information</h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Buyer Name *</label>
                    <input type="text" name="buyer_name" class="form-control" value="<?= isset($inquiry) ? esc($inquiry['buyer_name']) : old('buyer_name') ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Company Name</label>
                    <input type="text" name="buyer_company" class="form-control" value="<?= isset($inquiry) ? esc($inquiry['buyer_company'] ?? '') : old('buyer_company') ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email *</label>
                    <input type="email" name="buyer_email" class="form-control" value="<?= isset($inquiry) ? esc($inquiry['buyer_email']) : old('buyer_email') ?>" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="buyer_phone" class="form-control" value="<?= isset($inquiry) ? esc($inquiry['buyer_phone'] ?? '') : old('buyer_phone') ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">WhatsApp</label>
                    <input type="text" name="buyer_whatsapp" class="form-control" value="<?= isset($inquiry) ? esc($inquiry['buyer_whatsapp'] ?? '') : old('buyer_whatsapp') ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Country *</label>
                    <select name="country_id" class="form-control" required>
                        <option value="">Select Country</option>
                        <?php foreach ($countries as $country): ?>
                            <option value="<?= $country['id'] ?>" <?= (isset($inquiry) && $inquiry['country_id'] == $country['id']) ? 'selected' : '' ?>><?= esc($country['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="active" <?= (isset($inquiry) && ($inquiry['status'] ?? '') == 'active') ? 'selected' : '' ?>>Active</option>
                        <option value="pending" <?= (isset($inquiry) && ($inquiry['status'] ?? '') == 'pending') ? 'selected' : '' ?>>Pending</option>
                        <option value="inactive" <?= (isset($inquiry) && ($inquiry['status'] ?? '') == 'inactive') ? 'selected' : '' ?>>Inactive</option>
                        <option value="closed" <?= (isset($inquiry) && ($inquiry['status'] ?? '') == 'closed') ? 'selected' : '' ?>>Closed</option>
                        <option value="expired" <?= (isset($inquiry) && ($inquiry['status'] ?? '') == 'expired') ? 'selected' : '' ?>>Expired</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="form-check mt-2">
                        <input type="checkbox" name="is_featured" value="1" class="form-check-input" id="is_featured" <?= (isset($inquiry) && $inquiry['is_featured']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_featured">Featured Inquiry</label>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn" style="background: var(--primary-gradient); color: #fff;"><?= isset($inquiry) && $inquiry ? 'Update Inquiry' : 'Add Inquiry' ?></button>
                <a href="<?= base_url('dashboard/inquiries') ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
