<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
<section class="inner-form-banner">
    <div class="inner-form-banner-img">
        <img src="<?= base_url('assets/images/register-img.webp') ?>" class="w-100">
    </div>
    <div class="form-sec">
        <div class="b2b-logo">
            <img src="<?= base_url('assets/images/logo.svg') ?>" class="w-100">
        </div>
        <h1 class="h2">Welcome Back to B2B Trade Services</h1>
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 15px; font-size: 16px; font-weight: 600; border: 1px solid #c3e6cb; text-align: center;"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger" style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px;"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger" style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                <ul style="margin: 0; padding-left: 20px;">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form method="post" action="<?= base_url('login') ?>">
            <?= csrf_field() ?>
            <div class="form-input mt-4">
                <input type="email" name="email" placeholder="Email" value="<?= old('email') ?>" required>
            </div>
            <div class="form-input password-input">
                <input type="password" name="password" id="password" class="password" placeholder="Password" required>
                <i class="eye" onclick="togglePassword()">
                    <svg style="fill: #DBDBDB" class="eye-icon" height="20" viewBox="0 0 24 24" width="20" xmlns="http://www.w3.org/2000/svg">
                        <path clip-rule="evenodd" d="m12 4.5c-5 0-9.27 3.11-11 7.5 1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zm0 12.5c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"></path>
                    </svg>
                </i>
            </div>
            <div style="text-align:right;margin-top:8px;margin-bottom:8px;">
                <a href="<?= base_url('forgot-password') ?>" style="font-size:13px;color:#15A2A0;text-decoration:none;font-weight:500;">Forgot Password?</a>
            </div>
            <div class="submit-btn-gradient mt-3">
                <button type="submit" class="gradeint-cta">Log In</button>
            </div>
        </form>
        <!-- <p class="mt-4">Or Sign up with</p>-->
        <!--<div class="social-connect">-->
        <!--    <a href="#"><img src="<?= base_url('assets/images/fb-reg-icon.svg') ?>"></a>-->
        <!--    <a href="#"><img src="<?= base_url('assets/images/google-reg-icon.svg') ?>"></a>-->
        <!--</div>-->
        <div class="mt-4">
            <p>Don't have an account? <a href="<?= base_url('register') ?>" class="reg-a">Sign Up</a></p>
        </div>
    </div>
</section>
<?= $this->endSection() ?>