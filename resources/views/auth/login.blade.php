<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Login | Tanaman</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .swal2-popup.security-alert-popup {
            background-color: #f8d7da;
            border: 1px solid #dc3545;
            border-left: 5px solid #1a4d32;
        }

        .swal2-popup.security-alert-popup .swal2-title,
        .swal2-popup.security-alert-popup .swal2-html-container {
            color: #dc3545;
        }
    </style>
</head>
<body class="auth-body">

    <nav class="navbar">
        <div class="nav-brand">
            <div class="logo-container">
                <img src="{{ asset('images/TanamanLogo.png') }}" alt="Logo" class="nav-logo" loading="lazy">
            </div>
            <span class="company-name">Tanaman</span>
        </div>
    </nav>

    <main class="auth-layout">
        <div class="login-card">
            <div class="card-header">
                <h2>Welcome Back</h2>
                <p>Enter your credentials to access the dashboard</p>
            </div>

        <form class="card-body" id="loginForm">
            <div class="input-group">
                <label for="username">Username or Email</label>
                <input type="text" id="username" name="email" placeholder="Enter username or email" required>
            </div>

            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter password" required>
            </div>

            <div class="actions">
                <label for="remember-me" class="remember-me">
                    <input type="checkbox" id="remember-me" name="remember">
                    Remember me
                </label>
                <a href="#" class="forgot-password">Forgot password?</a>
            </div>

            <div id="formError" class="form-error hidden" aria-live="assertive"></div>
            <div class="submit-group">
                <button type="submit" class="submit-btn">Sign In</button>
            </div>
        </form>

        <div class="card-footer">
            <p>Don't have an account? <a href="#">Contact Admin</a></p>
        </div>

        </div>
    </main>

    <script>
        const loginForm = document.getElementById('loginForm');
        const formError = document.getElementById('formError');
        const submitButton = document.querySelector('.submit-btn');
        const submitButtonDefaultText = submitButton?.textContent?.trim() || 'Sign In';
        const usernameInput = document.getElementById('username');
        const passwordInput = document.getElementById('password');
        let lockoutInterval = null;
        let isLockoutActive = false;

        loginForm.addEventListener('submit', async function(event) {
            event.preventDefault();

            clearFormHighlights();

            const usernameValue = usernameInput.value;
            const passwordValue = passwordInput.value;
            const rememberInput = document.getElementById('remember-me').checked;

            toggleLoading(true);

            try {
                const response = await fetch("{{ route('login') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        login: usernameValue,
                        password: passwordValue,
                        remember: rememberInput
                    })
                });

                const result = await response.json();

                if (response.ok) {
                    resetLockoutState();
                    Swal.fire({
                        title: 'Welcome Back!',
                        text: `${result.message}`,
                        icon: 'success',
                        iconColor: '#319B72',
                        confirmButtonColor: '#1A4D3F',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = "{{ route('dashboard') }}";
                    });

                    return;
                }

                const errorMessage = result.message || 'An unknown error has occured';

                if (response.status === 429) {
                    startLockoutState(result.message || 'Too many login attempts. Please try again later.', result.retry_after || 60);
                    return;
                }

                if (response.status === 401) {
                    highlightValidationError(result.message || 'Invalid credentials', Number(result.remaining_attempts) || 0);
                    return;
                }

                highlightFormError(errorMessage);
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    title: 'System Error',
                    text: 'Something went wrong. Please try again later.',
                    icon: 'error',
                    confirmButtonColor: '#1A4D3F'
                });
            } finally {
                toggleLoading(false);
            }
        });

        function toggleLoading(isLoading) {
            if (isLockoutActive) {
                submitButton.disabled = true;
                return;
            }

            submitButton.disabled = isLoading;
            submitButton.textContent = isLoading ? 'Signing in...' : submitButtonDefaultText;
        }

        function startLockoutState(message, seconds) {
            if (!submitButton) {
                return;
            }

            clearInterval(lockoutInterval);
            isLockoutActive = true;
            submitButton.disabled = true;
            submitButton.classList.add('locked');

            let remainingSeconds = Math.max(Number(seconds) || 0, 0);
            submitButton.textContent = `${submitButtonDefaultText} (${remainingSeconds}s)`;

            usernameInput?.classList.remove('input-error');
            passwordInput?.classList.remove('input-error');

            if (formError) {
                formError.textContent = message;
                formError.classList.remove('hidden');
            }

            lockoutInterval = setInterval(() => {
                remainingSeconds -= 1;

                if (remainingSeconds <= 0) {
                    resetLockoutState();
                    return;
                }

                submitButton.textContent = `${submitButtonDefaultText} (${remainingSeconds}s)`;
            }, 1000);
        }

        function resetLockoutState() {
            isLockoutActive = false;
            clearInterval(lockoutInterval);
            lockoutInterval = null;

            submitButton.disabled = false;
            submitButton.classList.remove('locked');
            submitButton.textContent = submitButtonDefaultText;
            clearFormHighlights();
        }

        function highlightFormError(message) {
            usernameInput?.classList.add('input-error');
            passwordInput?.classList.add('input-error');

            if (formError) {
                formError.textContent = message;
                formError.classList.remove('hidden');
            }
        }

        function highlightValidationError(message, remainingAttempts) {
            usernameInput?.classList.add('input-error');
            passwordInput?.classList.add('input-error');

            let composedMessage = message;
            if (typeof remainingAttempts === 'number' && remainingAttempts > 0) {
                const plural = remainingAttempts === 1 ? 'attempt' : 'attempts';
                composedMessage = `${message}. You have ${remainingAttempts} more ${plural} left.`;
            }

            if (formError) {
                formError.textContent = composedMessage;
                formError.classList.remove('hidden');
            }
        }

        function clearFormHighlights() {
            usernameInput?.classList.remove('input-error');
            passwordInput?.classList.remove('input-error');

            if (formError) {
                formError.textContent = '';
                formError.classList.add('hidden');
            }
        }
    </script>

</body>
</html>
