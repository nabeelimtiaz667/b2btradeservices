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

<div class="card card-custom mb-4">
    <div class="card-header"><h5 class="mb-0">Add New Slide</h5></div>
    <div class="card-body">
        <p class="text-muted">
            Images should be landscape, roughly banner-shaped -- not a mobile screenshot
            (portrait) and not a widescreen/16:9 image. At least
            <strong><?= $minWidth ?>&times;<?= $minHeight ?>px</strong> so it doesn't look
            pixelated once displayed, and under <strong><?= $maxFileSizeMb ?>MB</strong>.
            No limit on how many slides you can add; all active slides rotate on the
            homepage banner in the order shown below.
        </p>
        <form method="post" action="<?= base_url('admin/settings/hero-banners') ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="add">
            <div class="row">
                <div class="col-md-5 mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="input_type" value="upload" id="addTypeUpload" checked
                            onchange="heroToggleInputType('add')">
                        <label class="form-check-label" for="addTypeUpload">Upload a file (min <?= $minWidth ?>&times;<?= $minHeight ?>px, landscape)</label>
                    </div>
                    <input type="file" name="image" id="addImageFile" class="form-control mt-1" accept="image/jpeg,image/png,image/webp,image/gif">

                    <div class="form-check mt-2">
                        <input class="form-check-input" type="radio" name="input_type" value="url" id="addTypeUrl"
                            onchange="heroToggleInputType('add')">
                        <label class="form-check-label" for="addTypeUrl">Use an image URL instead</label>
                    </div>
                    <input type="text" name="image_url" id="addImageUrl" class="form-control mt-1" placeholder="https://..." disabled>
                </div>
                <div class="col-md-5 mb-3">
                    <label class="form-label">Link</label>
                    <input type="text" name="link_url" class="form-control" placeholder="e.g. premium-services or https://..." required>
                </div>
                <div class="col-md-2 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Add Slide</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card card-custom mb-4">
    <div class="card-header"><h5 class="mb-0">Active Slides (<?= count($activeSlides) ?>)</h5></div>
    <div class="card-body">
        <?php if (!empty($activeSlides)): ?>
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th style="width:40px;"></th>
                        <th>Order</th>
                        <th>Image</th>
                        <th>Link</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="hero-sortable-body">
                    <?php foreach ($activeSlides as $slide): ?>
                    <tr id="hero-row-<?= $slide['id'] ?>" data-id="<?= $slide['id'] ?>" draggable="true">
                        <td class="hero-drag-handle" style="cursor:grab;font-size:18px;color:#999;" title="Drag to reorder">&#9776;</td>
                        <td class="hero-order-cell"><?= esc($slide['sort_order']) ?></td>
                        <td><img src="<?= esc($slide['file_type'] === 'url' ? $slide['image_filename'] : base_url('uploads/hero-banner/' . $slide['image_filename']), 'attr') ?>" style="width:120px;height:auto;border-radius:5px;"></td>
                        <td><?= esc($slide['link_url']) ?></td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="showHeroEditForm(<?= $slide['id'] ?>)">Edit</button>
                            <form method="post" action="<?= base_url('admin/settings/hero-banners') ?>" class="d-inline" onsubmit="return confirm('Move this slide to history? It will stop showing on the homepage until restored.')">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="remove_to_history">
                                <input type="hidden" name="id" value="<?= $slide['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-secondary">Move to History</button>
                            </form>
                        </td>
                    </tr>
                    <tr id="hero-edit-<?= $slide['id'] ?>" data-edit-for="<?= $slide['id'] ?>" style="display:none;">
                        <td colspan="5">
                            <form method="post" action="<?= base_url('admin/settings/hero-banners') ?>" enctype="multipart/form-data"
                                class="row g-2 align-items-end" onsubmit="return confirm('Save changes to this slide?')">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="id" value="<?= $slide['id'] ?>">
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="input_type" value="upload" id="editTypeUpload<?= $slide['id'] ?>"
                                            <?= $slide['file_type'] === 'upload' ? 'checked' : '' ?>
                                            onchange="heroToggleInputType('edit<?= $slide['id'] ?>')">
                                        <label class="form-check-label" for="editTypeUpload<?= $slide['id'] ?>">Replace with a file (optional, min <?= $minWidth ?>&times;<?= $minHeight ?>px)</label>
                                    </div>
                                    <input type="file" name="image" id="editImageFile<?= $slide['id'] ?>" class="form-control mt-1" accept="image/jpeg,image/png,image/webp,image/gif"
                                        <?= $slide['file_type'] === 'url' ? 'disabled' : '' ?>>

                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="radio" name="input_type" value="url" id="editTypeUrl<?= $slide['id'] ?>"
                                            <?= $slide['file_type'] === 'url' ? 'checked' : '' ?>
                                            onchange="heroToggleInputType('edit<?= $slide['id'] ?>')">
                                        <label class="form-check-label" for="editTypeUrl<?= $slide['id'] ?>">Replace with a URL instead</label>
                                    </div>
                                    <input type="text" name="image_url" id="editImageUrl<?= $slide['id'] ?>" class="form-control mt-1"
                                        placeholder="https://..." value="<?= $slide['file_type'] === 'url' ? esc($slide['image_filename']) : '' ?>"
                                        <?= $slide['file_type'] === 'upload' ? 'disabled' : '' ?>>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">Link</label>
                                    <input type="text" name="link_url" class="form-control" value="<?= esc($slide['link_url']) ?>" required>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-success btn-sm">Save</button>
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="hideHeroEditForm(<?= $slide['id'] ?>)">Cancel</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="text-muted small mb-2">Drag rows by the &#9776; handle to reorder, then click Save Order.</p>
        <form method="post" action="<?= base_url('admin/settings/hero-banners') ?>" id="hero-reorder-form">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="reorder">
            <input type="hidden" name="order" id="hero-order-input">
            <button type="submit" class="btn btn-sm btn-primary" id="hero-save-order-btn" disabled>Save Order</button>
        </form>
        <?php else: ?>
        <p class="text-muted text-center mb-0">No active slides -- the homepage banner will show nothing until at least one is added.</p>
        <?php endif; ?>
    </div>
</div>

<div class="card card-custom">
    <div class="card-header"><h5 class="mb-0">History (<?= count($historySlides) ?>)</h5></div>
    <div class="card-body">
        <?php if (!empty($historySlides)): ?>
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Link</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($historySlides as $slide): ?>
                    <tr>
                        <td><img src="<?= esc($slide['file_type'] === 'url' ? $slide['image_filename'] : base_url('uploads/hero-banner/' . $slide['image_filename']), 'attr') ?>" style="width:120px;height:auto;border-radius:5px;"></td>
                        <td><?= esc($slide['link_url']) ?></td>
                        <td>
                            <form method="post" action="<?= base_url('admin/settings/hero-banners') ?>" class="d-inline">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="restore">
                                <input type="hidden" name="id" value="<?= $slide['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-primary">Restore</button>
                            </form>
                            <form method="post" action="<?= base_url('admin/settings/hero-banners') ?>" class="d-inline" onsubmit="return confirm('Permanently delete this slide? This also deletes the image file and cannot be undone.')">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="delete_permanent">
                                <input type="hidden" name="id" value="<?= $slide['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete Permanently</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p class="text-muted text-center mb-0">No slides in history.</p>
        <?php endif; ?>
    </div>
</div>

<script>
function showHeroEditForm(id) {
    document.getElementById('hero-row-' + id).style.display = 'none';
    document.getElementById('hero-edit-' + id).style.display = '';
}
function hideHeroEditForm(id) {
    document.getElementById('hero-row-' + id).style.display = '';
    document.getElementById('hero-edit-' + id).style.display = 'none';
}

// Mutually-exclusive file-upload vs image-URL radios (Add form uses prefix
// "add", each row's Edit form uses "edit{id}"). The inactive input is
// disabled, not just visually ignored -- disabled fields are excluded from
// form submission entirely, so only the selected option's data ever reaches
// the server, regardless of what's still typed/chosen in the other one.
function heroToggleInputType(prefix) {
    const useUpload = document.getElementById(prefix + 'TypeUpload').checked;
    document.getElementById(prefix + 'ImageFile').disabled = !useUpload;
    document.getElementById(prefix + 'ImageUrl').disabled = useUpload;
}

(function () {
    // Native HTML5 drag-and-drop, no extra library needed for one table --
    // each visible row (hero-row-N) has a hidden edit row (hero-edit-N)
    // immediately after it that must move along as a pair, since the edit
    // form's Cancel button assumes that adjacency. Reordering only rewrites
    // the DOM (instant visual feedback); nothing is saved until "Save
    // Order" is clicked, which is a normal full-page POST like every other
    // action on this page -- deliberately not AJAX, to avoid this
    // project's CSRF-token-per-request lifecycle for a feature that isn't
    // worth that complexity.
    const tbody = document.getElementById('hero-sortable-body');
    if (!tbody) {
        return;
    }
    const saveBtn = document.getElementById('hero-save-order-btn');
    let draggedId = null;

    function rowPair(id) {
        return [document.getElementById('hero-row-' + id), document.getElementById('hero-edit-' + id)];
    }

    tbody.querySelectorAll('tr[draggable="true"]').forEach(function (row) {
        row.addEventListener('dragstart', function (e) {
            draggedId = row.dataset.id;
            e.dataTransfer.effectAllowed = 'move';
            row.classList.add('opacity-50');
        });
        row.addEventListener('dragend', function () {
            row.classList.remove('opacity-50');
        });
        row.addEventListener('dragover', function (e) {
            e.preventDefault();
            if (!draggedId || draggedId === row.dataset.id) {
                return;
            }
            const rect = row.getBoundingClientRect();
            const insertAfter = (e.clientY - rect.top) > rect.height / 2;
            const [draggedRow, draggedEdit] = rowPair(draggedId);
            const targetEdit = document.getElementById('hero-edit-' + row.dataset.id);
            if (insertAfter) {
                targetEdit.after(draggedRow, draggedEdit);
            } else {
                row.before(draggedRow, draggedEdit);
            }
        });
        row.addEventListener('drop', function (e) {
            e.preventDefault();
        });
    });

    tbody.addEventListener('dragend', function () {
        draggedId = null;
        saveBtn.disabled = false;
        Array.from(tbody.querySelectorAll('tr[data-id]')).forEach(function (row, i) {
            row.querySelector('.hero-order-cell').textContent = i + 1;
        });
    });

    document.getElementById('hero-reorder-form').addEventListener('submit', function () {
        const ids = Array.from(tbody.querySelectorAll('tr[data-id]')).map(function (row) {
            return row.dataset.id;
        });
        document.getElementById('hero-order-input').value = ids.join(',');
    });
})();
</script>
<?= $this->endSection() ?>
