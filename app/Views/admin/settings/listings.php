<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="page-header d-flex justify-content-between align-items-center">
    <h1 class="page-title">Site Settings</h1>
</div>

<ul class="nav nav-tabs mb-4">
    <li class="nav-item"><a class="nav-link <?= $activeTab === 'general' ? 'active' : '' ?>" href="<?= base_url('admin/settings/general') ?>">General</a></li>
    <li class="nav-item"><a class="nav-link <?= $activeTab === 'seo' ? 'active' : '' ?>" href="<?= base_url('admin/settings/seo') ?>">SEO</a></li>
    <li class="nav-item"><a class="nav-link <?= $activeTab === 'moderation' ? 'active' : '' ?>" href="<?= base_url('admin/settings/moderation') ?>">Content Moderation</a></li>
    <li class="nav-item"><a class="nav-link <?= $activeTab === 'categories' ? 'active' : '' ?>" href="<?= base_url('admin/settings/categories') ?>">Categories</a></li>
    <li class="nav-item"><a class="nav-link <?= $activeTab === 'listings' ? 'active' : '' ?>" href="<?= base_url('admin/settings/listings') ?>">Listings</a></li>
    <li class="nav-item"><a class="nav-link <?= $activeTab === 'top-sections' ? 'active' : '' ?>" href="<?= base_url('admin/settings/top-sections') ?>">Top Sections</a></li>
    <li class="nav-item"><a class="nav-link <?= $activeTab === 'hero-banners' ? 'active' : '' ?>" href="<?= base_url('admin/settings/hero-banners') ?>">Hero Banner</a></li>
    <li class="nav-item"><a class="nav-link <?= $activeTab === 'registration' ? 'active' : '' ?>" href="<?= base_url('admin/settings/registration') ?>">Registration</a></li>
    <li class="nav-item"><a class="nav-link <?= $activeTab === 'email' ? 'active' : '' ?>" href="<?= base_url('admin/settings/email') ?>">Email</a></li>
</ul>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show"><?= esc(session()->getFlashdata('success')) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show"><?= esc(session()->getFlashdata('error')) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<div class="card card-custom">
    <div class="card-header"><h5 class="mb-0">Listing Management</h5></div>
    <div class="card-body">
        <ul class="nav nav-pills mb-3" id="listingTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="products-tab" data-bs-toggle="pill" data-bs-target="#products" type="button" role="tab">Products (<?= count($products) ?>)</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="inquiries-tab" data-bs-toggle="pill" data-bs-target="#inquiries" type="button" role="tab">Inquiries (<?= count($inquiries) ?>)</button>
            </li>
        </ul>

        <div class="tab-content" id="listingTabsContent">
            <div class="tab-pane fade show active" id="products" role="tabpanel">
                <?php
                $pendingProductCount = count(array_filter($products, fn($p) => $p['status'] === 'pending'));
                $inactiveProductCount = count(array_filter($products, fn($p) => $p['status'] === 'inactive'));
                ?>
                <?php if ($pendingProductCount > 0): ?>
                <div class="alert alert-warning d-flex align-items-center gap-2 mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16" class="flex-shrink-0">
                        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                    </svg>
                    <div>
                        <strong><?= $pendingProductCount ?> product<?= $pendingProductCount > 1 ? 's' : '' ?> pending review.</strong>
                        These were submitted by suppliers and are awaiting your approval. Change their status to <strong>Active</strong> to publish them.
                        <?php if ($inactiveProductCount > 0): ?>
                        There <?= $inactiveProductCount > 1 ? 'are' : 'is' ?> also <?= $inactiveProductCount ?> inactive product<?= $inactiveProductCount > 1 ? 's' : '' ?>.
                        <?php endif; ?>
                    </div>
                </div>
                <?php elseif ($inactiveProductCount > 0): ?>
                <div class="alert alert-secondary d-flex align-items-center gap-2 mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16" class="flex-shrink-0">
                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                    </svg>
                    <div>
                        <?= $inactiveProductCount ?> inactive product<?= $inactiveProductCount > 1 ? 's' : '' ?> (hidden from the site).
                    </div>
                </div>
                <?php endif; ?>
                <div class="mb-3">
                    <input type="text" class="form-control" id="productSearch" placeholder="Search products..." onkeyup="filterTable('productSearch', 'productsTable')">
                </div>
                <?php
                    // Column-header sort links. A closure (not a named
                    // function) so re-rendering this view within the same
                    // request -- unlikely but not impossible -- can't hit a
                    // "cannot redeclare" fatal. $sort/$dir come from
                    // AdminSettings::listings(), already whitelisted there.
                    $productSort = $sort ?? 'created_at';
                    $productDir = $dir ?? 'desc';
                    // Every row-action form below posts here too, with the
                    // current sort carried in the query string -- without
                    // this, any toggle/status-change/delete POST would lose
                    // the admin's current column sort (the controller reads
                    // sort/dir from GET, and a POST has none unless the
                    // form's own action URL supplies it).
                    $listingsActionUrl = base_url('admin/settings/listings') . '?sort=' . urlencode($productSort) . '&dir=' . urlencode($productDir);
                    $productSortLink = function ($field, $label) use ($productSort, $productDir) {
                        $nextDir = ($productSort === $field && $productDir === 'asc') ? 'desc' : 'asc';
                        $arrow = $productSort === $field ? ($productDir === 'asc' ? '&#9650;' : '&#9660;') : '<span class="text-muted">&#8597;</span>';
                        $url = base_url('admin/settings/listings') . '?sort=' . urlencode($field) . '&dir=' . urlencode($nextDir);
                        return '<a href="' . esc($url, 'attr') . '" class="text-decoration-none text-dark d-inline-flex align-items-center gap-1">'
                            . esc($label) . ' ' . $arrow . '</a>';
                    };
                ?>
                <div class="table-responsive">
                    <table class="table table-custom" id="productsTable">
                        <thead>
                            <tr>
                                <th><?= $productSortLink('id', 'ID') ?></th>
                                <th>Image</th>
                                <th><?= $productSortLink('name', 'Product Name') ?></th>
                                <th><?= $productSortLink('supplier_name', 'Supplier') ?></th>
                                <th><?= $productSortLink('category_name', 'Category') ?></th>
                                <th><?= $productSortLink('status', 'Status') ?></th>
                                <th><?= $productSortLink('is_featured', 'Featured') ?></th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($products)): ?>
                                <?php foreach ($products as $p): ?>
                                    <tr>
                                        <td><?= esc($p['id']) ?></td>
                                        <td>
                                            <?php if (!empty($p['main_image'])): ?>
                                                <img src="<?= base_url('uploads/products/' . $p['main_image']) ?>" alt="" style="width:50px;height:50px;object-fit:cover;border-radius:5px;">
                                            <?php else: ?>
                                                <span class="text-muted">No image</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= esc($p['name']) ?></td>
                                        <td><?= esc($p['supplier_name']) ?></td>
                                        <td><?= esc($p['category_name']) ?></td>
                                        <td>
                                            <form method="post" action="<?= esc($listingsActionUrl, 'attr') ?>" class="d-inline">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="action" value="update_product_status">
                                                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                                <select name="status" class="form-select form-select-sm" style="width:auto;display:inline-block;" onchange="this.form.submit()">
                                                    <option value="active" <?= $p['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                                    <option value="pending" <?= $p['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                                    <option value="inactive" <?= $p['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-1">
                                                <form method="post" action="<?= esc($listingsActionUrl, 'attr') ?>" class="d-inline">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="action" value="toggle_featured_product">
                                                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                                    <button type="submit" class="btn btn-sm <?= $p['is_featured'] ? 'btn-warning' : 'btn-outline-secondary' ?>"><?= $p['is_featured'] ? '★' : '☆' ?></button>
                                                </form>
                                                <?php if ($p['is_featured']): ?>
                                                <form method="post" action="<?= esc($listingsActionUrl, 'attr') ?>" class="d-inline">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="action" value="set_product_featured_set">
                                                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                                    <select name="featured_set" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()" title="Which carousel set this product is pinned into (max 3 pinned products per set)">
                                                        <?php for ($set = 1; $set <= $productSetCount; $set++): ?>
                                                        <option value="<?= $set ?>" <?= (int) ($p['featured_set'] ?? 1) === $set ? 'selected' : '' ?>>Set <?= $set ?></option>
                                                        <?php endfor; ?>
                                                    </select>
                                                </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <form method="post" action="<?= esc($listingsActionUrl, 'attr') ?>" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this product?')">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="action" value="delete_product">
                                                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="8" class="text-center text-muted">No products found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="inquiries" role="tabpanel">
                <div class="mb-3">
                    <input type="text" class="form-control" id="inquirySearch" placeholder="Search inquiries..." onkeyup="filterTable('inquirySearch', 'inquiriesTable')">
                </div>
                <div class="table-responsive">
                    <table class="table table-custom" id="inquiriesTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Buyer</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Featured</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($inquiries)): ?>
                                <?php foreach ($inquiries as $inq): ?>
                                    <tr>
                                        <td><?= esc($inq['id']) ?></td>
                                        <td><?= esc($inq['title']) ?></td>
                                        <td><?= esc($inq['buyer_name']) ?></td>
                                        <td><?= esc($inq['category_name']) ?></td>
                                        <td>
                                            <form method="post" action="<?= esc($listingsActionUrl, 'attr') ?>" class="d-inline">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="action" value="update_inquiry_status">
                                                <input type="hidden" name="id" value="<?= $inq['id'] ?>">
                                                <select name="status" class="form-select form-select-sm" style="width:auto;display:inline-block;" onchange="this.form.submit()">
                                                    <option value="active" <?= $inq['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                                    <option value="inactive" <?= $inq['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td>
                                            <form method="post" action="<?= esc($listingsActionUrl, 'attr') ?>" class="d-inline">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="action" value="toggle_featured_inquiry">
                                                <input type="hidden" name="id" value="<?= $inq['id'] ?>">
                                                <button type="submit" class="btn btn-sm <?= $inq['is_featured'] ? 'btn-warning' : 'btn-outline-secondary' ?>"><?= $inq['is_featured'] ? '★' : '☆' ?></button>
                                            </form>
                                        </td>
                                        <td>
                                            <form method="post" action="<?= esc($listingsActionUrl, 'attr') ?>" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this inquiry?')">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="action" value="delete_inquiry">
                                                <input type="hidden" name="id" value="<?= $inq['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="7" class="text-center text-muted">No inquiries found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function filterTable(inputId, tableId) {
    var input = document.getElementById(inputId);
    var filter = input.value.toLowerCase();
    var table = document.getElementById(tableId);
    var rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    for (var i = 0; i < rows.length; i++) {
        var text = rows[i].textContent.toLowerCase();
        rows[i].style.display = text.indexOf(filter) > -1 ? '' : 'none';
    }
}
</script>
<?= $this->endSection() ?>
