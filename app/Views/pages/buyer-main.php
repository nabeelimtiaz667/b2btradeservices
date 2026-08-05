<?= $this->extend('layouts/inner') ?>

<?= $this->section('content') ?>
<section class="inner-banner-sec mt-4">
    <div class="container">
        <div class="inner-banner-img">
            <img src="<?= base_url('assets/images/contact-us-banner.webp') ?>" class="w-100">
        </div>
    </div>
</section>

<section class="supplier-page-sec mt-5">
    <div class="container">
        <h1 class="text-center h2">Find the most Authentic, Relevant and Recent Buying Leads/ Importers/ RFQ's</h1>
        
        
      <div class="searchbar-box-shadow mb-5">  <div class="searchbar-box  mt-0">
            <form action="<?= base_url('buyer/search') ?>" method="get">
                <div class="searchbar-input">
                    <img src="<?= base_url('assets/images/search.svg') ?>">
                    <input type="search" name="q" placeholder="What are you looking for?" value="<?= isset($searchKeyword) ? esc($searchKeyword) : '' ?>">
                    <button type="submit" class="outline-btn search-btn btn">Find Buyer</button>
                </div>
                <div id="moreOptions" class="options">
                    <div class="double-input">
                        <div class="form-input filter-icon-select filter-by-date">
                            <input type="date" class="date" name="date" placeholder="Filter by Date">
                        </div>
                        <div class="form-input">
                            <select class="form-control country-select country-icon-select filter-icon-select" name="country">
                                <option value="" selected>Country</option>
                                <?php if (isset($countries)): ?>
                                    <?php foreach ($countries as $country): ?>
                                        <option value="<?= $country['id'] ?>"><?= esc($country['name']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        
                    </div>
                </div>
                <button id="toggleBtn" class="toggle-btn" type="button" onclick="toggleOptions()">
                    <span id="btnText">More Options</span>
                    <img id="arrowIcon" src="<?= base_url('assets/images/arrow-down.svg') ?>" width="12" alt="arrow" />
                </button>
            </form>
        </div></div>

        <div class="row mt-md-5 mt-4 align-items-start buyer-row-rev">
            <div class="supplier-side-form supplier-side-form-sub-page">
                <div class="multiple-quote-form mt-lg-0 mt-5 mb-5">
                    <h2 class="text-white text-center mb-3">Connect with 03 Free Buyers Now</h2>
                    <form action="<?= base_url('register') ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="role" value="buyer">
                        <div class="form-input">
                            <input type="text" placeholder="Name*" name="name" required>
                        </div>
                        <div class="form-input">
                            <input type="email" placeholder="Email*" name="email" required>
                        </div>
                        <div class="form-input">
                            <input type="tel" placeholder="Phone*" class="phone" name="phone" required>
                        </div>
                        <div class="form-input password-input mt-2">
                            <input type="password" class="password" placeholder="Password*" name="password" required>
                            <i class="eye mt-2 pt-1" onclick="togglePassword()"><svg style="fill: #DBDBDB" class="eye-icon" height="20" viewBox="0 0 24 24" width="20" xmlns="http://www.w3.org/2000/svg"><path clip-rule="evenodd" d="m12 4.5c-5 0-9.27 3.11-11 7.5 1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zm0 12.5c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"></path></svg></i>
                        </div>
                        <div class="submit-btn">
                            <button type="submit">Submit Now</button>
                        </div>
                    </form>
                </div>

                <h2 class="mb-2 text-center light-green-h2-color">Featured Supplier</h2>
                <?php if (isset($featuredSuppliers) && count($featuredSuppliers) > 0): ?>
                    <?php foreach ($featuredSuppliers as $fs): ?>
                        <div class="top-products-box">
                            <div class="top-products-img featured-supplier-logo-wrap">
                                <?php if (!empty($fs['company_logo'])): ?>
                                    <img src="<?= base_url('uploads/suppliers/' . $fs['company_logo']) ?>" class="w-100 featured-supplier-logo" alt="<?= esc($fs['company_name'] ?? ($fs['name'] ?? '')) ?>">
                                <?php else: ?>
                                    <img src="<?= base_url('assets/images/supplier-img-9.webp') ?>" class="w-100">
                                <?php endif; ?>
                            </div>
                            <div class="top-products-content">
                                <h3><?= esc($fs['company_name'] ?? ($fs['name'] ?? '')) ?></h3>
                                <p><?= esc($fs['country']['name'] ?? '') ?></p>
                                <div class="product-link d-flex justify-content-between align-items-center">
                                    <a href="<?= base_url('supplier/profile/' . ($fs['slug'] ?? $fs['id'])) ?>">Learn More</a>
                                    <a href="<?= base_url('supplier/profile/' . ($fs['slug'] ?? $fs['id'])) ?>"><img src="<?= base_url('assets/images/down-arrow.svg') ?>"></a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="supplier-product-list">
                <div id="buyerWrapper">
                    <?php if (isset($inquiries) && count($inquiries) > 0): ?>
                        <?php foreach ($inquiries as $inquiry): ?>
                            <div class="buyer-main-list move-on-hover">
                                <div class="d-flex justify-content-between">
                                    <div class="buyer-main-list-content">
                                        <h2 class="f-16 light-green-h2-color"><a href="<?= inquiry_url($inquiry) ?>" class="text-decoration-none light-green-h2-color"><?= esc($inquiry['title']) ?></a></h2>
                                        <div class="d-flex justify-content-between mt-3 gap-15">
                                            <div>
                                                <p><b>Quantity Required:</b> <br />
                                                <span class="light-green-h2-color"> <?= esc($inquiry['quantity'] ?? 'To be Finalized') ?> <?= esc($inquiry['unit'] ?? '') ?></span></p>
                                            </div>
                                            <div>
                                                <p class=" gap-1"><b>Posted In:</b> <br />
                                                <?= isset($inquiry['country']) ? esc($inquiry['country']['name']) : 'N/A' ?></p>
                                            </div>
                                            <div>
                                                <p><b>Date Posted:</b>  <br />
                                                <?= isset($inquiry['created_at']) ? date('d M, Y', strtotime($inquiry['inquiry_date'])) : 'N/A' ?></p>
                                            </div>
                                        </div>
                                        <p><?= esc(substr($inquiry['description'] ?? '', 0, 200)) ?>...</p>
                                    </div>
                                    <div class="buyer-main-list-cta-side text-center">
                                        <p class="font-weight-600 text-white"><?= esc($inquiry['buyer_name'] ?? 'Buyer') ?></p>
                                        <?php if (isset($inquiry['country']) && $inquiry['country']): ?>
                                            <img src="<?= base_url('assets/images/flags/flag_' . str_replace(' ', '_', $inquiry['country']['name']) . '.svg') ?>" width="24" onerror="this.style.display='none'" alt="<?= esc($inquiry['country']['name']) ?>">
                                        <?php endif; ?>
                                        <a class="solid-btn mt-3" href="<?= inquiry_url($inquiry) ?>">Contact Buyer</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center p-5">
                            <p>No buyer inquiries found. Please try a different search.</p>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if (isset($pager) && $pager): ?>
                    <div class="d-flex justify-content-center mt-5 " style="border-top: 1px solid #e1e1e1;">
                        <?= $pager->links('buyer', 'default_full') ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<section class="mt-5 pt-md-5 pb-4 mb-5">
    <div class="container">
        <h3 class="text-center custom-h3">Browse Inquiries <br>By Category</h3>
        <div class="category-slider-sec mt-md-5 mt-3">
            <div class="category-slider">
                <div>
                    <div class="row">
                        <?php if (isset($categories)): ?>
                            <?php foreach ($categories as $cat): ?>
                                <div class="category-icon-box justify-content-center d-flex align-items-center gap-2">
                                    <img src="<?= base_url('assets/images/category-icon-1.svg') ?>"><?= esc($cat['name']) ?>
                                    <a href="<?= base_url('buyers/' . ($cat['slug'] ?? $cat['id'])) ?>"></a>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
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
<?= $this->endSection() ?>
