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
    <h1>Refund Policy</h1>

    <p>At B2BTradeServices.com, we aim to maintain transparency and fairness in all business dealings. This Refund Policy explains the conditions under which refunds may be requested and processed.</p>

<h2>Service Agreement Requirement</h2>
<p>Before receiving any payment, B2BTradeServices.com provides an official service contract to the client. This contract outlines all services, deliverables, timelines, warranties, and commitments in detail.</p>
<p>Clients must sign the official contract and submit a signed copy along with payment confirmation to ensure proper documentation and tracking.
Payments made without signing the official contract are not eligible for any refund under any circumstances.</p>

<h2>Requesting a Refund</h2>
<p>If a client is dissatisfied with the services, they must submit a formal refund request by email to <?= esc($siteSettings['contact_email'] ?? 'info@b2btradeservices.com') ?>
The request must clearly explain the reason for dissatisfaction and reference the related service agreement.</p>
<p>B2BTradeServices.com will respond to refund requests within approximately seven working days.</p>

<h2>Refund Review Process</h2>
<p>Each refund request is reviewed on a case-by-case basis. Our team evaluates the services delivered, contract terms, timelines, and individual case details to identify the cause of dissatisfaction.</p>
<p>Where applicable, B2BTradeServices.com may offer corrective solutions OR Compensations such as additional services or extended service duration at no extra cost to provide a fair opportunity for resolution.</p>
<p>If the client remains unsatisfied after corrective measures and the review confirms service limitations, the client may become eligible for a refund subject to final approval.</p>

</section>

</div>

<?= $this->endSection() ?>
