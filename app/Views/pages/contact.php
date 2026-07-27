<?= $this->extend('layouts/inner') ?>

<?= $this->section('styles') ?>
<style>
    .office-locations {
        display: flex;
        gap: 30px;
        margin-bottom: 50px;
    }
    .location-info {
        flex: 1;
    }
    .address-card {
        background-image: linear-gradient(45deg, #15A2A0, #5FC86B);
        color: white;
        padding: 30px;
        border-radius: 4px 4px 0 0;
        position: relative;
    }
    .address-card .flag {
        position: absolute;
        top: 20px;
        right: 20px;
        text-align: center;
    }
    .address-card .flag img {
        width: 30px;
        display: block;
        margin-bottom: 5px;
    }
    .address-card h3 {
        margin: 0 0 10px 0;
        font-size: 18px;
    }
    .address-card p {
        margin: 0 0 20px 0;
        font-size: 14px;
        max-width: 80%;
    }
    .phone-info {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .phone-icon {
        background-color: var(--accent-orange);
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 18px;
    }
    .phone-numbers {
        font-size: 14px;
        font-weight: bold;
    }
    .map-container {
        height: 300px;
        background-color: #e0e0e0;
        border-radius: 0 0 4px 4px;
        overflow: hidden;
    }
    .map-container iframe {
        width: 100%;
        height: 100%;
        border: none;
    }
    .contact-form-container {
        flex: 1;
        background: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .contact-form-container h3 {
        margin-top: 0;
        color: var(--dark-green);
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 10px;
        margin-bottom: 20px;
    }
    .help-section {
        border-top: 1px solid var(--border-color);
        padding-top: 40px;
    }
    .help-block {
        margin-bottom: 30px;
    }
    .help-block h2 {
        color: var(--dark-green);
        margin-bottom: 10px;
        position: relative;
        display: inline-block;
    }
    .help-block h2::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: -3px;
        width: 100%;
        height: 2px;
        background-color: var(--primary-teal);
    }
    .help-block p {
        margin: 5px 0;
        font-size: 14px;
        color: #555;
    }
    .help-block a {
        color: var(--accent-orange);
        text-decoration: none;
        font-weight: bold;
    }
    .action-buttons {
        display: flex;
        gap: 15px;
        margin-top: 20px;
    }
    .btn-action {
        padding: 10px 25px;
        border-radius: 20px;
        color: white !important;
        text-decoration: none;
        font-size: 14px;
        font-weight: bold;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: transform 0.2s;
        background-image: linear-gradient(45deg, #15A2A0, #5FC86B);
        position: relative;
        overflow: hidden;
    }
    .btn-action::before {
        content: "";
        position: absolute;
        top: 0;
        left: -100%;
        width: 60%;
        height: 100%;
        background: linear-gradient(120deg, transparent, rgba(255, 255, 255, 0.6), transparent);
        transition: 0.9s;
    }
    .btn-action:hover::before {
        left: 120%;
    }
    .btn-action:hover {
        background-image: linear-gradient(270deg, #15A2A0, #5FC86B);
        transition: .5s linear;
    }
    .office-locations input, .office-locations textarea {
        border: 1px solid #DBDBDB;
    }
    .submit-btn-gradient button {
        max-width: 100%;
        margin-top: 20px;
    }
    @media (max-width: 768px) {
        .office-locations {
            flex-direction: column;
        }
        .action-buttons {
            flex-wrap: wrap;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="inner-banner-sec mt-4">
    <div class="container">
        <div class="inner-banner-img">
            <img src="<?= base_url('assets/images/welcome-banner.webp') ?>" class="w-100">
        </div>
    </div>
</section>

<div class="container">
    <div class="section-header mt-5">
        <h2>Office Location</h2>
    </div>

    <div class="office-locations">
        <div class="location-info">
            <div class="address-card">
                <div class="flag">
                    <img src="https://flagcdn.com/w40/us.png" alt="USA Flag">
                    <span style="font-size: 10px; display: block;">USA</span>
                </div>
                <h3>Address</h3>
                <p>1001 S MAIN STREET, STE 500 - Kalispell, MONTANA, UNITED STATES 59901</p>

                <!--<div class="phone-info">-->
                <!--    <div class="phone-icon">-->
                <!--        <i class="fas fa-phone-alt"></i>-->
                <!--    </div>-->
                <!--    <div class="phone-numbers">-->
                <!--        <div>xxx xxxx xxxx (International)</div>-->
                <!--        <div>xxx xxxx xxxx (Toll Free)</div>-->
                <!--    </div>-->
                <!--</div>-->
            </div>
            <div class="map-container">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d85076.77350267995!2d-114.4060510456962!3d48.213373059824924!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x536650959ceac247%3A0xaf1fbdda1d5abb62!2sKalispell%2C%20MT%2059901%2C%20USA!5e0!3m2!1sen!2s!4v1767893060497!5m2!1sen!2s" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>

        <div class="contact-form-container">
            <h3>Contact Us</h3>
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>
            <form action="<?= base_url('contact/submit') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="form_type" value="contact">
                <input type="hidden" name="source_page" value="contact-page">
                <input type="hidden" name="lead_type" value="supplier">
                <div class="form-input">
                    <input type="text" name="name" placeholder="Name*" required>
                </div>
                <div class="form-input">
                    <input type="email" name="email" placeholder="Email*" required>
                </div>
                <div class="form-input mb-2">
                    <input type="tel" name="phone" class="phone" placeholder="Phone number*" required>
                </div>
                <div class="form-input">
                    <input type="text" name="company" placeholder="Subject*" required>
                </div>
                <div class="form-textarea">
                    <textarea name="message" placeholder="Message"></textarea>
                </div>
                <div class="submit-btn-gradient">
                    <button type="submit" class="gradeint-cta w">Send Message</button>
                </div>
            </form>
        </div>
    </div>

    <div class="help-section mb-5">
        <div class="help-block">
            <h2>How Can We Help?</h2>
            <p>Customer Support staff will get back to you shortly!</p>
            <p>Please fill the form by specifying your concern alongside the necessary contact information.</p>
        </div>

        <div class="help-block d-none">
            <h2>Need Instant Help?</h2>
            <p><a href="#">Click here</a> and start talking with one of our representatives.</p>
        </div>

        <div class="help-block">
            <h2>Visit our Office</h2>
            <p><strong>US Office</strong></p>
            <p>1001 S MAIN STREET, STE 500 - Kalispell, MONTANA, UNITED STATES 59901</p>
            <p><strong>Reach out to us via Email</strong></p>
            <p>Send us an email at <a href="mailto:<?= esc($siteSettings['contact_email'] ?? 'info@b2btradeservices.com') ?>" style="color: var(--primary-teal);"><?= esc($siteSettings['contact_email'] ?? 'info@b2btradeservices.com') ?></a> and share your concern.</p>
        </div>

        <div class="action-buttons">
            <a href="mailto:<?= esc($siteSettings['contact_email'] ?? 'info@b2btradeservices.com') ?>" class="btn-action btn-mail">
                <i class="fas fa-envelope"></i> Mail Us
            </a>
            <!--<a href="tel:" class="btn-action btn-call">-->
            <!--    <i class="fas fa-phone-alt"></i> Call Us-->
            <!--</a>-->
            <!--<a href="#" class="btn-action btn-chat">-->
            <!--    <i class="fas fa-comments"></i> Live Chat-->
            <!--</a>-->
        </div>
    </div>
</div>
<?= $this->endSection() ?>
