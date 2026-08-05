<?= $this->extend('layouts/inner-pkg') ?>

<?= $this->section('content') ?>

<!-- Hero section with package info + registration form -->
<section class="mt-lg-3 silver-bg">
    <div class="container">
        <div class="row">
            <div class="latest-buy-offer bg-pkg-transparent">
                <div class="verified-badge-pkg">
                                            <div class="d-flex align-items-center gap-2">
                            <img src="<?= base_url('assets/images/verified-badge.svg') ?>">
                            <div>
                                <p class="mb-0 f-12"><b>Verified B2B Excellence </b> </p>
                            </div>
                        </div>
                </div>
                <h1 class="h2">STARTER</h1>
                <h2 class="h4"><b>$499<span>/Year</span></b></h2>
                <p><b>Perfect for: Startups, New Exporters and Small Trading Companies looking for International Buyers and structured support.</b> </p>
                <p>The Starter plan gives you access to essential B2B tools, Access to worldwide Database of Registered Importers and Exporters, and advisory support to streamline operations and improve performance without heavy investments.</p>
             <div class="row double-icon-pkg">
                    <div class="col-md-6 mb-md-0 mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <img src="<?= base_url('assets/images/database-img.svg') ?>">
                            <div>
                                <p class="mb-0"><b>Direct Connect</b><br>Buyers Database Access</p>
                            </div>
                        </div>
                    </div>
                     <div class="col-md-6">
                        <div class="d-flex align-items-center gap-2">
                            <img src="<?= base_url('assets/images/seo-img.svg') ?>">
                            <div>
                                <p class="mb-0"><b>Fast Steo</b><br>Dedicated Online Store</p>
                            </div>
                        </div>
                    </div>
             </div>
                <div class="price-box-content price-box-content-silver-ul mt-4">
                    <p><span><img src="<?= base_url('assets/images/check-all.svg') ?>"></span><span>Official Company Profile Page on our B2B Platform</span></p>
                    <p><span><img src="<?= base_url('assets/images/check-all.svg') ?>"></span><span>Online Store </span></p>
                    <p><span><img src="<?= base_url('assets/images/check-all.svg') ?>"></span><span>10 Show Case Products (Front Display Products)</span></p>
                    <p><span><img src="<?= base_url('assets/images/check-all.svg') ?>"></span><span>Buyers Database Access</span></p>
                    <p><span><img src="<?= base_url('assets/images/check-all.svg') ?>"></span><span>Customer Support Executive</span></p>
                </div>
                <a class="Download_pdf text-start mt-4   new-brochure-link" target="_blank" href="<?= base_url('assets/media/b2b-trade-services-corporate-profile.pdf') ?>"><img src="<?= base_url('assets/images/arrow-right-circle-fill.svg') ?>">View Brochures</a>
             
            </div>
            <div class="b2b-top-form single-view-pkg-form">
                <div class="view-pkg-img">
                    <img src="<?= base_url('assets/images/starter-coin.webp') ?>" class="w-100">
                </div>
                <h2 class=" ">Become a Premium
Member Now and get 10x
times more Buyers</h2>
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                <?php endif; ?>
                <form action="<?= base_url('contact/submit') ?>" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="form_type" value="package_inquiry">
                    <input type="hidden" name="source_page" value="silver-package">
                    <div class="filter-group">
                        <label class="radio-label">
                            <input type="radio" name="industry" value="supplier" checked>
                            <span class="radio"></span>
                            Supplier
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="industry" value="buyer">
                            <span class="radio"></span>
                            Buyer
                        </label>
                    </div>
                    <div class="form-input mt-3">
                        <input type="text" name="name" placeholder="Name*" required>
                    </div>
                    <div class="dual-input">
                        <div class="form-input">
                            <input type="email" name="email" placeholder="Email*" required>
                        </div>
                        <div class="form-input mb-10">
                            <input type="tel" name="phone" class="phone" placeholder="Phone number*" required>
                        </div>
                    </div>
                    <div class="form-input">
                        <input type="text" name="company" placeholder="Company Name*" required>
                    </div>
                    <div class="radio-join d-flex gap-2 align-items-start">
                        <input type="checkbox" name="agree" id="agree-silver" required><label for="agree-silver"><span style="color: #0F9EA5;">*</span>By joining, I agree to terms of use, privacy policy, IPR and agree to receive emails related to our services.</label>
                    </div>
                    <div class="submit-btn-gradient mt-3">
                        <button type="submit" class="gradeint-cta">Join Now</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Product Visibility section -->
<section class="mt-5">
    <div class="container">
        <div class="row align-items-center text-center text-md-start">
            <div class="col-md-6">
                <div class="about-us-box-img">
                    <img src="<?= base_url('assets/images/laptop-pkg.webp') ?>" class="w-100">
                </div>
            </div>
            <div class="col-md-6">
                <h2>Official  Company Profile Page:</h2>
                <p class="mt-3">B2B TRADE SERVICES will design an eye-catching official company Starter profile page for your business which will help your buyers to directly get to know more about your company size, products, history and validation.</p>
                <!--<ul class="checkbox-ul-new premeium-ul">-->
                <!--    <li><h5 class="f-16 mb-1"><b>Product Showcase:</b></h5>With our Silver Package, you can enjoy showcasing up to 10 products of your own choice.</li>-->
                <!--    <li><h5 class="f-16 mb-1"><b>Product Postings:</b></h5>Enjoy 50 product postings and get more exposure in the International Trade Market.</li>-->
                <!--    <li><h5 class="f-16 mb-1"><b>B2B Shop Management:</b></h5>We will allow you to not only build an online shop on B2BTradeServices.com but to enable you to maximize your brand & product exposure in 200+ countries.</li>-->
                <!--</ul>-->
            </div>
        </div>
    </div>
</section>

<!-- Featured Supplier & Buyer Access section -->
<section class="about-us-intro mt-5">
    <div class="container">
        <div class="row mt-5 align-items-center row-rev-mob">
            <div class="col-md-7">
                <h2>Online Store With 10 Showcase Products:</h2>
                <p>B2B TRADE SERVICES will design a separate Products Landing Page with extended URL domain name linked to your company like:(www.B2Btradeservices.com/your-company-name), which you can share with all your buyers and importers confidently and increase your products optimization and trust over buyers. Starter Members can post up to 10 Products to be displayed at Front Display.</p>
                <!--<ul class="checkbox-ul-new premeium-ul mt-3">-->
                <!--    <li><h5 class="f-16 mb-1"><b>Complete Access to Buyer Database:</b></h5>You can enjoy unlimited access to buyer's database from more than 250 <br class="br_hide"> countries across the globe.</li>-->
                <!--    <li><h5 class="f-16 mb-1"><b>Filtered Inquiries:</b></h5>Get genuine and filtered buyer inquiries and convert them into <br class="br_hide"> revenue-generating orders yourself.</li>-->
                <!--</ul>-->
            </div>
            <div class="col-md-5">
                <div class="about-us-box-img">
                    <img src="<?= base_url('assets/images/gold-col1.png') ?>" class="w-100">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Customer Service section -->
<section class="mt-5 mb-5 pb-md-5 pt-md-4">
    <div class="container">
        <div class="bg-profile-contact">
            <div class="row">
                <div class="profile-contact-info-bottom profile-contact-info-bottom-view-pkg mt-md-4">
                    <h2 class="f-18">Buyers Database Access</h2>
                    <p>In this Starter Membership Package, you will be able to connect with as many buyers as you want via our platform. No limit to daily messages, hassle free communication and quick conversation options with run-time online active buyers</p>
                    <!--<ul class="checkbox-ul-new premeium-ul mt-3">-->
                    <!--    <li><h5 class="f-16 mb-1"><b>Ready to Help:</b></h5>We are always available for our premium clients. We ensure that all of their inquiries are responded effectively</li>-->
                    <!--    <li><h5 class="f-16 mb-1"><b>Trade Advice and Consultancy:</b></h5>Our experienced staff is always there for you. If you need any trade-related advice, we have got your back.</li>-->
                    <!--</ul>-->
                </div>
            </div>
        </div>
    </div>
</section>

<!-- World's Leading B2B section -->
<section class="text-center mt-5 mb-5 pt-md-4 pb-md-4">
    <div class="container">
        <h2>The World's Leading Global B2B <br> Trading Platform</h2>
        <p>B2B Trade Services LLC is a global B2B marketplace with an extensive experience in wholesale trade solution. The platform connects verified buyers and suppliers worldwide, simplifying bulk sourcing through innovative e-commerce solutions. With intuitive web and mobile experiences, dedicated support, and trusted traders, B2B Trade Services delivers a seamless, reliable, and efficient global trading experience.</p>
        <a href="<?= base_url('about-us') ?>" class="read_more_link">Read More</a>
    </div>
</section>

<?= $this->endSection() ?>
