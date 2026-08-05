<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc(($title ?? 'Forgot Password') . ' - B2B Trade Services') ?></title>
    <?php if (!empty($metaDescription)): ?>
    <meta name="description" content="<?= esc($metaDescription) ?>">
    <?php endif; ?>
    <?php if (!empty($canonical)): ?>
    <link rel="canonical" href="<?= esc($canonical) ?>">
    <?php endif; ?>
        <link href="./assets/images/b2b-icon.svg" rel="icon">
    <link href="./assets/images/b2b-icon.svg" rel="apple-touch-icon">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
        }

        /* Background Blur Circles */
        .blur-circle {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.6;
            z-index: 0;
        }

        .blur-circle-1 {
            width: 400px;
            height: 400px;
            background: rgba(0, 0, 0, 0.15);
            bottom: -100px;
            right: 10%;
        }

        .blur-circle-2 {
            width: 350px;
            height: 350px;
            background: rgba(0, 0, 0, 0.1);
            bottom: 50px;
            right: 25%;
        }

        /* Container */
        .auth-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 500px;
            padding: 20px;
        }

        /* Header */
        .auth-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .auth-header h1 {
            font-size: 28px;
            font-weight: 700;
            color: #0A504F;
            margin-bottom: 12px;
        }

        .auth-header p {
            font-size: 16px;
            color: #666;
            margin: 0;
        }

    .gradeint-cta {
    background-image: linear-gradient(45deg, #15A2A0, #5FC86B);
    width: 100%;
    border-radius: 26px;
    max-width: 100%;
    color: #fff;
    font-size: 14px;
    height: 51px;
    display: flex;
  border: 0;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    position: relative;
    overflow: hidden;
    cursor: pointer;
  gap: 8px;
}
.gradeint-cta:hover {
    background-image: linear-gradient(270deg, #15A2A0, #5FC86B);
    transition: .5s linear ease;
    color: #fff;
}
.gradeint-cta::before {
    content: "";
    position: absolute;
    top: 0;
    left: -100%;
    width: 60%;
    height: 100%;
    background: linear-gradient(120deg, transparent, rgba(255, 255, 255, 0.6), transparent);
    transition: 0.9s;
}
.gradeint-cta:hover::before {
    left: 120%;
}

        /* Glassmorphism Form Container */
        .glass-form {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
            display: block;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.6);
            border: 1px solid rgba(0, 150, 136, 0.2);
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 14px;
            transition: all 0.3s ease;
            color: #333;
            width: 100%;
        }

        .form-control::placeholder {
            color: rgba(0, 0, 0, 0.3);
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.8);
            border-color: #009688;
            box-shadow: 0 0 0 3px rgba(0, 150, 136, 0.1);
            outline: none;
        }

        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, #009688 0%, #00b8a9 100%);
            border: none;
            color: white;
            padding: 14px 20px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 150, 136, 0.3);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 150, 136, 0.4);
            background: linear-gradient(135deg, #00b8a9 0%, #009688 100%);
            color: white;
            text-decoration: none;
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        /* Footer Links */
        .auth-footer {
            text-align: center;
            margin-top: 25px;
            font-size: 14px;
            color: #666;
        }

        .auth-footer a {
            color: #009688;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .auth-footer a:hover {
            text-decoration: underline;
            color: #00b8a9;
        }

        /* Info Message */
        .info-message {
            background: rgba(0, 150, 136, 0.1);
            border-left: 3px solid #009688;
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 25px;
            font-size: 14px;
            color: #333;
        }

        .info-message i {
            margin-right: 10px;
            color: #009688;
        }

        /* Responsive Design */
        @media (max-width: 576px) {
            .auth-container {
                max-width: 100%;
            }

            .auth-header h1 {
                font-size: 24px;
            }

            .glass-form {
                padding: 30px 20px;
                border-radius: 16px;
            }

            .blur-circle-1 {
                width: 250px;
                height: 250px;
            }

            .blur-circle-2 {
                width: 200px;
                height: 200px;
            }
        }
    </style>
</head>
<body>
    <!-- Background Blur Circles -->
    <div class="blur-circle blur-circle-1"></div>
    <div class="blur-circle blur-circle-2"></div>

    <div class="auth-container">


        <!-- Header -->
        <div class="auth-header">
            <h1>Forgot Password?</h1>
            <p>Enter your email address to receive a password reset link</p>
        </div>

        <!-- Form -->
        <form class="glass-form" method="POST" action="<?= base_url('forgot-password') ?>">
            <?= csrf_field() ?>

            <div class="b2b-logo" style="text-align: center; margin-bottom: 30px;">
                <a href="<?= base_url() ?>"><img src="<?= base_url('assets/images/logo.svg') ?>" width="200px"></a>
            </div>

            <?php
                $successMsg = $success ?? session()->getFlashdata('success');
                $errorMsg   = $error   ?? session()->getFlashdata('error');
            ?>

            <?php if ($successMsg): ?>
                <div style="background:rgba(95,200,107,0.15);border-left:3px solid #5FC86B;padding:12px 16px;border-radius:6px;margin-bottom:20px;font-size:14px;color:#333;">
                    <i class="fas fa-check-circle" style="color:#5FC86B;margin-right:8px;"></i>
                    <?= esc($successMsg) ?>
                </div>
                <div class="auth-footer" style="margin-top:10px;">
                    Remember your password? <a href="<?= base_url('login') ?>">Sign In</a>
                </div>
            <?php else: ?>

                <?php if ($errorMsg): ?>
                    <div style="background:rgba(220,53,69,0.1);border-left:3px solid #dc3545;padding:12px 16px;border-radius:6px;margin-bottom:20px;font-size:14px;color:#333;">
                        <i class="fas fa-exclamation-circle" style="color:#dc3545;margin-right:8px;"></i>
                        <?= esc($errorMsg) ?>
                    </div>
                <?php endif; ?>

                <div class="info-message">
                    <i class="fas fa-info-circle"></i>
                    We'll send you an email with instructions to reset your password.
                </div>

                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input 
                        type="email" 
                        class="form-control" 
                        placeholder="Enter your email address" 
                        name="email"
                        value="<?= esc($email ?? old('email')) ?>"
                        required
                    >
                </div>

                <button type="submit" class="gradeint-cta">
                    <i class="fas fa-paper-plane"></i> Send Reset Link
                </button>

                <div class="auth-footer">
                    Remember your password? <a href="<?= base_url('login') ?>">Sign In</a>
                </div>

            <?php endif; ?>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
