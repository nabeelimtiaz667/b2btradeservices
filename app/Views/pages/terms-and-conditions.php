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

<section class="terms-conditions">

    <h1>Terms and Conditions</h1>

    <p>
        Welcome to <strong>B2BTradeServices.com</strong>. These Terms and Conditions govern your access to and use of our website and services. 
        By visiting or using our platform, you accept these terms in full. If you do not agree with any part of these terms, you should not use this site.
    </p>

    <h2>1. Agreement Between You and B2BTradeServices.com</h2>
    <p>
        This document is a binding agreement between you (User) and B2BTradeServices.com ("we," "us," "our"). 
        It applies to all visitors, users, and others who access our platform or use our services.
    </p>

    <h2>2. Definitions</h2>
    <p><strong>"User"</strong> means anyone who visits, browses, registers, or uses the platform in any role.</p>
    <p><strong>"Content"</strong> refers to all information, listings, messages, images, and data posted or shared on the platform.</p>
    <p><strong>"Transaction"</strong> means any commercial arrangement between users for buying, selling, shipping, or payment of products or services.</p>

    <h2>3. Nature of Our Service</h2>
    <p>
        B2BTradeServices.com operates as an online business marketplace that enables users to connect, list products or services, 
        and communicate with one another. We are not involved in any direct sale, purchase, shipment, or payment process between our users. 
        All commercial dealings are handled solely between the users themselves.
    </p>

    <h2>4. User Account and Responsibilities</h2>
    <p>
        When you register with B2BTradeServices.com, you promise that all details you provide are lawful, accurate, and current. 
        You are fully responsible for safeguarding your account credentials and for all activity under your account.
    </p>

    <h2>5. Compliance with Laws</h2>
    <p>
        Users must follow all applicable local and international laws, including regulations on exports, sanctions, and prohibited products. 
        You agree not to use the platform to engage in illegal trade or transactions.
    </p>

    <h2>6. Listings and Marketplace Content</h2>
    <p>
        You guarantee that any content you upload or list is legal, truthful, and that you have the rights to share it. 
        We may remove or block any listing or content that seems unlawful, unsafe, or inconsistent with these terms at any time.
    </p>

    <h2>7. Financial Transactions</h2>
    <p>
        All financial arrangements, payments, refunds, and disputes are matters solely between the users involved. 
        B2BTradeServices.com does not handle, manage, or control any funds or payment systems on behalf of users.
    </p>

    <h2>8. Disclaimer and Limitation of Liability</h2>
    <p>
        Our platform is provided "as is" without any warranties of completeness, accuracy, or suitability for a particular purpose. 
        We are not liable for any loss of revenue, profits, business interruption, or data that may result from using the platform.
    </p>

    <h2>9. Indemnity</h2>
    <p>
        You agree to protect, defend, and hold B2BTradeServices.com and its representatives harmless from any claims or losses arising 
        from your content, transactions, or violation of applicable law or these terms.
    </p>

    <h2>10. Suspension and Termination</h2>
    <p>
        We may suspend or permanently block accounts or remove listings if we determine that a user poses legal, safety, 
        or operational risk to the platform or its users.
    </p>

    <h2>11. Intellectual Property</h2>
    <p>
        All rights to the site design, graphics, text, and software belong to B2BTradeServices.com. 
        By using our platform, you grant us permission to host, display, and distribute your content as needed for service operation.
    </p>

    <h2>12. Changes to Terms</h2>
    <p>
        We may update these Terms and Conditions at any time. Continued use of the platform after changes means you accept the revised terms.
    </p>

    <h2>13. Refund Policy</h2>
    <p>
        All payments made to B2BTradeServices.com are subject to our official Refund Policy. Refund eligibility, review procedures, 
        deductions, and processing timelines are governed strictly by the terms outlined in the signed service contract and our Refund Policy page.
    </p>
    <p>
        Clients are advised to carefully review the complete Refund Policy before making any payment. 
        Detailed information regarding refund conditions, approval process, and timelines is available on our dedicated Refund Policy page.
    </p>
    <p>
        By using our services and completing a payment, you acknowledge that you have read, understood, and agreed to the Refund Policy 
        published on B2BTradeServices.com.
    </p>
    <p>
        For full details, please visit the 
        <a href="<?= base_url('refund-policy') ?>">Refund Policy page</a>.
    </p>

    <h2>14. Governing Law</h2>
    <p>
        These terms are governed by the laws of <strong>[Your Jurisdiction]</strong>, 
        and any disputes will be resolved in the courts of that jurisdiction.
    </p>

    <h2>Contact Us</h2>
    <p>
        If you have questions about these Terms and Conditions, you may reach us at:
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
