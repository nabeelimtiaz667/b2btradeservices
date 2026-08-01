<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>



<section class="banner-sec mt-4">
    <div class="container">
        <div class="row">
            <div class="Categories-list">
                <h5>Categories </h5>
                <ul>
                    <?php if (isset($categories)): ?>
                        <?php foreach (array_slice($categories, 0, 12) as $cat): ?>
                            <li><a href="<?= base_url('supplier-category/' . $cat['slug']) ?>"> <?= esc($cat['name']) ?></a></li>
                        <?php endforeach; ?>
                        <li><a href="<?= base_url('supplier-category') ?>"> All Categories</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="banner-slider-sec">
                <div class="banner-slider">
                    <div>
                       <a href="<?= base_url('premium-services') ?>"> <img src="<?= base_url('assets/images/web-ban01.webp') ?>" alt=""> </a>
                    </div>
                    <div>
                       <a href="<?= base_url('premium-services') ?>"> <img src="<?= base_url('assets/images/web-ban02.webp') ?>" alt=""> </a>
                    </div>
                    <div>
                       <a href="<?= base_url('buyers') ?>"> <img src="<?= base_url('assets/images/web-ban03.webp') ?>" alt=""> </a>
                    </div>
                    <!--<div>-->
                    <!--   <a href="<?= base_url('supplier/profile/satin-packages-limited') ?>"> <img src="<?= base_url('assets/images/web-ban04.webp') ?>" alt=""> </a>-->
                    <!--</div>-->
                </div>
            </div>
            <div class="b2b-top-form">
                <h3>Register Quick Now! And get free Buyers/ Suppliers Leads</h3>
                <form action="<?= base_url('register') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="filter-group ">
                            <label class="radio-label">
                                <input type="radio" name="role" value="supplier" checked   onchange="toggleFields(this)">
                                <span class="radio"></span>
                                Supplier
                            </label>

                            <label class="radio-label">
                                <input type="radio" name="role"  value="buyer" onchange="toggleFields(this)">
                                <span class="radio"></span>
                                Buyer
                            </label>


                        </div>

                        <div class="form-input mt-3">
                <input type="text" name="name" placeholder="Name*" required>
            </div>

            <div class="dual-input">
                <div class="form-input ">
                    <input type="email" name="email" placeholder="Email*" required>
                </div>
                <div class="form-input password-input">
                    <input type="password" name="password" class="password" placeholder="Password*" required>
                    <i class="eye" onclick="togglePassword()"><svg style="fill: #DBDBDB" class="eye-icon" height="20"
                            viewBox="0 0 24 24" width="20" xmlns="http://www.w3.org/2000/svg">
                            <path clip-rule="evenodd"
                                d="m12 4.5c-5 0-9.27 3.11-11 7.5 1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zm0 12.5c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z">
                            </path>
                        </svg></i>
                </div>


            </div>
            <div class="form-input">
                <input type="tel" name="phone" class="phone" placeholder="Phone number*" required>
            </div>
            <div class="whatsapp-checkbox">
                <input type="checkbox" name="whatsapp" id="whatsapp"><label for="whatsapp">Whatsapp<img src="<?= base_url('assets/images/whatsapp-icon.svg') ?>" width="15px"></label>
            </div>
            <div class="form-input">
               <select class="form-control country country-select" required=""  name="country_id">
                    <option value="" selected="selected">Country*</option>
                    <?php if (isset($countries)): ?>
                        <?php foreach ($countries as $country): ?>
                            <option value="<?= $country['id'] ?>"><?= esc($country['name']) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

<div class="form-input selling-field">
    <input type="text" name="selling_products" placeholder="Selling Products*" required>
</div>


<div class="form-input buying-field" style="display:none;">
    <input type="text" name="buying_products" placeholder="Buying Products*">
</div>



            <div class="radio-join d-flex gap-2 align-items-start">
                <input type="checkbox" name="OPT_IN" id="OPT_IN" required><label for="OPT_IN"><span
                        style="color: #0F9EA5;">*</span>By joining. I agree to terms of use, privacy policy, IPR and
                    agree to receive emails related to our services.</label>
            </div>
            <div class="submit-btn-gradient mt-3">
                        <button type="submit" class="gradeint-cta">Register Now</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</section>


<section class="mt-5">
    <div class="container">
        <div class="row"> 
            <div class="latest-buy-offer">
                <div class="d-flex align-items-center justify-content-between">
                    <h2 class="mb-0">Latest Buy Offers</h2>
                    <a href="<?= base_url('buyer') ?>" class="view-all-link d-flex align-items-center gap-2"> View All <img src="<?= base_url('assets/images/arrow-right.svg') ?>"></a>
                </div>
                <?php if (isset($latestInquiries) && !empty($latestInquiries)): ?>
                    <?php foreach (array_slice($latestInquiries, 0, 8) as $inq): ?>
                        <a href="<?= inquiry_url($inq) ?>" class="text-decoration-none text-dark buy-offer-link">
                            <div class="latest-buy-offer-row mt-4">
                                <div class="d-flex gap-3 align-items-center">
                                    <?php if (!empty($inq['country_flag'])): ?>
                                        <img src="<?= base_url('assets/images/flags/' . $inq['country_flag']) ?>" alt="" style="width: 30px; height: 20px; object-fit: cover; border-radius: 3px; border: 1px solid #eee;">
                                    <?php elseif (!empty($inq['attachment'])): ?>
                                        <img src="<?= base_url('uploads/inquiries/' . $inq['attachment']) ?>" alt="Reference" style="width: 40px; height: 40px; object-fit: cover; border-radius: 6px; border: 1px solid #eee;">
                                    <?php else: ?>
                                        <div style="width: 30px; height: 20px; background: #f0f0f0; border-radius: 3px; display: flex; align-items: center; justify-content: center;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="#999" viewBox="0 0 16 16"><path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5l6.154 2.462 6.154-2.462L8.186 1.113zM15 4.239l-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923l6.5 2.6z"/></svg>
                                        </div>
                                    <?php endif; ?>
                                    <span><?= esc($inq['title'] ?? $inq['product_name'] ?? 'Buy Offer') ?></span>
                                </div>
                                <div>
                                    <p class="mb-0 date-latest"><?= date('M d, Y', strtotime($inq['inquiry_date'])) ?></p>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted mt-3">No buy offers yet.</p>
                <?php endif; ?>
            </div>
            <div class="multiple-quote-form">
                <h2 class="text-white text-center">Get Multiple Quotes</h2>
                <?php if (session()->getFlashdata('success') && !isset($formSubmitted)): ?>
                    <div class="alert alert-success text-center"><?= session()->getFlashdata('success') ?></div>
                <?php endif; ?>
                <form action="<?= base_url('contact/submit') ?>" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="form_type" value="quote">
                    <input type="hidden" name="source_page" value="homepage">
                    <input type="hidden" name="lead_type" value="buyer">
                    <div class="form-input">
                        <input type="text" name="name" placeholder="Name*" required>
                    </div>
                    <div class="dual-input">
                        <div class="form-input">
                        <input type="email" name="email" placeholder="Email*" required>
                    </div>
                      <div class="form-input">
                        <input type="tel" name="phone" class="phone" placeholder="Phone*" required>
                    </div>
                    </div>
                      <div class="dual-input">
                        <div class="form-input">
                     <select class="form-control country-select"  name="country_id" required>
                    <option value="" selected="selected">Country</option>
                    <?php if (isset($countries)): ?>
                        <?php foreach ($countries as $country): ?>
                            <option value="<?= $country['id'] ?>"><?= esc($country['name']) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
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
        </div>
    </div>
</section>

<section class="mt-4">
    <div class="container">
        <div class="row">
            <div class="top-products">
               <div class="d-flex heading align-items-center justify-content-between">
                    <h2 class="mb-0">Top Products</h2>
                    <a href="<?= base_url('product') ?>" class="view-all-link d-flex align-items-center gap-2"> View All <img src="<?= base_url('assets/images/arrow-right.svg') ?>"></a>
                </div>
                <?php if (isset($topProducts) && !empty($topProducts)): ?>
                    <?php foreach (array_slice($topProducts, 0, 3) as $tp): ?>
                    <div class="top-products-box">
                        <div class="top-products-img">
                            <?php if (!empty($tp['main_image'])): ?>
                                <img src="<?= base_url('uploads/products/' . $tp['main_image']) ?>" class="w-100">
                            <?php else: ?>
                                <img src="<?= base_url('assets/images/top-product-img-1.webp') ?>" class="w-100">
                            <?php endif; ?>
                        </div> 
                        <div class="top-products-content">
                            <h3><?= esc($tp['name']) ?></h3>
                            <?php if (isset($tp['supplier']['country'])): ?>
                                <p class="d-flex align-items-center gap-2">
                                    <img src="<?= base_url('assets/images/flags/' . ($tp['supplier']['country']['flag'] ?? '')) ?>" width="20" onerror="this.style.display='none'">
                                    <?= esc($tp['supplier']['country']['name'] ?? '') ?>
                                </p>
                            <?php else: ?>
                                <p>&nbsp;</p>
                            <?php endif; ?>
                            <div class="product-link d-flex justify-content-between align-items-center">
                                <a href="<?= base_url('product/detail/' . $tp['id']) ?>">Learn More</a> <a href="<?= base_url('product/detail/' . $tp['id']) ?>"><img src="<?= base_url('assets/images/down-arrow.svg') ?>"></a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted mt-3">No products available yet.</p>
                <?php endif; ?>
            </div>
            <div class="top-supplier">
                <div class="top-supplier-main">
                    <div class="d-flex heading align-items-center justify-content-between">
                        <h2 class="mb-0">Top Suppliers</h2>
                        <a href="<?= base_url('supplier') ?>" class="view-all-link d-flex align-items-center gap-2"> View All <img src="<?= base_url('assets/images/arrow-right.svg') ?>"></a>
                    </div>

                    <div class="top-supplier-card-sec">
                        <div class="row">
                            <?php if (isset($featuredSuppliers) && !empty($featuredSuppliers)): ?>
                                <?php $imgIndex = 1; foreach (array_slice($featuredSuppliers, 0, 2) as $ts): ?>
                                <div class="top-supplier-card">
                                    <div class="top-supplier-img">
                                        <?php
                                            $profileImg = '';
                                            if (!empty($ts['profile_image'])) {
                                                $profileImg = base_url('uploads/profiles/' . $ts['profile_image']);
                                            } elseif (!empty($ts['company_logo'])) {
                                                $profileImg = base_url('uploads/suppliers/' . $ts['company_logo']);
                                            } elseif (!empty($ts['products']) && !empty($ts['products'][0]['main_image'])) {
                                                $profileImg = base_url('uploads/products/' . $ts['products'][0]['main_image']);
                                            } else {
                                                $profileImg = base_url('assets/images/supplier-product-list-img.webp');
                                            }
                                        ?>
                                        <img src="<?= $profileImg ?>" class="w-100" alt="<?= esc($ts['company_name'] ?? $ts['name']) ?>">
                                    </div>
                                    <div class="top-supplier-content">
                                        <p class="f-12 mb-3"><?= esc($ts['category'] ?? $ts['industry'] ?? '') ?></p>
                                        <h3><?= esc($ts['company_name'] ?? $ts['name']) ?></h3>
                                        <p>Products: <?= esc($ts['selling_products'] ?? '') ?> <br>
Country: <?= esc($ts['country']['name'] ?? $ts['country_name'] ?? '') ?></p>
                                        <div class="">
                                            <a href="<?= base_url('supplier/profile/' . ($ts['slug'] ?? $ts['id'])) ?>" class="outline-btn contact-btn btn">Contact</a>
                                        </div>
                                    </div>
                                </div>
                                <?php $imgIndex++; endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted">No suppliers available yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="cta-sec mt-5">
    <div class="cta-sec-content">
        <h2 class="text-white">Buyer Consultant <br>Manager (BCM)</h2>
        <p class="text-white">Your dedicated employee from our Head Quarter, responsible for fetching genuine leads for your business while <br class="br_hide"> managing your trade account professionally.</p>
        <div>
            <a   class="btn gradeint-cta bcm-cta" >Schedule a Free Appointment with your BCM</a>
        </div>
    </div>
</section>

<div id="popup1" style="display: none;">
    <div class="b2b-top-form">
        <button id="closePopup1">X</button>
        <h2 class="text-center">Buyer Consultant Manager (BCM)</h2>
        <h3>Schedule a Free Appointment with your BCM</h3>
        <form action="<?= base_url('register') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="filter-group">
                <label class="radio-label">
                    <input type="radio" name="role" value="supplier" checked onchange="toggleFields(this)">
                    <span class="radio"></span>
                    Supplier
                </label>
                <label class="radio-label">
                    <input type="radio" name="role" value="buyer" onchange="toggleFields(this)">
                    <span class="radio"></span>
                    Buyer
                </label>
            </div>
            <div class="form-input mt-3">
                <input type="text" name="name" placeholder="Name" required>
            </div>
            <div class="dual-input">
                <div class="form-input">
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="form-input password-input">
                    <input type="password" name="password" class="password" placeholder="Password">
                    <i class="eye" onclick="togglePassword()">
                        <svg style="fill: #DBDBDB" class="eye-icon" height="20" viewBox="0 0 24 24" width="20" xmlns="http://www.w3.org/2000/svg">
                            <path clip-rule="evenodd" d="m12 4.5c-5 0-9.27 3.11-11 7.5 1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zm0 12.5c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"></path>
                        </svg>
                    </i>
                </div>
            </div>
            <div class="form-input">
                <input type="tel" name="phone" class="phone" placeholder="Phone number">
            </div>
            <div class="whatsapp-checkbox">
                <input type="checkbox" name="whatsapp" id="popup_whatsapp"><label for="popup_whatsapp">Whatsapp<img src="<?= base_url('assets/images/whatsapp-icon.svg') ?>" width="15px"></label>
            </div>
            <div class="form-input selling-field">
                <input type="text" name="selling_products" placeholder="Selling Products">
            </div>
            <div class="form-input buying-field" style="display:none;">
                <input type="text" name="buying_products" placeholder="Buying Products">
            </div>
            <div class="radio-join d-flex gap-2 align-items-start">
                <input type="checkbox" name="OPT_IN" id="popup_OPT_IN" required><label for="popup_OPT_IN"><span style="color: #0F9EA5;">*</span>By joining, I agree to terms of use, privacy policy, IPR and agree to receive emails related to our services.</label>
            </div>
            <div class="submit-btn-gradient mt-3">
                <button type="submit" class="gradeint-cta">Join Now</button>
            </div>
        </form>
    </div>
</div>

<div id="overlay1" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999;"></div>

<section class="pricing-sec text-center pricing-sec-with-tabs mt-5 mb-5">
    <div class="container">
        <h2>Premium Exclusive Services</h2>
        <p>Get 10x more leads with our result oriented service packs</p>
          <ul class="nav nav-tabs mb-3 gap-2 justify-content-center d-md-none" id="mobilePackageTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="silver-tab" data-bs-toggle="tab" data-bs-target="#silver" type="button" role="tab" aria-controls="silver" aria-selected="true">Starter</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="gold-tab" data-bs-toggle="tab" data-bs-target="#gold" type="button" role="tab" aria-controls="gold" aria-selected="false">Gold</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="platinium-tab" data-bs-toggle="tab" data-bs-target="#platinium" type="button" role="tab" aria-controls="platinium" aria-selected="false">Platinum</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="vip-tab" data-bs-toggle="tab" data-bs-target="#vip" type="button" role="tab" aria-controls="vip" aria-selected="false">VIP</button>
                </li>
            </ul>
             <div class="tab-content" id="mobilePackageTabsContent">
        <div class="row mt-lg-5 pt-md-5 pt-3">
            <div class="col-md-3">
                 <div class="tab-pane fade show active" id="silver" role="tabpanel" aria-labelledby="silver-tab">
                <div class="price-box">
                    <div class="price-box-img text-center">
                        <img style="width: 125px;" src="<?= base_url('assets/images/starter-coin.webp') ?>">
                    </div>
                    <div class="price-box-content show-more-wrapper" data-visible="5">
                        <p><span><img src="<?= base_url('assets/images/right-check.svg') ?>"></span><span>Company Profile Page
</span></p>
                        <p><span><img src="<?= base_url('assets/images/right-check.svg') ?>"></span><span>10 Showcase Products <br><span class="span-price"> (Front Display Products)</span></span></p>
                        <p><span><img src="<?= base_url('assets/images/wrong-tik.svg') ?>"></span><span>Special Product Catalog Page
</span></p>
                        <p><span><img src="<?= base_url('assets/images/right-check.svg') ?>"></span><span>Buyer's Database Access
</span></p>
                        <p><span><img src="<?= base_url('assets/images/wrong-tik.svg') ?>"></span><span>Banner Advertisement
</span></p>
                        <p><span><img src="<?= base_url('assets/images/wrong-tik.svg') ?>"></span><span>BCM (Buyer Consultant Manager)</span></p>
                        <p><span><img src="<?= base_url('assets/images/wrong-tik.svg') ?>"></span><span>International .COM Website
</span></p>
                        <p><span><img src="<?= base_url('assets/images/wrong-tik.svg') ?>"></span><span>WebDomain & WebHosting
</span></p>
                        <p><span><img src="<?= base_url('assets/images/wrong-tik.svg') ?>"></span><span>Social Media Marketing
</span></p>
                        <p><span><img src="<?= base_url('assets/images/wrong-tik.svg') ?>"></span><span>Google SEO (Search Engine Optimization)
</span></p>
    <p><span><img src="<?= base_url('assets/images/wrong-tik.svg') ?>"></span><span>Logo Designing

</span></p>
    <p><span><img src="<?= base_url('assets/images/wrong-tik.svg') ?>"></span><span>Stationary Designing

</span></p>
    <p><span><img src="<?= base_url('assets/images/wrong-tik.svg') ?>"></span><span>International Company Registration (LLC/LTD)

</span></p>
    <p><span><img src="<?= base_url('assets/images/wrong-tik.svg') ?>"></span><span>Top-Rated Verified Buyers

</span></p>
    <p><span><img src="<?= base_url('assets/images/wrong-tik.svg') ?>"></span><span>Company profile PDF/ Product Catalog PDF Designing

</span></p>
                          <button class="show-more-btn">Show More</button>
                    </div>
                    <a class="outline-btn outline-btn-white search-btn btn learn_more_btn mt-3 mx-auto mb-3" href="<?= base_url('premium-services/starter-package') ?>">Learn More</a>
                    <a class="Download_pdf" target="_blank" href="<?= base_url('assets/media/b2b-trade-services-corporate-profile.pdf') ?>">Download PDF</a>
                </div>
            </div>
</div>
            <div class="col-md-3">
                   <div class="tab-pane fade" id="gold" role="tabpanel" aria-labelledby="gold-tab">
                <div class="price-box gold-price" style="position:relative; overflow:visible;">
                    <!--<div class="most-popular-ribbon">Most Popular</div>-->
                    <div class="price-box-img text-center">
                        <img style="width: 125px;" src="<?= base_url('assets/images/gold-coin.webp') ?>">
                    </div>
                   <div class="price-box-content show-more-wrapper" data-visible="5">
                         <p><span><img src="<?= base_url('assets/images/right-check.svg') ?>"></span><span>Company Profile Page
</span></p>
                        <p><span><img src="<?= base_url('assets/images/right-check.svg') ?>"></span><span>20 Showcase Products <br><span class="span-price"> (Front Display Products)</span></span></p>
                        <p><span><img src="<?= base_url('assets/images/right-check.svg') ?>"></span><span>Special Product Catalog Page
</span></p>
                        <p><span><img src="<?= base_url('assets/images/right-check.svg') ?>"></span><span>Buyer's Database Access
</span></p>
                        <p><span><img src="<?= base_url('assets/images/wrong-tik.svg') ?>"></span><span>Banner Advertisement
</span></p>
                        <p><span><img src="<?= base_url('assets/images/right-check.svg') ?>"></span><span>1 BCM (Buyer Consultant Manager)</span></p>
                        <p><span><img src="<?= base_url('assets/images/right-check.svg') ?>"></span><span>International .COM Website
</span></p>
                        <p><span><img src="<?= base_url('assets/images/wrong-tik.svg') ?>"></span><span>WebDomain & WebHosting
</span></p>
                        <p><span><img src="<?= base_url('assets/images/wrong-tik.svg') ?>"></span><span>Social Media Marketing
</span></p>
                        <p><span><img src="<?= base_url('assets/images/wrong-tik.svg') ?>"></span><span>Google SEO (Search Engine Optimization)
</span></p>
    <p><span><img src="<?= base_url('assets/images/right-check.svg') ?>"></span><span>Logo Designing

</span></p>
    <p><span><img src="<?= base_url('assets/images/wrong-tik.svg') ?>"></span><span>Stationary Designing

</span></p>
    <p><span><img src="<?= base_url('assets/images/wrong-tik.svg') ?>"></span><span>International Company Registration (LLC/LTD)

</span></p>
    <p><span><img src="<?= base_url('assets/images/right-check.svg') ?>"></span><span>(6-8 Buyers/ Month)
 Top-Rated Verified Buyers

</span></p>
    <p><span><img src="<?= base_url('assets/images/wrong-tik.svg') ?>"></span><span>Company profile PDF/ Product Catalog PDF Designing

</span></p>
                          <button class="show-more-btn">Show More</button>
                    </div>
                      <a class="outline-btn outline-btn-white search-btn btn learn_more_btn mt-3 mx-auto mb-3" href="<?= base_url('premium-services/gold-package') ?>">Learn More</a>
                    <a class="Download_pdf" target="_blank" href="<?= base_url('assets/media/b2b-trade-services-corporate-profile.pdf') ?>">Download PDF</a>
                </div>
            </div>
</div>
            <div class="col-md-3">
                <div class="tab-pane fade" id="platinium" role="tabpanel" aria-labelledby="platinium-tab">
                <div class="price-box platinium-price">
                    <div class="price-box-img text-center">
                        <img style="width: 125px;" src="<?= base_url('assets/images/palti-coin.webp') ?>">
                    </div>
                   <div class="price-box-content show-more-wrapper" data-visible="5">
                         <p><span><img src="<?= base_url('assets/images/right-check.svg') ?>"></span><span>Company Profile Page
</span></p>
                        <p><span><img src="<?= base_url('assets/images/right-check.svg') ?>"></span><span>30 Showcase Products <br><span class="span-price"> (Front Display Products)</span></span></p>
                        <p><span><img src="<?= base_url('assets/images/right-check.svg') ?>"></span><span>Special Product Catalog Page
</span></p>
                        <p><span><img src="<?= base_url('assets/images/right-check.svg') ?>"></span><span>Buyer's Database Access
</span></p>
                        <p><span><img src="<?= base_url('assets/images/right-check.svg') ?>"></span><span>Banner Advertisement
</span></p>
                        <p><span><img src="<?= base_url('assets/images/right-check.svg') ?>"></span><span>Team of 2 BCM (Buyer Consultant Manager)</span></p>
                        <p><span><img src="<?= base_url('assets/images/right-check.svg') ?>"></span><span>International .COM Website
</span></p>
                        <p><span><img src="<?= base_url('assets/images/right-check.svg') ?>"></span><span>WebDomain & WebHosting
</span></p>
                        <p><span><img src="<?= base_url('assets/images/right-check.svg') ?>"></span><span>Social Media Marketing
</span></p>
                        <p><span><img src="<?= base_url('assets/images/right-check.svg') ?>"></span><span>(03 Keywords) Google SEO (Search Engine Optimization)
</span></p>
    <p><span><img src="<?= base_url('assets/images/right-check.svg') ?>"></span><span>Logo Designing

</span></p>
    <p><span><img src="<?= base_url('assets/images/right-check.svg') ?>"></span><span>Stationary Designing

</span></p>
    <p><span><img src="<?= base_url('assets/images/right-check.svg') ?>"></span><span>International Company Registration (LLC/LTD)

</span></p>
    <p><span><img src="<?= base_url('assets/images/right-check.svg') ?>"></span><span>(10-12 Buyers/ Month)
 Top-Rated Verified Buyers

</span></p>
    <p><span><img src="<?= base_url('assets/images/right-check.svg') ?>"></span><span>Company profile PDF/ Product Catalog PDF Designing

</span></p>
                          <button class="show-more-btn">Show More</button>
                    </div>
                      <a class="outline-btn outline-btn-white search-btn btn learn_more_btn mt-3 mx-auto mb-3" href="<?= base_url('premium-services/platinum-package') ?>">Learn More</a>
                    <a class="Download_pdf" target="_blank" href="<?= base_url('assets/media/b2b-trade-services-corporate-profile.pdf') ?>">Download PDF</a>
                </div>
            </div>
  </div>
            <div class="col-md-3">
                 <div class="tab-pane fade" id="vip" role="tabpanel" aria-labelledby="vip-tab">
                <div class="price-box vip-price">
                    <div class="price-box-img text-center">
                        <img style="width: 125px;" src="<?= base_url('assets/images/vip-coin.webp') ?>">
                    </div>
                    <div class="price-box-content show-more-wrapper" data-visible="5">
                                       <p><span><img src="<?= base_url('assets/images/right-check.svg') ?>"></span><span>Company Profile Page
</span></p>
                        <p><span><img src="<?= base_url('assets/images/right-check.svg') ?>"></span><span>50 Showcase Products <br><span class="span-price"> (Front Display Products)</span></span></p>
                        <p><span><img src="<?= base_url('assets/images/right-check.svg') ?>"></span><span>Special Product Catalog Page
</span></p>
                        <p><span><img src="<?= base_url('assets/images/right-check.svg') ?>"></span><span>Buyer's Database Access
</span></p>
                        <p><span><img src="<?= base_url('assets/images/right-check.svg') ?>"></span><span>Banner Advertisement
</span></p>
                        <p><span><img src="<?= base_url('assets/images/right-check.svg') ?>"></span><span>Team of 3 BCM (Buyer Consultant Manager)</span></p>
                        <p><span><img src="<?= base_url('assets/images/right-check.svg') ?>"></span><span>International .COM Website
</span></p>
                        <p><span><img src="<?= base_url('assets/images/right-check.svg') ?>"></span><span>WebDomain & WebHosting
</span></p>
                        <p><span><img src="<?= base_url('assets/images/right-check.svg') ?>"></span><span>Social Media Marketing
</span></p>
                        <p><span><img src="<?= base_url('assets/images/right-check.svg') ?>"></span><span>(06 Keywords) Google SEO (Search Engine Optimization)
</span></p>
    <p><span><img src="<?= base_url('assets/images/right-check.svg') ?>"></span><span>Logo Designing

</span></p>
    <p><span><img src="<?= base_url('assets/images/right-check.svg') ?>"></span><span>Stationary Designing

</span></p>
    <p><span><img src="<?= base_url('assets/images/right-check.svg') ?>"></span><span>International Company Registration (LLC/LTD)

</span></p>
    <p><span><img src="<?= base_url('assets/images/right-check.svg') ?>"></span><span>(12-15 Buyers/ Month)
 Top-Rated Verified Buyers

</span></p>
    <p><span><img src="<?= base_url('assets/images/right-check.svg') ?>"></span><span>Company profile PDF/ Product Catalog PDF Designing

</span></p>
                          <button class="show-more-btn">Show More</button>
                    </div>
                      <a class="outline-btn outline-btn-white search-btn btn learn_more_btn mt-3 mx-auto mb-3" href="<?= base_url('premium-services/vip-package') ?>">Learn More</a>
                    <a class="Download_pdf" target="_blank" href="<?= base_url('assets/media/b2b-trade-services-corporate-profile.pdf') ?>">Download PDF</a>
                </div>
            </div>
              </div>
        </div>
         </div>
    </div>

</section>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<?php
$categoryGroups = [];
if (isset($categories) && !empty($categories)) {
    $catList = array_slice($categories, 0, 4);
    $supplierChunks = isset($categorySuppliers) ? array_chunk($categorySuppliers, 3) : (isset($featuredSuppliers) ? array_chunk($featuredSuppliers, 3) : []);
    $productChunks = isset($featuredProducts) ? array_chunk($featuredProducts, 3) : [];
    $bgClasses = ['product-bg-img-1', 'product-bg-img-2', 'product-bg-img-3', 'product-bg-img-4'];
    
    foreach ($catList as $ci => $cat) {
        $categoryGroups[] = [
            'category' => $cat,
            'suppliers' => isset($supplierChunks[$ci]) ? $supplierChunks[$ci] : [],
            'products' => isset($productChunks[$ci]) ? $productChunks[$ci] : [],
            'bgClass' => isset($bgClasses[$ci]) ? $bgClasses[$ci] : 'product-bg-img-1',
        ];
    }
}
?>

<?php foreach ($categoryGroups as $gi => $group): ?>
<?php if ($gi % 2 === 0): ?>
<section class="fetured-supplier-sec mt-5<?= $gi > 0 ? ' pt-5' : '' ?>">
    <div class="container">
        <div class="row align-items-center">
            <div class="product-bg-img <?= $group['bgClass'] ?>">
               <div class="product-bg-content">
                 <p class="mb-1 text-center text-white">Product</p>
                <h2 class="text-center text-white"><?= esc($group['category']['name']) ?></h2>
               </div>
            </div>
            <div class="fetured-supplier">
                <div class="d-flex heading align-items-center justify-content-between">
                    <h2 class="mb-0">Featured Suppliers</h2>
                    <a href="<?= base_url('supplier') ?>" class="view-all-link d-flex align-items-center gap-2"> View All <img src="<?= base_url('assets/images/arrow-right.svg') ?>"></a>
                </div>
                <div class="d-flex gap-2 mt-3 justify-content-between featured-box-row">
                    <?php if (!empty($group['suppliers'])): ?>
                        <?php foreach ($group['suppliers'] as $fs): ?>
                        <div class="top-products-box">
                            <div class="top-products-img featured-supplier-logo-wrap">
                                <?php if (!empty($fs['company_logo'])): ?>
                                    <img src="<?= base_url('uploads/suppliers/' . $fs['company_logo']) ?>" class="w-100 featured-supplier-logo" alt="<?= esc($fs['company_name'] ?? ($fs['name'] ?? '')) ?>">
                                <?php else: ?>
                                    <img src="<?= base_url('assets/images/supplier-product-list-img.webp') ?>" class="w-100">
                                <?php endif; ?>
                            </div> 
                            <div class="top-products-content">
                                <h3><?= esc($fs['company_name'] ?? ($fs['name'] ?? '')) ?></h3>
                                <p><?= esc($fs['country']['name'] ?? '') ?></p>
                                <div class="product-link d-flex justify-content-between align-items-center">
                                    <a href="<?= base_url('supplier/profile/' . ($fs['slug'] ?? $fs['id'])) ?>">Learn More</a> <a href="<?= base_url('supplier/profile/' . ($fs['slug'] ?? $fs['id'])) ?>"><img src="<?= base_url('assets/images/down-arrow.svg') ?>"></a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">No featured suppliers yet.</p>
                    <?php endif; ?>
                </div>

                <div class="d-flex heading mt-5 align-items-center justify-content-between">
                    <h2 class="mb-0">Featured Products</h2>
                    <a href="<?= base_url('product') ?>" class="view-all-link d-flex align-items-center gap-2"> View All <img src="<?= base_url('assets/images/arrow-right.svg') ?>"></a>
                </div>
                <div class="d-flex gap-2 mt-3 justify-content-between featured-box-row">
                    <?php if (!empty($group['products'])): ?>
                        <?php foreach ($group['products'] as $fp): ?>
                        <div class="top-products-box">
                            <div class="top-products-img">
                                <?php if (!empty($fp['main_image'])): ?>
                                    <img src="<?= base_url('uploads/products/' . $fp['main_image']) ?>" class="w-100">
                                <?php else: ?>
                                    <img src="<?= base_url('assets/images/supplier-product-img-1.webp') ?>" class="w-100">
                                <?php endif; ?>
                            </div> 
                            <div class="top-products-content">
                                <h3><?= esc($fp['name']) ?></h3>
                                <p><?= isset($fp['supplier']) ? esc($fp['supplier']['company_name'] ?? $fp['supplier']['name']) : '' ?></p>
                                <div class="product-link d-flex justify-content-between align-items-center">
                                    <a href="<?= base_url('product/detail/' . $fp['id']) ?>">Learn More</a> <a href="<?= base_url('product/detail/' . $fp['id']) ?>"><img src="<?= base_url('assets/images/down-arrow.svg') ?>"></a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">No featured products yet.</p>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</section>
<?php else: ?>
<section class="fetured-supplier-sec fetured-supplier-sec-rec mt-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="fetured-supplier">
                <div class="d-flex heading align-items-center justify-content-between">
                    <h2 class="mb-0">Featured Suppliers</h2>
                    <a href="<?= base_url('supplier') ?>" class="view-all-link d-flex align-items-center gap-2"> View All <img src="<?= base_url('assets/images/arrow-right.svg') ?>"></a>
                </div>
                <div class="d-flex gap-2 mt-3 justify-content-between featured-box-row">
                    <?php if (!empty($group['suppliers'])): ?>
                        <?php foreach ($group['suppliers'] as $fs): ?>
                        <div class="top-products-box">
                            <div class="top-products-img featured-supplier-logo-wrap">
                                <?php if (!empty($fs['company_logo'])): ?>
                                    <img src="<?= base_url('uploads/suppliers/' . $fs['company_logo']) ?>" class="w-100 featured-supplier-logo" alt="<?= esc($fs['company_name'] ?? ($fs['name'] ?? '')) ?>">
                                <?php else: ?>
                                    <img src="<?= base_url('assets/images/supplier-product-list-img.webp') ?>" class="w-100">
                                <?php endif; ?>
                            </div> 
                            <div class="top-products-content">
                                <h3><?= esc($fs['company_name'] ?? ($fs['name'] ?? '')) ?></h3>
                                <p><?= esc($fs['country']['name'] ?? '') ?></p>
                                <div class="product-link d-flex justify-content-between align-items-center">
                                    <a href="<?= base_url('supplier/profile/' . ($fs['slug'] ?? $fs['id'])) ?>">Learn More</a> <a href="<?= base_url('supplier/profile/' . ($fs['slug'] ?? $fs['id'])) ?>"><img src="<?= base_url('assets/images/down-arrow.svg') ?>"></a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">No featured suppliers yet.</p>
                    <?php endif; ?>
                </div>

                <div class="d-flex heading mt-5 align-items-center justify-content-between">
                    <h2 class="mb-0">Featured Products</h2>
                    <a href="<?= base_url('product') ?>" class="view-all-link d-flex align-items-center gap-2"> View All <img src="<?= base_url('assets/images/arrow-right.svg') ?>"></a>
                </div>
                <div class="d-flex gap-2 mt-3 justify-content-between featured-box-row">
                    <?php if (!empty($group['products'])): ?>
                        <?php foreach ($group['products'] as $fp): ?>
                        <div class="top-products-box">
                            <div class="top-products-img">
                                <?php if (!empty($fp['main_image'])): ?>
                                    <img src="<?= base_url('uploads/products/' . $fp['main_image']) ?>" class="w-100">
                                <?php else: ?>
                                    <img src="<?= base_url('assets/images/supplier-product-img-1.webp') ?>" class="w-100">
                                <?php endif; ?>
                            </div> 
                            <div class="top-products-content">
                                <h3><?= esc($fp['name']) ?></h3>
                                <p><?= isset($fp['supplier']) ? esc($fp['supplier']['company_name'] ?? $fp['supplier']['name']) : '' ?></p>
                                <div class="product-link d-flex justify-content-between align-items-center">
                                    <a href="<?= base_url('product/detail/' . $fp['id']) ?>">Learn More</a> <a href="<?= base_url('product/detail/' . $fp['id']) ?>"><img src="<?= base_url('assets/images/down-arrow.svg') ?>"></a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">No featured products yet.</p>
                    <?php endif; ?>
                </div>

            </div>
            <div class="product-bg-img <?= $group['bgClass'] ?>">
               <div class="product-bg-content">
                 <p class="mb-1 text-center text-white">Product</p>
                <h2 class="text-center text-white"><?= esc($group['category']['name']) ?></h2>
               </div>
            </div>

        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($gi === 0): ?>
<section class="mt-5 pt-5 pb-5">
    <div class="container">
        <h2 class="text-center">Export Globally to Millions of Verified <br>
Buyers at B2BTradeServices
</h2>
        <div class="row mt-5 icon-boxes-row">
            <div class="icon-box-col">
                <div class="icon-box-img text-center">
                    <img src="<?= base_url('assets/images/icon-1.svg') ?>">
                    <h2 class="mt-3">1.8M+</h2>
                    <p>Buyers</p>
                </div>
            </div>
            <div class="icon-box-col">
                <div class="icon-box-img text-center">
                    <img src="<?= base_url('assets/images/icon-2.svg') ?>">
                    <h2 class="mt-3">2M+</h2>
                    <p>Products</p>
                </div>
            </div>
            <div class="icon-box-col">
                <div class="icon-box-img text-center">
                    <img src="<?= base_url('assets/images/icon-3.svg') ?>">
                    <h2 class="mt-3">30K+</h2>
                    <p>Suppliers</p>
                </div>
            </div>
            <div class="icon-box-col">
                <div class="icon-box-img text-center">
                    <img src="<?= base_url('assets/images/icon-4.svg') ?>">
                    <h2 class="mt-3">300+</h2>
                    <p>Categories </p>
                </div>
            </div>
            <div class="icon-box-col">
                <div class="icon-box-img text-center">
                    <img src="<?= base_url('assets/images/icon-5.svg') ?>">
                    <h2 class="mt-3">100+</h2>
                    <p>Countries & Regions</p>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($gi === 1): ?>
<section class="mt-5 pt-lg-3">
    <div class="container">
        <div class="right-supplier-sec">
            <div class="row align-items-center">
                <div class="supplier-content-sec">
                    <h2 class="text-white">Get Matched with the Right Suppliers — Instantly</h2>
                    <ul class="check-icon-ul">
                        <li class="text-white"> Receive fast quotes from trusted suppliers</li>
                        <li class="text-white"> Submit once, compare multiple offers</li>
                        <li class="text-white"> Access suppliers worldwide</li>
                        <li class="text-white"> Smart RFQ-based business matching</li>
                    </ul>
                </div>
                <div class="supplier-contact-form">
                    <div class="multiple-quote-form">
                <h2 class=" text-center">Get Multiple Quotes</h2>
                <form class="mt-3" action="<?= base_url('contact/submit') ?>" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="form_type" value="quote">
                    <input type="hidden" name="source_page" value="homepage-suppliers-section">
                    <input type="hidden" name="lead_type" value="buyer">
                    <div class="tripple-input">
                         <div class="form-input">
                        <input type="text" name="name" placeholder="Name*" required>
                    </div>
                        <div class="form-input">
                        <input type="email" name="email" placeholder="Email*" required>
                    </div>
                      <div class="form-input">
                        <input type="tel" name="phone" class="phone" placeholder="Phone*" required>
                    </div>
                    </div>
                      <div class="dual-input">
                        <div class="form-input">
                     <select name="country_id" class="form-control" style="height:45px; border-radius:40px; border:1px solid #ddd; padding:0 12px;" required>
                        <option value="">Select Country*</option>
                        <?php if (isset($countries)): ?>
                            <?php foreach ($countries as $country): ?>
                                <option value="<?= $country['id'] ?>"><?= esc($country['name']) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    </div>
                      <div class="form-input">
                        <input type="number" name="quantity" placeholder="Quantity*" required>
                    </div>
                    </div>
                    <div class="form-textarea">
                        <textarea name="message" placeholder="What are you looking for?"></textarea>
                    </div>
                    <div class="submit-btn-gradient mt-2">
                        <button type="submit" class="gradeint-cta">Submit Now</button>
                    </div>
                </form>
            </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="mt-5 pt-4 pb-4">
    <div class="container">
            <h2 class="text-center">Browse Our Categories</h2>
            <p class="text-center">Explore suppliers and products across all categories to find the best match for your business needs.</p>
        <div class="category-slider-sec">
             <div class="category-slider">
                    <div>
                       <div class="row">
                        <?php if (isset($categories) && !empty($categories)): ?>
                            <?php foreach (array_slice($categories, 0, 10) as $catIdx => $cat): ?>
                            <div class="category-icon-box justify-content-center d-flex align-items-center gap-2">
                                <?php if (!empty($cat['image'])): ?>
                                    <img src="<?= base_url('uploads/categories/' . $cat['image']) ?>">
                                <?php else: ?>
                                    <img src="<?= base_url('assets/images/category-icon-' . (($catIdx % 10) + 1) . '.svg') ?>">
                                <?php endif; ?>
                                <?= esc($cat['name']) ?>
                                <a href="<?= base_url('supplier-category/' . $cat['slug']) ?>"></a>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                       </div>
                    </div>
                    <?php if (isset($categories) && count($categories) > 10): ?>
                    <div>
                       <div class="row">
                        <?php foreach (array_slice($categories, 10, 10) as $catIdx => $cat): ?>
                            <div class="category-icon-box justify-content-center d-flex align-items-center gap-2">
                                <?php if (!empty($cat['image'])): ?>
                                    <img src="<?= base_url('uploads/categories/' . $cat['image']) ?>">
                                <?php else: ?>
                                    <img src="<?= base_url('assets/images/category-icon-' . (($catIdx % 10) + 1) . '.svg') ?>">
                                <?php endif; ?>
                                <?= esc($cat['name']) ?>
                                <a href="<?= base_url('supplier-category/' . $cat['slug']) ?>"></a>
                            </div>
                        <?php endforeach; ?>
                       </div>
                    </div>
                    <?php endif; ?>
                </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php endforeach; ?>


<section class="mt-5 pt-md-5">
    <div class="container">
            <div class="flag-slider-sec">
                <h2 class="text-center">Regional Channel</h2>
                <p class="text-center">Find Suppliers by Country or Region </p>
                <div class="flag-slider mt-4 pt-md-3">
                    <?php if (isset($countries) && !empty($countries)): ?>
                        <?php $countryChunks = array_chunk($countries, 7); ?>
                        <?php foreach (array_slice($countryChunks, 0, 3) as $chunk): ?>
                        <div>
                            <div class="row">
                                <?php foreach ($chunk as $c): ?>
                                <a href="<?= base_url('supplier-country/' . $c['code']) ?>" class="flag-slider-box text-center text-decoration-none text-dark">
                                    <?php if (!empty($c['flag'])): ?>
                                        <img src="<?= base_url('assets/images/flags/' . $c['flag']) ?>">
                                    <?php else: ?>
                                        <div style="width: 60px; height: 40px; background: #f0f0f0; border-radius: 4px; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                                            <span style="font-size: 12px; color: #999;"><?= esc(strtoupper($c['code'] ?? '')) ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <p><?= esc($c['name']) ?></p>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div>
                            <div class="row">
                                <p class="text-center text-muted">No countries available yet.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
    </div>
</section>


<section class="success-stories-sec mt-5 pt-lg-4">
    <div class="container">
        <div class="row ">
<div class="multiple-quote-form">
                <h2 class="text-white text-center">Find us</h2>
                <form action="<?= base_url('contact/submit') ?>" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="form_type" value="contact">
                    <input type="hidden" name="source_page" value="homepage-find-us">
                    <input type="hidden" name="lead_type" value="buyer">
                    <div class="form-input">
                        <input type="text" name="name" placeholder="Name*" required>
                    </div>
                    <div class="dual-input">
                        <div class="form-input">
                        <input type="email" name="email" placeholder="Email*" required>
                    </div>
                      <div class="form-input">
                        <input type="tel" name="phone" class="phone" placeholder="Phone*" required>
                    </div>
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
                    <a href="<?= base_url('success-stories') ?>" class="view-all-link d-flex align-items-center gap-2"> View All <img src="<?= base_url('assets/images/arrow-right.svg') ?>"></a>
                </div>
                <div class="success-stories-slider-sec">
                    <div class="success-stories-slider">
                        <div>
                            <p>“B2B Trade Services helped us connect with relevant buyers efficiently. My Business Consultant Manager (BCM) they provided makes it easy to find and filter genuine business leads.
<br><br>Their additional services such as website development, catalog design, and LLC formation are extremely valuable. I would recommend suppliers to try their services, as the platform offers a refreshing approach compared to traditional B2B marketplaces.”
 </p>
                                <p class="client-name mb-1">Mr. Johnson – CEO & Founder</p>
                                <p class="success-stories-compay-name mb-0">SATIN PACKAGES LIMITED</p>
</div>
   <div>
                            <p>“B2B Trade Services has been a valuable platform for expanding our international business network. We started receiving inquiries from buyers in different regions shortly after listing our products.
<br><br>The platform’s tools for connecting with potential buyers and managing inquiries are very useful. It has helped us explore new markets and build meaningful business relationships with companies worldwide.”
</p>
                                <p class="client-name mb-1">Michael Carter – Export Manager</p>
                                <p class="success-stories-compay-name mb-0">GlobalTech Industrial Supplies</p>
</div>
   <div>
                            <p>“Joining B2B Trade Services has helped increase our product visibility among international buyers. The platform makes it easier to present our products and communicate with companies looking for reliable suppliers.
<br><br>We appreciate the professional environment and the opportunities it creates for businesses to connect and grow globally.”
</p>
                                <p class="client-name mb-1">Emily Rodriguez – Sales Director</p>
                                <p class="success-stories-compay-name mb-0">BrightStar Steel Solutions</p>
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
        <p>B2B Trade Services LLC is a global B2B marketplace with an extensive experience in wholesale trade solution. The platform connects verified buyers and suppliers worldwide, simplifying bulk sourcing through innovative e-commerce solutions. With intuitive web and mobile experiences, dedicated support, and trusted traders, B2B Trade Services delivers a seamless, reliable, and efficient global trading experience.

</p>
<a href="<?= base_url('about-us') ?>" class="read_more_link">Read More</a>

    </div>
</section>


<?= $this->endSection() ?>
