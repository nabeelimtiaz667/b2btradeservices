<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - B2B Trade Services</title>
    <link href="<?= base_url('assets/images/b2b-icon.svg') ?>" rel="icon">
    <link href="<?= base_url('assets/images/b2b-icon.svg') ?>" rel="apple-touch-icon">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
        }
        .blur-circle {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.6;
            z-index: 0;
        }
        .blur-circle-1 {
            width: 400px; height: 400px;
            background: rgba(0,0,0,0.15);
            bottom: -100px; right: 10%;
        }
        .blur-circle-2 {
            width: 350px; height: 350px;
            background: rgba(0,0,0,0.1);
            bottom: 50px; right: 25%;
        }
        .auth-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 500px;
            padding: 20px;
        }
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
            top: 0; left: -100%;
            width: 60%; height: 100%;
            background: linear-gradient(120deg, transparent, rgba(255,255,255,0.6), transparent);
            transition: 0.9s;
        }
        .gradeint-cta:hover::before { left: 120%; }
        .glass-form {
            background: rgba(255,255,255,0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.8);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }
        .form-group { margin-bottom: 25px; }
        .form-label {
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
            display: block;
        }
        .form-control {
            background: rgba(255,255,255,0.6);
            border: 1px solid rgba(0,150,136,0.2);
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 14px;
            transition: all 0.3s ease;
            color: #333;
            width: 100%;
        }
        .form-control::placeholder { color: rgba(0,0,0,0.3); }
        .form-control:focus {
            background: rgba(255,255,255,0.8);
            border-color: #009688;
            box-shadow: 0 0 0 3px rgba(0,150,136,0.1);
            outline: none;
        }
        .input-wrapper {
            position: relative;
        }
        .input-wrapper .form-control {
            padding-right: 44px;
        }
        .toggle-pwd {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #999;
            padding: 0;
            font-size: 15px;
        }
        .toggle-pwd:hover { color: #009688; }
        .password-strength {
            margin-top: 8px;
            font-size: 12px;
        }
        .strength-bar {
            height: 4px;
            border-radius: 2px;
            background: #eee;
            margin-bottom: 4px;
            overflow: hidden;
        }
        .strength-bar-fill {
            height: 100%;
            border-radius: 2px;
            transition: width 0.3s, background 0.3s;
        }
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
        }
        .auth-footer a:hover { text-decoration: underline; }
        @media (max-width: 576px) {
            .auth-container { max-width: 100%; }
            .auth-header h1 { font-size: 24px; }
            .glass-form { padding: 30px 20px; border-radius: 16px; }
        }
    </style>
</head>
<body>
    <div class="blur-circle blur-circle-1"></div>
    <div class="blur-circle blur-circle-2"></div>

    <div class="auth-container">
        <div class="auth-header">
            <h1>Set New Password</h1>
            <p>Choose a strong password for your account</p>
        </div>

        <form class="glass-form" method="POST" action="<?= base_url('reset-password/' . esc($token)) ?>">
            <?= csrf_field() ?>

            <div class="b2b-logo" style="text-align:center;margin-bottom:30px;">
                <a href="<?= base_url() ?>"><img src="<?= base_url('assets/images/logo.svg') ?>" width="200px"></a>
            </div>

            <?php $errorMsg = $error ?? session()->getFlashdata('error'); ?>
            <?php if ($errorMsg): ?>
                <div style="background:rgba(220,53,69,0.1);border-left:3px solid #dc3545;padding:12px 16px;border-radius:6px;margin-bottom:20px;font-size:14px;color:#333;">
                    <i class="fas fa-exclamation-circle" style="color:#dc3545;margin-right:8px;"></i>
                    <?= esc($errorMsg) ?>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label class="form-label">New Password</label>
                <div class="input-wrapper">
                    <input type="password" class="form-control" name="password" id="password"
                           placeholder="Enter new password (min. 6 characters)" required minlength="6">
                    <button type="button" class="toggle-pwd" onclick="toggleField('password', this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="password-strength" id="strength-wrap" style="display:none;">
                    <div class="strength-bar"><div class="strength-bar-fill" id="strength-bar"></div></div>
                    <span id="strength-label" style="color:#999;"></span>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Confirm New Password</label>
                <div class="input-wrapper">
                    <input type="password" class="form-control" name="password_confirm" id="password_confirm"
                           placeholder="Re-enter your new password" required>
                    <button type="button" class="toggle-pwd" onclick="toggleField('password_confirm', this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div id="match-msg" style="font-size:12px;margin-top:6px;display:none;"></div>
            </div>

            <button type="submit" class="gradeint-cta">
                <i class="fas fa-lock"></i> Reset Password
            </button>

            <div class="auth-footer">
                Back to <a href="<?= base_url('login') ?>">Sign In</a>
            </div>
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleField(id, btn) {
            var input = document.getElementById(id);
            var icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        var pwdInput = document.getElementById('password');
        var confirmInput = document.getElementById('password_confirm');

        pwdInput.addEventListener('input', function() {
            var val = this.value;
            var wrap = document.getElementById('strength-wrap');
            var bar = document.getElementById('strength-bar');
            var label = document.getElementById('strength-label');
            if (val.length === 0) { wrap.style.display = 'none'; return; }
            wrap.style.display = 'block';
            var score = 0;
            if (val.length >= 6) score++;
            if (val.length >= 10) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;
            var colors = ['#dc3545','#fd7e14','#ffc107','#5FC86B','#15A2A0'];
            var labels = ['Very Weak','Weak','Fair','Strong','Very Strong'];
            bar.style.width = (score * 20) + '%';
            bar.style.background = colors[score - 1] || '#eee';
            label.textContent = labels[score - 1] || '';
            label.style.color = colors[score - 1] || '#999';
            checkMatch();
        });

        confirmInput.addEventListener('input', checkMatch);

        function checkMatch() {
            var msg = document.getElementById('match-msg');
            var confirm = confirmInput.value;
            if (confirm.length === 0) { msg.style.display = 'none'; return; }
            msg.style.display = 'block';
            if (pwdInput.value === confirm) {
                msg.textContent = '✓ Passwords match';
                msg.style.color = '#5FC86B';
            } else {
                msg.textContent = '✗ Passwords do not match';
                msg.style.color = '#dc3545';
            }
        }
    </script>
</body>
</html>
