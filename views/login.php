<div class="d-flex justify-content-center align-items-center min-vh-100">
    <form class="form-signin w-100" action="/login" method="POST" style="max-width: 400px;">
        <img class="mb-4 d-block mx-auto" src="/docs/5.3/assets/brand/bootstrap-logo.svg" alt="" width="72" height="57">

        <h1 class="h3 mb-3 fw-normal text-center">Please sign in</h1>

        <!-- Email Field -->
        <div class="form-floating mb-3">
            <input 
                type="email" 
                class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                id="floatingInput" 
                name="email"
                placeholder="name@example.com"
                value="<?= htmlspecialchars($data['email'] ?? '') ?>"
                required
            >
            <label for="floatingInput">Email address</label>
            <?php if (isset($errors['email'])): ?>
                <div class="invalid-feedback">
                    <?= htmlspecialchars($errors['email']) ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Password Field -->
        <div class="form-floating mb-3">
            <input 
                type="password" 
                class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                id="floatingPassword" 
                name="password"
                placeholder="Password"
                required
            >
            <label for="floatingPassword">Password</label>
            <?php if (isset($errors['password'])): ?>
                <div class="invalid-feedback">
                    <?= htmlspecialchars($errors['password']) ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- General errors -->
        <?php if (isset($errors['general'])): ?>
            <div class="alert alert-danger mb-3">
                <?= htmlspecialchars($errors['general']) ?>
            </div>
        <?php endif; ?>

        <!-- Remember me -->
        <div class="form-check text-start mb-3">
            <input class="form-check-input" type="checkbox" value="1" id="rememberMe" name="remember" <?= !empty($data['remember']) ? 'checked' : '' ?>>
            <label class="form-check-label" for="rememberMe">
                Remember me
            </label>
        </div>

        <button class="btn btn-primary w-100 py-2" type="submit">Sign in</button>

        <p class="mt-5 mb-3 text-body-secondary text-center">&copy; <?= date('Y') ?></p>
    </form>
</div>
