<?= $this->extend('layouts/inner') ?>

<?= $this->section('content') ?>
<section class="inner-banner-sec mt-4">
    <div class="container">
        <div class="inner-banner-img">
            <img src="<?= base_url('assets/images/contact-us-banner.webp') ?>" class="w-100">
        </div>
    </div>
</section>

<div class="container static-content">

<section class="privacy-policy">
    <h1>Privacy Policy</h1>

    <p><strong>www.b2btradeservices.com</strong></p>

    <p>
        Welcome to www.b2btradeservices.com. Your privacy matters to us. 
        If you have landed on this page, it means you are looking for transparency. 
        This Privacy Policy explains how we collect, use, share, and protect your information 
        when you use our website and services.
    </p>

    <h2>1. Information We Collect</h2>
    <p>We may collect personal or technical information when you use our site, including:</p>

    <h3>● Personal Details</h3>
    <p>
        Information you provide directly, such as your name, email, phone number, 
        company name, and any form data you submit.
    </p>

    <h3>● Usage Data</h3>
    <p>
        Technical data like your IP address, browser type, pages visited, 
        time spent on pages, device information, and interaction patterns.
    </p>

    <h3>● Cookies & Tracking Technologies</h3>
    <p>
        We use cookies, web beacons, and similar tools to improve your experience, 
        track site activity, and personalize content.
    </p>

    <h3>● Third-Party Data (Optional)</h3>
    <p>
        If you link or log in through external services (e.g., Google, LinkedIn), 
        we may collect basic profile information that you permit.
    </p>

    <h2>2. How We Use Your Data</h2>
    <p>We use your information for the following purposes:</p>

    <ul>
        <li><strong>To provide and improve our services:</strong> 
            Account setup, communication, service delivery, and tailored user experience.</li>

        <li><strong>Communication & Support:</strong> 
            Sending service updates, alerts, support messages, and notifications.</li>

        <li><strong>Analytics & Security:</strong> 
            Monitoring site performance, detecting fraud, and ensuring a secure environment.</li>

        <li><strong>Marketing (with consent):</strong> 
            With your permission, we may send promotional messages. You may opt out at any time.</li>
    </ul>

    <h2>3. Data Sharing and Disclosure</h2>

    <p><strong>We will never sell your personal data.</strong></p>

    <p>Your information may be shared:</p>

    <ul>
        <li>Internally, within our team for service delivery.</li>
        <li>With other users, when you choose to interact or communicate through the platform.</li>
        <li>For legal reasons, if required by law or to protect rights, safety, and property.</li>
    </ul>

    <h2>4. Cookies & Tracking</h2>
    <p>We use cookies and similar technologies for:</p>

    <ul>
        <li><strong>Essential site functions:</strong> Enable login and security features.</li>
        <li><strong>Performance analysis:</strong> Understand how users browse and use our platform.</li>
        <li><strong>Personalization:</strong> Remember your preferences and improve your experience.</li>
    </ul>

    <p>
        You can control or disable cookies via your browser settings. 
        However, some features may not function properly if cookies are disabled.
    </p>

    <h2>5. Data Security</h2>
    <p>
        We implement reasonable safeguards to protect your data from unauthorized access, 
        alteration, or loss. However, no online service can guarantee absolute security.
    </p>

    <h2>6. Your Rights</h2>
    <p>As a user, you may:</p>

    <ul>
        <li><strong>Access your personal data:</strong> Request a copy of the information held about you.</li>
        <li><strong>Correct or update your data.</strong></li>
        <li><strong>Delete your account and data:</strong> Request deletion as allowed by law.</li>
        <li><strong>Restrict processing or portability:</strong> Ask to limit how your data is used or request your data in a portable format.</li>
    </ul>

    <p>
        To exercise these rights, please contact us at the email address below.
    </p>

    <h2>7. Third-Party Links</h2>
    <p>
        Our site may contain links to other websites not controlled by us. 
        We are not responsible for the privacy practices of external sites. 
        Please review their privacy policies separately.
    </p>

    <h2>8. Changes to This Policy</h2>
    <p>
        We may update this Privacy Policy from time to time. 
        We advise you to visit this page regularly for the latest updates.
    </p>

    <h2>Contact Us</h2>
    <p>
        For questions about this Privacy Policy or to exercise your data rights, 
        please contact us at:
    </p>
    <p>
        📧 <a href="mailto:<?= esc($siteSettings['contact_email'] ?? 'info@b2btradeservices.com') ?>"><?= esc($siteSettings['contact_email'] ?? 'info@b2btradeservices.com') ?></a>
    </p>

<p>
    Users are strictly required to comply with our 
    <a href="<?= base_url('banned-keywords-and-illegal-products-policy') ?>">
        Banned Keywords and Illegal Products Policy</a>. 
    Listing, promoting, or engaging in transactions involving prohibited items may lead to immediate suspension, 
    permanent account termination, and possible reporting to relevant authorities.
</p>

<p><a href="https://b2btradeservices.com/">B2B Trade Services LLC</a> will not be held responsible or accountable for any actions of the users that violates above terms and conditions, specially banned keywords and illegal products.</p>

</section>

</div>

<?= $this->endSection() ?>
