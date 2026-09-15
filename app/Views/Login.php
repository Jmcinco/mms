<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>MMS – Login</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
  <style>
    :root{
      --navy:      #0e2c52;
      --blue-700:  #1c5fc4;
      --blue-600:  #2f6fe0;
      --blue-500:  #4c8bf5;
      --blue-100:  #e6f0fd;
      --sky-200:   #cfe2fb;
      --ink-700:   #2c3e58;
      --ink-400:   #7c8aa3;
      --bg:        #eef3f9;
      --teal-600:  #0f9d8c;
      --line:      #e3ebf5;
    }

    body {
      background: var(--bg);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      color: var(--ink-700);
    }
    .login-card {
      width: 420px;
      border-radius: 14px;
      box-shadow: 0 8px 32px rgba(14,44,82,0.14);
      border: 1px solid var(--line);
      overflow: hidden;
    }
    .login-header {
      background: linear-gradient(135deg, var(--navy), var(--blue-700));
      padding: 32px 24px 24px;
      text-align: center;
      color: #fff;
    }
    .login-header h2 {
      font-size: 2rem;
      font-weight: 800;
      letter-spacing: 2px;
      margin-bottom: 4px;
    }
    .login-header p {
      font-size: 0.85rem;
      opacity: 0.85;
      margin: 0;
    }
    .login-body {
      background: #fff;
      padding: 32px 28px;
    }
    .form-label {
      color: var(--ink-700);
    }
    .form-control:focus {
      border-color: var(--blue-700);
      box-shadow: 0 0 0 0.2rem rgba(28,95,196,.18);
    }
    .btn-login {
      background: var(--blue-700);
      border: none;
      color: #fff;
      font-weight: 600;
      letter-spacing: 1px;
      padding: 10px;
      border-radius: 6px;
      transition: background .2s;
    }
    .btn-login:hover { background: var(--blue-600); color:#fff; }
    .btn-login:disabled { opacity: .6; cursor: not-allowed; }
    .input-group-text { background: var(--blue-100); border-right:none; color: var(--blue-700); }
    .input-group .form-control { border-left:none; }
    .alert-danger { font-size: .9rem; }
    .g-recaptcha { display: flex; justify-content: center; margin-bottom: 20px; }
    footer { font-size: .8rem; color: var(--ink-400); text-align:center; margin-top:18px; }
  </style>
</head>
<body>
<div>
  <div class="login-card card">
    <div class="login-header">
      <h2><i class="fa-solid fa-fire me-2"></i>MMS</h2>
      <p>Media Monitoring System</p>
    </div>
    <div class="login-body">
      <div id="alertBox" class="alert alert-danger d-none"></div>
      <div class="mb-3">
        <label class="form-label fw-semibold">Username</label>
        <div class="input-group">
          <span class="input-group-text"><i class="fa fa-user"></i></span>
          <input type="text" id="username" class="form-control" placeholder="Enter username"/>
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label fw-semibold">Password</label>
        <div class="input-group">
          <span class="input-group-text"><i class="fa fa-lock"></i></span>
          <input type="password" id="password" class="form-control" placeholder="Enter password"/>
        </div>
      </div>
      <div class="g-recaptcha" data-sitekey="<?= esc(env('recaptcha.siteKey')) ?>"></div>
      <button class="btn btn-login w-100" id="btnSignIn" onclick="doLogin()">
        <i class="fa fa-sign-in-alt me-2"></i>SIGN IN
      </button>
    </div>
  </div>
</div>
<script>

async function doLogin() {
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value.trim();
    const alertBox = document.getElementById('alertBox');
    const btn = document.getElementById('btnSignIn');

    alertBox.classList.add('d-none');

    const recaptchaResponse = typeof grecaptcha !== 'undefined'
        ? grecaptcha.getResponse()
        : '';

    if (!recaptchaResponse) {
        alertBox.textContent = 'Please complete the reCAPTCHA challenge.';
        alertBox.classList.remove('d-none');
        return;
    }

    const formData = new FormData();
    formData.append('username', username);
    formData.append('password', password);
    formData.append('g-recaptcha-response', recaptchaResponse);

    btn.disabled = true;

    try {
        const response = await fetch('auth/login', {
            method: "POST",
            body: formData
        });

        console.log("Status:", response.status);

        const text = await response.text();
        console.log("Response:", text);

        const result = JSON.parse(text);

        if (!result.status) {
            alertBox.textContent = result.message;
            alertBox.classList.remove('d-none');
            if (typeof grecaptcha !== 'undefined') grecaptcha.reset();
            return;
        }

        window.location.href = result.redirect;

    } catch (e) {
        console.error("Fetch Error:", e);
        alertBox.textContent = e.message;
        alertBox.classList.remove('d-none');
        if (typeof grecaptcha !== 'undefined') grecaptcha.reset();
    } finally {
        btn.disabled = false;
    }
}
</script>
</body>
</html>