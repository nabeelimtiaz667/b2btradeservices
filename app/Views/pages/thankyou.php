<?= $this->extend('layouts/inner') ?>

<?= $this->section('content') ?>
<section class="inner-banner-sec mt-4">
    <div class="container">
        <div class="inner-banner-img">
            <img src="<?= base_url('assets/images/contact-us-banner.webp') ?>" class="w-100">
        </div>
    </div>
</section>


<div class="container static-content mt-4 textcenter">

    <!-- Privacy Policy Section -->
   <!-- Privacy Policy Section -->
<section class="privacy-policy">
    <h1 class="text-center">Thank You</h1>
    <h3  class="text-center">One of our representatives will reach out to you shortly.</h3>
  

    <p  class="text-center"> 
     If you’d like to speak with someone immediately, please email us at <?= esc($siteSettings['contact_email'] ?? 'info@b2btradeservices.com') ?>.
    </p>

   
</section>


	

</div>

<?= $this->endSection() ?>
