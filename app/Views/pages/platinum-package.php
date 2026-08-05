<?= $this->extend('layouts/inner-pkg') ?>

<?= $this->section('content') ?>

<!-- Hero section with package info + registration form -->
<style>
 
.premeium-ul li {
    margin-bottom: 0px !important;
    background-size: 13px !important;
}
.single-view-pkg-form {
    position: relative;
    align-self: flex-start;
}
.silver-bg:before{
    content:'';
    width: 100%;
    height: 100%;
    display: block;
    position: absolute;
    top: 0;
    z-index:-1;
    opacity: 0.5;
     background-image: linear-gradient(rgb(236, 111, 159) 0%, rgb(142, 94, 212) 50%, rgb(63, 110, 219) 100%);
}
    .verified-badge-pkg {
    border: 1px solid #000000;
    background: #3f6edb24;
   
}
.single-view-pkg-form {
    background: #3f6edb24;
}
.silver-bg .latest-buy-offer h4 span {
    color: #0F1626;
 
}
</style>
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
                <h1 class="h2">Platinum Package</h1>
                <h2 class="h4"><b>$3,999<span>/Year</span></b></h2>
                <p>Strategic Partnership Level</p>
                <p><b>Perfect for: Established businesses seeking strategic alignment and operational excellence.</b></p>
                <p>Platinum Membership is built for serious exporters and growth-focused businesses ready to expand aggressively in global markets. It offers priority access to verified bulk buyers, advanced market intelligence, and dedicated strategic support that ensures faster conversions, higher-value deals, and stronger international positioning.</p>
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
                <div class="price-box-content price-box-content-silver-ul  mt-4">
                    <p><span><img src="<?= base_url('assets/images/check-all.svg') ?>"></span><span>Official Company profile Page on our B2B Platform</span></p>
                    <p><span><img src="<?= base_url('assets/images/check-all.svg') ?>"></span><span>Online Store</span></p>
                    <p><span><img src="<?= base_url('assets/images/check-all.svg') ?>"></span><span>30 Showcase Products (Front Display Products)</span></p>
                    <p><span><img src="<?= base_url('assets/images/check-all.svg') ?>"></span><span>Buyers Database Access</span></p>
                    <p><span><img src="<?= base_url('assets/images/check-all.svg') ?>"></span><span>Team of 02 BCM (Buyer Consultant Managers)</span></p>
                    <p><span><img src="<?= base_url('assets/images/check-all.svg') ?>"></span><span>International .COM Website</span></p>
                    <p><span><img src="<?= base_url('assets/images/check-all.svg') ?>"></span><span>WebDomain & WebHosting</span></p>
                    <p><span><img src="<?= base_url('assets/images/check-all.svg') ?>"></span><span>Professional Email Addresses for 05 Users</span></p>
                    <p><span><img src="<?= base_url('assets/images/check-all.svg') ?>"></span><span>Logo Designing</span></p>
                    <p><span><img src="<?= base_url('assets/images/check-all.svg') ?>"></span><span>Banner Advertisement</span></p>
                    <p><span><img src="<?= base_url('assets/images/check-all.svg') ?>"></span><span>Social Media Marketing</span></p>
                    <p><span><img src="<?= base_url('assets/images/check-all.svg') ?>"></span><span>Google SEO (Top ranking worldwide on 03 Keywords of your products)</span></p>
                    <p><span><img src="<?= base_url('assets/images/check-all.svg') ?>"></span><span>Stationary Designing</span></p>
                    <p><span><img src="<?= base_url('assets/images/check-all.svg') ?>"></span><span>International Company Registration (LLC/LTD Formation)</span></p>
                    <p><span><img src="<?= base_url('assets/images/check-all.svg') ?>"></span><span>Catalog Designing</span></p>
                    <p><span><img src="<?= base_url('assets/images/check-all.svg') ?>"></span><span>10-12 Top-Rated Verified Buyers/Month</span></p>
                </div>
                
             <a class="Download_pdf text-start mt-4 new-brochure-link" target="_blank" href="<?= base_url('assets/media/b2b-trade-services-corporate-profile.pdf') ?>"><img src="<?= base_url('assets/images/arrow-right-circle-fill.svg') ?>">View Brochures</a>
            </div>
            <div class="b2b-top-form single-view-pkg-form">
                <div class="view-pkg-img">
                    <img src="<?= base_url('assets/images/palti-coin.webp') ?>" class="w-100">
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
                    <input type="hidden" name="source_page" value="platinum-package">
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
                        <input type="checkbox" name="agree" id="agree-platinum" required><label for="agree-platinum"><span style="color: #0F9EA5;">*</span>By joining, I agree to terms of use, privacy policy, IPR and agree to receive emails related to our services.</label>
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
                <h2>Official Company Profile Page:</h2>
                <p class="mt-3">B2B TRADE SERVICES will design an eye-catching official company PLATINUM profile page for your business which will help your buyers to directly get to know more about your company size, products, history and validation.</p>
                
            </div>
        </div>
    </div>
</section>

<!-- Featured Supplier & Buyer Access section -->
<section class="about-us-intro mt-5">
    <div class="container">
        <div class="row mt-5 align-items-center row-rev-mob">
            <div class="col-md-7">
                <h2>Online Store With 30 Showcase Products:</h2>
                <p>B2B TRADE SERVICES will design a separate Products Landing Page with extended URL domain name linked to your company like:(<a href="http://www.B2Btradeservices.com/your-company-name">www.B2Btradeservices.com/your-company-name</a>), which you can share with all your buyers and importers confidently and increase your products optimization and trust over buyers. Starter Members can post up to 30 Products to be displayed at Front Display.</p>
            </div>
            <div class="col-md-5">
                <div class="about-us-box-img">
                    <img src="<?= base_url('assets/images/gold-col1.png') ?>" class="w-100">
                </div>
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
                    <img src="<?= base_url('assets/images/gold-col2.png') ?>" class="w-100">
                </div>
            </div>
            <div class="col-md-6">
                <h2>Buyers Database Access</h2>
                <p class="mt-3">In this PLATINUM Membership Package, you will be able to connect with as many buyers as you want via our platform. No limit to daily messages, hassle free communication and quick conversation options with run-time online active buyers</p>
                
            </div>
        </div>
    </div>
</section>

<!-- Featured Supplier & Buyer Access section -->
<section class="about-us-intro mt-5">
    <div class="container">
        <div class="row mt-5 align-items-center row-rev-mob">
            <div class="col-md-7">
                <h2>Buyer Consultant Manager (Team of 02 Dedicated Expert):</h2>
                 <p>BCM means BUYER CONSULTANT MANAGER, A (BCM) is an officially employed person of B2B TRADE SERVICES PLATFORM with 3-4 Years of extensive research and experience on Buyer Fetching, Importers Contact details Validation, Inquiry Confirmation and Establishing professional Connections between Exporters and Importers.  <br>
In this PLATINUM MEMBERSHIP PACKAGE, we will assign a Team of 02 special BCM to you who understands your business, products, buyers’ criteria and target markets. They will work for your company on daily basis (across different time zones) to cater more buyers in different countries.</p>
                  <p>Your TEAM of 2 BCMS will work on following process Regularly:</p>
                  <ul class="checkbox-ul-new premeium-ul mt-3">
                    <li>Creating and Managing your Online Store on www.b2btradeservices.com</li>
                    <li>Uploading all of your products at company showcase profile</li>
                    <li>Finding new b2b buyers, bulk importers and companies who are looking to buy your products</li>
                    <li>Verifying buyer’s company details, contact details</li>
                    <li>Validating buyer’s requirements and checking their interest level</li>
                    <li>Confirming exact buying requirements to see if the buyer is a good match or not</li>
                    <li>Connect and introduce Top-Rated buyers with you on WhatsApp/ Zoom Call/ Email</li>
                    <li>Follow-up with your existing buyers while continuously hunting for new leads</li>
                    <li>Helps you in drafting Official Quotations Documents, Verifying LOI (Letter of Intent) from Buyer</li>
                    <li>Monthly Reporting and KPI’s Assessment

</li>
                </ul>
                  </div>
            <div class="col-md-5">
                <div class="about-us-box-img">
                    <img src="<?= base_url('assets/images/two-manager.png') ?>" class="w-100">
                </div>
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
                    <img src="<?= base_url('assets/images/gold-col4.png') ?>" class="w-100">
                </div>
            </div>
            <div class="col-md-6">
                <h2>International .COM Website (03 Languages):</h2>
                <p>Expand beyond borders with a professionally developed international company website designed specifically for export-focused businesses.</p>
                <p>In today’s global trade environment, buyers expect credibility, clarity, and international accessibility. Our .COM website solution positions your business as a trusted global supplier.
</p>
<p>Why Exporters Need a Dedicated International Website</p>

<ul class="checkbox-ul-new premeium-ul mt-3">
                    <li>Overseas buyers search on Google using global domains (.com preferred)</li>
                    <li>International distributors expect professional product presentation</li>
                    <li>Trade inquiries require structured RFQ systems</li>
                    <li>Trust signals significantly impact conversion rates</li>
                    <li>A globally optimized website increases direct export inquiries</li>
                </ul>
                <p>WHATS INCLUDED in your .COM International Website: 
</p>
<ul class="checkbox-ul-new premeium-ul mt-3">
                    <li>Custom-designed, export-focused multi-page website (5–10 pages)</li>
                    <li>Mobile-responsive, fast-loading, and SSL-secured setup</li>
                    <li>Product showcase pages with inquiry (RFQ) forms</li>
                    <li>Basic international SEO setup (meta tags, sitemap, Google indexing)</li>
                    <li>Lead capture integration (email, WhatsApp, contact routing)</li>
                    <li>Designed and developed in 03 different Languages of your choice
</li>
                </ul>
                 
            </div>
        </div>
    </div>
</section>



<!-- Featured Supplier & Buyer Access section -->
<section class="about-us-intro mt-5">
    <div class="container">
        <div class="row mt-5 align-items-center row-rev-mob">
            <div class="col-md-7">
                <h2>Webdomain & Webhosting
</h2>
<p>A professional domain name gives your business credibility and makes it easy for international buyers to find and remember you. Coupled with fast, reliable hosting servers, it ensures your website loads quickly, provides a smooth user experience, and builds trust—critical factors for converting visitors into genuine B2B buyers.</p>
               <p>What’s Included:</p>
                <ul class="checkbox-ul-new premeium-ul mt-3">
                    <li>Registration of your custom domain name (e.g., www.yourcompany.com)</li>
                    <li>Secure web hosting with reliable uptime</li>
                    <li>Website deployment on your preferred domain</li>
                    <li>SSL certificate for safe browsing</li>
                    <li>Email setup linked to your domain</li>
                    <li>Technical support and maintenance for smooth operation</li>
                </ul>
               
               
                 
                  </div>
            <div class="col-md-5">
                <div class="about-us-box-img">
                    <img src="<?= base_url('assets/images/domain-pkg.png') ?>" class="w-100">
                </div>
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
                    <img src="<?= base_url('assets/images/mail-pkg.png') ?>" class="w-100">
                </div>
            </div>
            <div class="col-md-6">
                <h2>Professional  Email Address (05 Users):</h2>
                <p>Platinum Membership includes custom business email accounts for five users, enhancing your company’s credibility and communication with international buyers. Professional emails (e.g., name@yourcompany.com) build trust, reinforce your brand, and ensure secure, reliable correspondence for all B2B interactions.</p>
 
                 
            </div>
        </div>
    </div>
</section>


<!-- Featured Supplier & Buyer Access section -->
<section class="about-us-intro mt-5">
    <div class="container">
        <div class="row mt-5 align-items-center row-rev-mob">
            <div class="col-md-7">
                <h2>Logo Designing:
</h2>
<p>A professionally designed logo is essential for international suppliers and exporters because it represents your brand in global markets where first impressions determine credibility. Even if your company already has a logo, older designs often look outdated, overly local, or inconsistent with modern international standards. A refreshed, globally aligned logo gives your business a more professional, trustworthy, and competitive appearance — helping you attract overseas buyers, strengthen brand recognition, and position your company as a serious global player.</p>
               <p>What’s Included:</p>
                <ul class="checkbox-ul-new premeium-ul mt-3">
                    <li>Custom-designed logo concepts tailored for international markets</li>
                    <li>Modern, clean, globally relevant design approach</li>
                    <li>Multiple initial concepts with revision rounds</li>
                    <li>High-resolution files (PSD, PNG, JPG, PDF formats)</li>
                </ul>
                  </div>
            <div class="col-md-5">
                <div class="about-us-box-img">
                    <img src="<?= base_url('assets/images/gold-col5.png') ?>" class="w-100">
                </div>
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
                <h2>Banner Advertisement:</h2>
                 
                 <p>A homepage banner is a powerful way to display your company name, promotions, or brand message prominently on our B2B platform right in front of millions of buyers worldwide. </p>
            <p>Why It’s Important:</p>
             <ul class="checkbox-ul-new premeium-ul mt-3">
                    <li>Maximizes visibility to international buyers right on the homepage</li>
                   <li>Instantly grabs attention with your products or offers</li>
                   <li>Drives more inquiries and leads from serious B2B clients</li>
                   <li>Reinforces brand credibility and professionalism</li>
                   <li>Highlights key promotions or new product launches to the right audience
</li>
                </ul>
            </div>
        </div>
    </div>
</section>


<!-- Featured Supplier & Buyer Access section -->
<section class="about-us-intro mt-5">
    <div class="container">
        <div class="row mt-5 align-items-center row-rev-mob">
            <div class="col-md-7">
                <h2>Social Media Marketing
</h2>
<p>Social Media Marketing helps your business connect with global buyers, build brand awareness, and promote products across major platforms, our social media experts will create your accounts and maintain with weekly postings on all major platforms specially (Facebook, LinkedIn and Instagram)</p>
               <p>What’s Included:</p>
                <ul class="checkbox-ul-new premeium-ul mt-3">
                    <li>Setup and optimization of business profiles on key social platforms</li>
                    <li>Regular posting of products, updates, and promotional content</li>
                    <li>Content designed for international B2B audiences</li>
                    <li>Engagement monitoring and response management</li>
                    <li>Ad Campaign Designing</li>
                    <li>Performance reports to track reach, leads, and engagement</li>
                </ul>
                  </div>
            <div class="col-md-5">
                <div class="about-us-box-img">
                    <img src="<?= base_url('assets/images/smm-pkg.png') ?>" class="w-100">
                </div>
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
                    <img src="<?= base_url('assets/images/seo-pkg.png') ?>" class="w-100">
                </div>
            </div>
            <div class="col-md-6">
                <h2> Google SEO  (Search Engine Optimization) – <br> 06 Keywords</h2>
                 
                 <p>Our Google SEO service helps your business achieve top visibility in search results, driving qualified international buyers to your products. Our Google SEO experts will make sure that your products appear at the top of search results, making it easier for international buyers to find you. Higher rankings drive more qualified traffic, increase inquiries, and boost global credibility. </p>
            <p>Why It’s Important:</p>
             <ul class="checkbox-ul-new premeium-ul mt-3">
                    <li>Optimization for up to 03 targeted product keywords</li>
                    <li>On-page SEO (meta tags, headings, content optimization)</li>
                    <li>Off-page SEO support (backlinks, directory listings)</li>
                    <li>Technical SEO checks for fast loading and mobile-friendliness</li>
                </ul>
            </div>
        </div>
    </div>
</section>



<!-- Featured Supplier & Buyer Access section -->
<section class="about-us-intro mt-5">
    <div class="container">
        <div class="row mt-5 align-items-center row-rev-mob">
            <div class="col-md-7">
                <h2>Stationery Designing
</h2>
<p>Professional stationery creates a consistent and credible brand image for your business, both online and offline.</p>
   <p>What’s Included:</p>
                <ul class="checkbox-ul-new premeium-ul mt-3">
                    <li>Custom letterhead design for official correspondence</li>
                    <li>Business card design for networking and trade events (05 Users)</li>
                    <li>Email signature aligned with your brand identity (05 Users)</li>
                    <li>Flyer design to promote products, offers, or events</li>
                    <li>Cups, Caps and Company T-shirt Promotion Designing
</li>
                </ul>
                  </div>
            <div class="col-md-5">
                <div class="about-us-box-img">
                    <img src="<?= base_url('assets/images/office-holdings-pkg.png') ?>" class="w-100">
                </div>
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
                    <img src="<?= base_url('assets/images/llc-pkg.png') ?>" class="w-100">
                </div>
            </div>
            <div class="col-md-6">
                <h2>International Company Registration (LLC/LTD Formation)</h2>
                 
                 <p>Platinum Membership includes support for setting up your international business entity, helping you establish credibility and operate globally. Registering an international company (LLC in the USA or LTD in the UK) gives your business global credibility and legal recognition, making it easier to attract serious overseas buyers. It also enables smoother transactions, builds trust, and strengthens your brand presence in international markets.</p>
            <p>Why It’s Important:</p>
             <ul class="checkbox-ul-new premeium-ul mt-3">
                    <li>Registration of LLC in the USA or LTD in the UK</li>
                    <li>Preparation and filing of all legal documents</li>
                    <li>Assistance with company name approval and compliance</li>
                    <li>Guidance on banking, tax, and regulatory requirements</li>
                    <li>Official certificate issuance and ongoing support for company setup</li>
                </ul>
            </div>
        </div>
    </div>
</section>



<!-- Featured Supplier & Buyer Access section -->
<section class="about-us-intro mt-5">
    <div class="container">
        <div class="row mt-5 align-items-center row-rev-mob">
            <div class="col-md-7">
                <h2>10-12 Top-Rated Verified Buyers/Month</h2>
<p>Receive access to 10–12 carefully screened and credibility-verified Top Rated international buyers each month, ensuring your business connects only with genuine, high-potential importers actively sourcing products in your category.</p>
   <p><b>WHO IS A TOP RATED (TR) BUYER?</b><br>A Top-Rated (TR) Buyer is a serious, verified, and purchase-ready B2B client. Each TR Buyer must meet the following strict qualification standards:</p>
   
                <ul class="checkbox-ul-new premeium-ul mt-3">
                    <li>Verified directly by our BCM (Buyer Consultant Manager)</li>
                    <li>Looking strictly for bulk quantities (genuine B2B buyers, not B2C inquiries)</li>
                    <li>Actively sourcing the exact or highly relevant products that you supply</li>
                    <li>Provides complete company profile and verified contact details</li>
                    <li>Shares proof of requirement verification (posted RFQs or confirmed WhatsApp communication with BCM detailing full purchase requirements)</li>
                    <li>Ready to communicate in real-time with the supplier via WhatsApp, Zoom call, or email</li>
                    <li>Has a working contact number, active WhatsApp and email, or an operational company website

</li>
                </ul>
                <p>This structured screening ensures you connect only with authentic, serious, and transaction-ready</p>
                  </div>
            <div class="col-md-5">
                <div class="about-us-box-img">
                    <img src="<?= base_url('assets/images/gold-col7.png') ?>" class="w-100">
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
                    <h2 class="f-18">24/7 Available Customer Service <br class="br_hide"> with B2BTradeServices</h2>
                    <ul class="checkbox-ul-new premeium-ul mt-3">
                        <li><h3 class="f-16 mb-1 h5"><b>Ready to Help:</b></h3>We are always available for our premium clients. We ensure that all of their inquiries are responded effectively</li>
                        <li><h3 class="f-16 mb-1 h5"><b>Trade Advice and Consultancy:</b></h3>Our experienced staff is always there for you. If you need any trade-related advice, we have got your back.</li>
                    </ul>
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
