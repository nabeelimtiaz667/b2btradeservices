<?= $this->extend('layouts/inner') ?>

<?= $this->section('content') ?>
<section class="inner-banner-sec mt-4">
    <div class="container">
        <div class="inner-banner-img">
            <img src="<?= base_url('assets/images/buyers-single-banner.webp') ?>" class="w-100">
            <div class="inner-banner-sec-content">
                <h1>Find B2B Buying Leads</h1>
                <h2>For Your Business</h2>
            </div>
        </div>
    </div>
</section>

<section class="buyers-profile-details mt-5">
    <div class="container">
        <div class="row">
            <div class="buyers-left-content">
                <h3 class="font-weight-600"><?= esc($inquiry['title'] ?? 'Buyer Inquiry Details') ?></h3>
                <div class="product-quick-details-list mt-4">
                    <div class="d-flex">
                        <p><b>Purchaser:</b></p>
                        <?php if (session()->get('user_type') === 'admin'): ?>
                            <p><?= esc($inquiry['buyer_name'] ?? 'N/A') ?></p>
                        <?php else: ?>
                            <p><span style="color:#999; font-style:italic;"><i class="fas fa-lock"></i> Premium Members only</span></p>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex">
                        <p><b>Country/Region:</b></p>
                        <p class="d-flex gap-2 align-items-center"><?= esc($inquiry['country']['name'] ?? 'N/A') ?>
                        <?php if (!empty($inquiry['country']['name'] ?? '')): ?>
                            <img src="<?= base_url('assets/images/flags/flag_' . str_replace(' ', '_', $inquiry['country']['name']) . '.svg') ?>" width="20" onerror="this.style.display='none'">
                        <?php endif; ?>
                        </p>
                    </div>
                    <div class="d-flex">
                        <p><b>Contact Number:</b></p>
                        <?php if (session()->get('user_type') === 'admin'): ?>
                            <p><a href="tel:<?= esc($inquiry['buyer_phone'] ?? '') ?>"><?= esc($inquiry['buyer_phone'] ?? 'N/A') ?></a></p>
                        <?php else: ?>
                            <p><span style="color:#999; font-style:italic;"><i class="fas fa-lock"></i> Premium Members only</span></p>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex">
                        <p><b>Company Name:</b></p>
                        <?php if (session()->get('user_type') === 'admin'): ?>
                            <p><?= esc($inquiry['buyer_company'] ?? 'N/A') ?></p>
                        <?php else: ?>
                            <p><span style="color:#999; font-style:italic;"><i class="fas fa-lock"></i> Premium Members only</span></p>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex">
                        <p><b>Date Posted:</b></p>
                        <p><?= isset($inquiry['inquiry_date']) ? date('d M, Y', strtotime($inquiry['inquiry_date'])) : date('d M, Y', strtotime($inquiry['created_at'])) ?></p>
                    </div>
                </div>
                <div class="d-flex gap-5 mt-4">
                    <div>
                        <p class="f-12 dark-green-text font-weight-600">Quantity Required</p>
                        <p class="f-12"><?= esc($inquiry['quantity'] ?? 'N/A') ?> <?= esc($inquiry['unit'] ?? '') ?></p>
                    </div>
                    <div>
                        <p class="f-12 dark-green-text font-weight-600 mb-2">Buying Frequency</p>
                        <p class="f-12"><?= esc($inquiry['shipping_terms'] ?? 'Monthly') ?></p>
                    </div>
                </div>
            </div>
            <div class="buyers-right-content">
                <div class="buy-offer-details">
                    <p class="f-16 light-green-h2-color font-weight-600">Buy offer Details</p>
                    <p><?= nl2br(esc($inquiry['description'] ?? 'No description provided.')) ?></p>
                    <?php if (!empty($inquiry['attachment'])): ?>
                        <div class="mt-3 mb-3">
                            <img src="<?= base_url('uploads/inquiries/' . $inquiry['attachment']) ?>" alt="Reference Image" style="max-width: 100%; max-height: 300px; object-fit: cover; border-radius: 8px; cursor: pointer;" onclick="window.open(this.src, '_blank')">
                        </div>
                    <?php endif; ?>
                    <div class="dual-btn">
                        <button type="button" class="btn solid-btn contact-buyer-cta" data-bs-toggle="modal" data-bs-target="#contactBuyerModal">Contact Buyer</button>
                        <a href="<?= base_url('register') ?>" class="btn outline-btn">Submit a Similar Request</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="mt-5">
    <div class="container">
        <h2 class="text-center">Related Buy Offers</h2>
        <div class="realted-boxes-sec">
            <?php if (isset($relatedInquiries) && count($relatedInquiries) > 0): ?>
                <?php foreach ($relatedInquiries as $related): ?>
                    <?php if ($related['id'] != $inquiry['id']): ?>
                        <div class="buyers-related-offer-box">
                            <div class="d-flex gap-3 align-items-center justify-content-between">
                                <div class="buyers-related-offer-box-content">
                                    <p class="f-16 dark-green-text font-weight-600"><?= esc($related['title']) ?></p>
                                    <p><?= esc(substr($related['description'] ?? '', 0, 200)) ?></p>
                                    <div class="flag-part">
                                        <p class="d-flex align-items-center gap-3">
                                            <?= isset($related['country']) ? esc($related['country']['name']) : 'N/A' ?>
                                            <?php if (isset($related['country'])): ?>
                                                <img src="<?= base_url('assets/images/flags/flag_' . str_replace(' ', '_', $related['country']['name']) . '.svg') ?>" width="20" onerror="this.style.display='none'">
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="buyers-related-offer-box-cta">
                                    <a href="<?= inquiry_url($related) ?>" class="solid-btn">View Details</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center p-4">
                    <p>No related buy offers found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="success-stories-sec mt-md-5 mt-2 pt-lg-4">
    <div class="container">
        <div class="row align-items-end">
            <div class="multiple-quote-form">
                <h2 class="text-white text-center">Find us</h2>
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success text-center"><?= session()->getFlashdata('success') ?></div>
                <?php endif; ?>
                <form action="<?= base_url('contact/submit') ?>" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="form_type" value="contact">
                    <input type="hidden" name="source_page" value="buyer-detail">
                    <input type="hidden" name="source_id" value="<?= $inquiry['id'] ?? '' ?>">
                    <input type="hidden" name="lead_type" value="supplier">
                    <div class="form-input">
                        <input type="text" name="name" placeholder="Name*" required>
                    </div>
                    <div class="form-input">
                            <input type="email" name="email" placeholder="Email*" required>
                        </div>
                        <div class="form-input mb-10">
                            <input type="tel" name="phone" class="phone" placeholder="Phone*" required>
                        </div>
                    
                    <div class="dual-input">
                        <div class="form-input">
                            <input type="text" name="industry" placeholder="Select Industry*" required>
                        </div>
                        <div class="form-input">
                            <input type="number" name="quantity" placeholder="Quantity*" required>
                        </div>
                    </div>
                    <div class="form-textarea">
                        <textarea name="message" placeholder="What are you looking for?"></textarea>
                    </div>
                    <div class="submit-btn">
                        <button type="submit">Submit Now</button>
                    </div>
                </form>
            </div>
            <div class="success-stories-col">
                <div class="d-flex heading align-items-center justify-content-between">
                    <h2 class="mb-0">Success Stories</h2>
                    <a href="#" class="view-all-link d-flex align-items-center gap-2">View All <img src="<?= base_url('assets/images/arrow-right.svg') ?>"></a>
                </div>
                <div class="success-stories-slider-sec">
                    <div class="success-stories-slider">
                        <div>
                            <p>B2B Trade Services helped us find reliable suppliers from South Asia within days. The platform's lead management system made it easy to track and communicate with multiple vendors simultaneously.</p>
                            <p class="client-name mb-1">Client Name</p>
                            <p class="success-stories-compay-name mb-0">Company</p>
                        </div>
                        <div>
                            <p>As a buyer, I was able to post my requirements and receive quotes from verified suppliers across multiple regions. The process was smooth and the quality of leads exceeded our expectations.</p>
                            <p class="client-name mb-1">Client Name</p>
                            <p class="success-stories-compay-name mb-0">Company</p>
                        </div>
                        <div>
                            <p>The B2B marketplace connected us with premium suppliers we wouldn't have found otherwise. Their verification process gives us confidence in every transaction we make through the platform.</p>
                            <p class="client-name mb-1">Client Name</p>
                            <p class="success-stories-compay-name mb-0">Company</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="text-center mt-5 mb-5 pt-md-4 pb-md-4">
    <div class="container">
        <h2>The World's Leading Global B2B <br> Trading Platform</h2>
        <p>B2B Trade Services LLC is a global B2B marketplace with an extensive experience in wholesale trade solution. The platform connects verified buyers and suppliers worldwide, simplifying bulk sourcing through innovative e-commerce solutions. With intuitive web and mobile experiences, dedicated support, and trusted traders, B2B Trade Services delivers a seamless, reliable, and efficient global trading experience.</p>
        <a href="<?= base_url('about-us') ?>" class="read_more_link">Read More</a>
    </div>
</section>

<div class="modal fade" id="contactBuyerModal" tabindex="-1" aria-labelledby="contactBuyerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title light-green-h2-color font-weight-600" id="contactBuyerModalLabel">Contact Buyer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                
                <div id="popupSuccessMsg" class="alert alert-success text-center" style="display: none;">
                    Your information has been sent to the buyer.
                </div>

                <form id="popupContactForm" class="popupContactForms">
                    <div class="mb-3 form-input">
                        <input type="text" class="form-control" name="name" placeholder="Name*" required style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
                    </div>
                    <div class="mb-3 form-input">
                        <input type="email" class="form-control" name="email" placeholder="Email*" required style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
                    </div>
                    <div class="mb-3 form-input">
                        <input type="tel" class="form-control" name="phone" placeholder="Phone*" required style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
                    </div>
                    <div class="mb-3">
                        <select class="form-control country-select" name="country_id" required style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
                            <option value="">Select Country*</option>
                            <?php foreach ($countries as $country): ?>
                                <option value="<?= $country['id'] ?>"><?= esc($country['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4 form-textarea">
                        <textarea class="form-control" name="message" rows="4" placeholder="Your Message"  style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;"></textarea>
                    </div>
                    <div class="submit-btn text-center">
                        <button type="submit" class="btn solid-btn contact-buyer-cta" style="border: none; cursor: pointer;">Send Information</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('popupContactForm');
    const successMsg = document.getElementById('popupSuccessMsg');
    
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevents the page from reloading
            
            // 1. Show the success message
            successMsg.style.display = 'block';
            
            // 2. Reset the form fields to blank
            contactForm.reset();
            
            // 3. Wait 2 seconds, then close the modal and hide the message
            setTimeout(() => {
                // Find the active modal instance and close it
                const modalElement = document.getElementById('contactBuyerModal');
                const modalInstance = bootstrap.Modal.getInstance(modalElement);
                
                if (modalInstance) {
                    modalInstance.hide();
                }
                
                // Hide the message again so it's fresh for the next time it's opened
                successMsg.style.display = 'none';
            }, 2000); 
        });
    }
});
</script>

<?= $this->endSection() ?>