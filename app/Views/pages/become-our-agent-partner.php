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

<section class="agent-partner">

    <h1>Become Our Agent Partner & Start Earning Today</h1>

    <p>
        Join our <strong>Agency Partnership Program</strong> at 
        <strong>B2BTradeServices.com</strong> and earn attractive commissions 
        by referring businesses to our B2B marketplace. 
        As an agent, you help grow our network and get rewarded 
        for every successful conversion.
    </p>

    <h2>Why Join Our Agent Program?</h2>

    <p>
        By partnering with us, you gain access to a large global marketplace 
        built for business growth. We offer:
    </p>

    <h3>1. Competitive Commission</h3>
    <p>
        Earn up to <strong>50% commission</strong> for every company you refer 
        that becomes a paying member.
    </p>

    <h3>2. No Targets</h3>
    <p>
        There are no sales quotas to meet. You earn purely based on 
        the results you deliver.
    </p>

    <h3>3. Flexible Work Setup</h3>
    <p>
        Work on your own schedule and promote our platform in ways 
        that suit your audience and strategy.
    </p>

    <h3>4. Dedicated Agent Support</h3>
    <p>
        Each agent receives a dedicated support contact to assist 
        with onboarding, referrals, and any questions.
    </p>

    <h3>5. Ideal for Agencies & Freelancers</h3>
    <p>
        This program is open to marketing agencies, freelancers, consultants, 
        and independent business partners of all sizes.
    </p>

    <h2>What You Do as an Agent</h2>

    <p>As an Agent Partner, your role includes:</p>

    <ul>
        <li>Finding and engaging potential business customers</li>
        <li>Promoting B2BTradeServices.com to your audience</li>
        <li>Helping prospects understand the value of our platform</li>
        <li>Referring interested businesses to our registration page</li>
    </ul>

    <h2>Benefits for You</h2>

    <h3>Unlimited Payouts</h3>
    <p>
        There is no cap on your earnings. The more businesses you refer, 
        the more your income grows.
    </p>

    <h3>Work on Your Terms</h3>
    <p>
        Set your own pace and run campaigns based on what works best 
        for your network and audience.
    </p>

    <h3>Continuous Support</h3>
    <p>
        Our team works with you to help your referrals convert and succeed, 
        ensuring long-term partnership value.
    </p>

    <h2>Who Can Become an Agent?</h2>

    <p>
        Anyone with an audience, professional network, or business contacts can join. 
        Whether you are an affiliate marketer, marketing agency, consultant, 
        or freelancer looking to add a new revenue stream, 
        this program is designed for you.
    </p>

    <h2>Start Earning Today</h2>

    <p>
        Ready to become an Agent Partner for 
        <strong>B2BTradeServices.com</strong>? 
        Complete the registration form below and begin earning commissions 
        on qualified referrals.
    </p>

    <h3>Registration is Simple</h3>

    <p>
        Fill out the form with your:
    </p>

    <ul>
        <li>Name</li>
        <li>Company Name</li>
        <li>Country</li>
        <li>Contact Information</li>
    </ul>

    <p>
        Our team will review your details and get back to you shortly.
    </p>

    <p>
        Alternatively, you can contact us directly at:<br>
        📧 <a href="mailto:<?= esc($siteSettings['contact_email'] ?? 'info@b2btradeservices.com') ?>"><?= esc($siteSettings['contact_email'] ?? 'info@b2btradeservices.com') ?></a>
    </p>

</section>

</div>

<div class="container text-center">
    <section class="cta-partner-section">
        <h2>Want to become Our Partner!</h2>
        <p>Register Now and talk to our Affiliate Manager</p>
        <a href="#" class="btn-cta" data-bs-toggle="modal" data-bs-target="#applyNowModal">Join the Network</a>
    </section>
</div>

<?= view('partials/agent-partner-form-modal') ?>

<?= $this->endSection() ?>
