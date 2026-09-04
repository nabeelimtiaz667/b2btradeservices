<?= $this->extend('layouts/inner') ?>

<?= $this->section('content') ?>

<?php
    $galleryImages = [];
    if (!empty($product['main_image'])) {
        $galleryImages[] = base_url('uploads/products/' . $product['main_image']);
    }
    if (!empty($product['gallery_images'])) {
        $gallery = json_decode($product['gallery_images'], true);
        if (is_array($gallery)) {
            foreach ($gallery as $img) {
                $galleryImages[] = base_url('uploads/products/' . $img);
            }
        }
    }
    if (empty($galleryImages)) {
        $galleryImages[] = base_url('assets/images/supplier-product-img-1.webp');
    }
?>

<section class="single-product-page mt-5">
    <div class="container">
        <div class="row">
            <div class="left-single-product">
                <div class="row align-items-center">
                    <div class="single-product-image">
                        <div class="product-gallery">
                            <div class="main-image-container" id="mainContainer">
                                <span class="arrow left" id="prev">&#10094;</span>
                                <img src="<?= $galleryImages[0] ?>" id="mainImage" alt="<?= esc($product['name'] ?? 'Product Image') ?>">
                                <span class="arrow right" id="next">&#10095;</span>
                            </div>
                            <?php if (count($galleryImages) > 1): ?>
                            <div class="thumbnails">
                                <?php foreach ($galleryImages as $idx => $img): ?>
                                    <img src="<?= $img ?>" class="thumb <?= $idx === 0 ? 'active' : '' ?>" alt="Thumb <?= $idx + 1 ?>">
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="product_details_single">
                        <h1 class="h2"><?= esc($product['name'] ?? '') ?></h1>
                        <?php if (!empty($product['min_order_quantity'])): ?>
                        <div class="d-flex mt-3">
                            <p>MOQ</p>
                            <p><?= esc($product['min_order_quantity']) ?> <?= esc($product['min_order_unit'] ?? 'Units') ?></p>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($product['port'])): ?>
                        <div class="d-flex">
                            <p>Port</p>
                            <p><?= esc($product['port']) ?></p>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($product['packaging'])): ?>
                        <div class="d-flex">
                            <p>Packaging</p>
                            <p><?= esc($product['packaging']) ?></p>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($product['delivery_time'])): ?>
                        <div class="d-flex">
                            <p>Lead Time</p>
                            <p><?= esc($product['delivery_time']) ?></p>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($product['payment_terms'])): ?>
                        <div class="d-flex">
                            <p>Payment Terms</p>
                            <p><?= esc($product['payment_terms']) ?></p>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($product['price_range'])): ?>
                        <div class="d-flex">
                            <p>Price Range</p>
                            <p><?= esc($product['price_range']) ?></p>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($product['supply_ability'])): ?>
                        <div class="d-flex border-bottom-0">
                            <p>Supply Ability</p>
                            <p><?= esc($product['supply_ability']) ?></p>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($product['supplier'])): ?>
                        <a class="gradeint-cta mt-4" href="<?= base_url('supplier/profile/' . ($product['supplier']['slug'] ?? $product['supplier']['id'])) ?>">Contact</a>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!empty($product['specifications'])): ?>
                <div class="product-quick-details">
                    <h2 class="f-16 light-green-h2-color">Quick Details</h2>
                    <div class="column-count-2 product-quick-details-list mt-3">
                        <div class="d-flex">
                            <p><b>Product Name</b></p>
                            <p><?= esc($product['name']) ?></p>
                        </div>
                        <?php if (!empty($product['category'])): ?>
                        <div class="d-flex">
                            <p><b>Category</b></p>
                            <p><?= esc($product['category']['name']) ?></p>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($product['certifications'])): ?>
                        <div class="d-flex">
                            <p><b>Certifications</b></p>
                            <p><?= esc($product['certifications']) ?></p>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($product['min_order_quantity'])): ?>
                        <div class="d-flex">
                            <p><b>Min. Order</b></p>
                            <p><?= esc($product['min_order_quantity']) ?> <?= esc($product['min_order_unit'] ?? 'Units') ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="product-main-details">
                    <h2 class="f-16 light-green-h2-color">Product Details</h2>
                    <?php if (!empty($product['description'])): ?>
                        <p><?= nl2br(esc($product['description'])) ?></p>
                    <?php endif; ?>

                    <?php if (!empty($product['specifications'])): ?>
                    <h2 class="f-16 light-green-h2-color mt-3">Specifications</h2>
                    <p><?= nl2br(esc($product['specifications'])) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="right-single-product">
                <?php if (!empty($product['supplier'])): ?>
                <div class="supplier-details-single-page">
                    <div class="d-flex justify-content-between mb-4">
                        <h2 class="mb-0 f-18"><a href="<?= base_url('supplier/profile/' . ($product['supplier']['slug'] ?? $product['supplier']['id'])) ?>" style="color: inherit; text-decoration: none;"><?= esc($product['supplier']['company_name'] ?? $product['supplier']['name'] ?? 'Supplier') ?></a></h2>
                        <?php if (isset($product['supplier']['membership_level'])): ?>
                        <div class="sp-membership-icon">
                            <?php if ($product['supplier']['membership_level'] == 'free'): ?>
                                <img src="<?= base_url('assets/images/free-membership-coin.webp') ?>" style="width: 40px;">
                            <?php elseif ($product['supplier']['membership_level'] == 'starter'): ?>
                                <img src="<?= base_url('assets/images/starter-coin.webp') ?>" style="width: 40px;">
                            <?php elseif ($product['supplier']['membership_level'] == 'gold'): ?>
                                <img src="<?= base_url('assets/images/gold-coin.webp') ?>" style="width: 40px;">
                            <?php elseif ($product['supplier']['membership_level'] == 'platinum'): ?>
                                <img src="<?= base_url('assets/images/palti-coin.webp') ?>" style="width: 40px;">
                            <?php elseif ($product['supplier']['membership_level'] == 'vip'): ?>
                                <img src="<?= base_url('assets/images/vip-coin.webp') ?>" style="width: 40px;">
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($product['supplier']['city']) || !empty($product['supplier']['country'])): ?>
                    <p><?= esc($product['supplier']['city'] ?? '') ?><?= !empty($product['supplier']['country']['name'] ?? '') ? ', ' . esc($product['supplier']['country']['name']) : '' ?></p>
                    <?php endif; ?>
                    <?php if (!empty($product['supplier']['country']['name'] ?? '')): ?>
                    <p class="d-flex gap-3 mb-0"><?= esc($product['supplier']['country']['name'] ?? '') ?>
                        <?php if (!empty($product['supplier']['country']['flag'] ?? '')): ?>
                        <img src="<?= esc($product['supplier']['country']['flag']) ?>" width="20" onerror="this.style.display='none'">
                        <?php endif; ?>
                    </p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <div class="multiple-quote-form mb-5">
                    <h2 class="text-white text-center f-18">Get Free Quotes From Multiple Sellers</h2>
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success text-center"><?= session()->getFlashdata('success') ?></div>
                    <?php endif; ?>
                    <form action="<?= base_url('contact/submit') ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="form_type" value="product_quote">
                        <input type="hidden" name="source_page" value="product-detail">
                        <input type="hidden" name="source_id" value="<?= $product['id'] ?? '' ?>">
                        <input type="hidden" name="lead_type" value="buyer">
                        <div class="form-input">
                            <input type="text" name="name" placeholder="Name" required>
                        </div>
                        
                            <div class="form-input">
                                <input type="email" name="email" placeholder="Email*" required>
                            </div>
                            <div class="form-input mb-10">
                                <input type="tel" name="phone" class="phone" placeholder="Phone*" required>
                            </div>
                      
                       
                            <div class="form-input">
                                <input type="text" name="industry" placeholder="Industry (Optional)">
                            </div>
                            <div class="form-input">
                                <input type="number" name="quantity" placeholder="Quantity*" required>
                            </div>
                        
                        <div class="form-textarea">
                            <textarea name="message" placeholder="What are you looking for?"></textarea>
                        </div>
                        <div class="submit-btn">
                            <button type="submit">Submit Now</button>
                        </div>
                    </form>
                </div>

                <?php if (!empty($supplierProducts)): ?>
                <h2 class="f-16 light-green-h2-color">More Products From this Supplier</h2>
                <?php foreach ($supplierProducts as $sp): ?>
                <div class="top-products-box mb-4">
                    <div class="top-products-img">
                        <img src="<?= !empty($sp['main_image']) ? base_url('uploads/products/' . $sp['main_image']) : base_url('assets/images/supplier-product-img-1.webp') ?>" class="w-100">
                    </div>
                    <div class="top-products-content">
                        <h3><?= esc($sp['name']) ?></h3>
                        <p><?= !empty($sp['category']) ? esc($sp['category']['name']) : '' ?></p>
                        <div class="product-link d-flex justify-content-between align-items-center">
                            <a href="<?= base_url('product/detail/' . $sp['id']) ?>">Learn More</a>
                            <a href="<?= base_url('product/detail/' . $sp['id']) ?>"><img src="<?= base_url('assets/images/down-arrow.svg') ?>"></a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mainImage = document.getElementById('mainImage');
    const thumbs = document.querySelectorAll('.thumb');
    const prevBtn = document.getElementById('prev');
    const nextBtn = document.getElementById('next');
    let currentIndex = 0;
    const images = [];

    thumbs.forEach(function(thumb, index) {
        images.push(thumb.src);
        thumb.addEventListener('click', function() {
            currentIndex = index;
            updateMainImage();
        });
    });

    if (images.length === 0 && mainImage) {
        images.push(mainImage.src);
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            currentIndex = (currentIndex - 1 + images.length) % images.length;
            updateMainImage();
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            currentIndex = (currentIndex + 1) % images.length;
            updateMainImage();
        });
    }

    function updateMainImage() {
        if (mainImage && images[currentIndex]) {
            mainImage.src = images[currentIndex];
            thumbs.forEach(function(t, i) {
                t.classList.toggle('active', i === currentIndex);
            });
        }
    }
});
</script>

<?= $this->endSection() ?>
