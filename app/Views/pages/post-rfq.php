<?= $this->extend('layouts/inner') ?>

<?= $this->section('content') ?>
<section class="inner-banner-sec mt-4">
    <div class="container">
        <div class="inner-banner-img">
            <img src="<?= base_url('assets/images/buyers-single-banner.webp') ?>" class="w-100">
            <div class="inner-banner-sec-content">
                <h1>Post a Buy Offer / Inquiry</h1>
                <h2>Tell suppliers what you need</h2>
            </div>
        </div>
    </div>
</section>

<section class="mt-5 mb-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card" style="border-radius: 12px; box-shadow: 0 2px 15px rgba(0,0,0,0.08);">
                    <div class="card-body p-4 p-md-5">
                        <h3 style="color: #0d6968; text-align: center; margin-bottom: 25px;">Post a Buy Offer / Inquiry</h3>
                        
                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                        <?php endif; ?>
                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                        <?php endif; ?>

                        <form action="<?= base_url('contact/submit') ?>" method="POST" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            <input type="hidden" name="form_type" value="rfq">
                            <input type="hidden" name="source_page" value="post-rfq">
                            <input type="hidden" name="lead_type" value="buyer">

                            <div class="mb-3">
                                <label class="form-label fw-bold">Inquiry Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" placeholder="e.g. Looking for Cotton T-Shirts" required style="padding: 12px; border-radius: 8px;">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Requirement Details <span class="text-danger">*</span></label>
                                <textarea name="message" class="form-control" rows="5" placeholder="Describe what you're looking for in detail..." required style="padding: 12px; border-radius: 8px;"></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Quantity Required <span class="text-danger">*</span></label>
                                    <input type="text" name="quantity" class="form-control" placeholder="e.g. 1000 Pieces" style="padding: 12px; border-radius: 8px;" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                                    <select name="category" class="form-control" style="padding: 12px; border-radius: 8px;" required>
                                        <option value="">Select Category</option>
                                        <?php if (isset($categories)): ?>
                                            <?php foreach ($categories as $cat): ?>
                                                <option value="<?= $cat['id'] ?>"><?= esc($cat['name']) ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>

                            <hr class="my-4">
                            <h4 class="h5" style="color: #0d6968;">Your Contact Information</h4>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" placeholder="Your Full Name" required style="padding: 12px; border-radius: 8px;">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" placeholder="your@email.com" required style="padding: 12px; border-radius: 8px;">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Phone Number <span class="text-danger">*</span></label>
                                    <input type="tel" name="phone" class="form-control phone" placeholder="Phone with country code" style="padding: 12px; border-radius: 8px;" required>
                                    <div class="form-check mt-2">
                                        <input type="checkbox" class="form-check-input" name="whatsapp" id="rfq_whatsapp">
                                        <label class="form-check-label" for="rfq_whatsapp">Also available on WhatsApp</label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Country <span class="text-danger">*</span></label>
                                    <select name="country" class="form-control" style="padding: 12px; border-radius: 8px;">
                                        <option value="">Select Country</option>
                                        <?php if (isset($countries)): ?>
                                            <?php foreach ($countries as $c): ?>
                                                <option value="<?= $c['id'] ?>"><?= esc($c['name']) ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Attachment (Optional)</label>
                                <input type="file" name="attachment" class="form-control" accept="image/*,.pdf" style="padding: 12px; border-radius: 8px;">
                                <small class="text-muted">Upload reference image or specification document (Max 2MB)</small>
                            </div>

                            <div class="mt-4 text-center">
                                <button type="submit" class="btn" style="background: linear-gradient(135deg, #0d6968, #0F9EA5); color: #fff; padding: 14px 50px; border-radius: 30px; font-weight: 700; font-size: 16px;">Post Inquiry / RFQ</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
