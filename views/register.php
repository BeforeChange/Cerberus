<div class="d-flex justify-content-center align-items-center min-vh-100">
    <form class="form-signin w-100" action="/register" method="POST" style="max-width: 400px;">
        <img class="mb-4 d-block mx-auto" src="/docs/5.3/assets/brand/bootstrap-logo.svg" alt="" width="72" height="57">

        <h1 class="h3 mb-3 fw-normal text-center">Register</h1>

        <!-- Email Field -->
        <div class="form-floating mb-3">
            <input 
                type="email" 
                class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                id="email" 
                name="email"
                placeholder="name@example.com"
                value="<?= htmlspecialchars($data['email'] ?? '') ?>"
                required
            >
            <label for="email">Email address</label>
            <?php if (isset($errors['email'])): ?>
                <div class="invalid-feedback">
                    <?= htmlspecialchars($errors['email']) ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Password Field -->
        <div class="form-floating mb-3 position-relative">
            <input 
                type="password" 
                class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                id="password" 
                name="password"
                placeholder="Password"
                value="<?= htmlspecialchars($data['password'] ?? '') ?>"
                required
            >
            <label for="password">Password</label>
            <?php if (isset($errors['password'])): ?>
                <div class="invalid-feedback d-block">
                    <?= htmlspecialchars($errors['password']) ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Confirm Password Field -->
        <div class="form-floating mb-3 position-relative">
            <input 
                type="password" 
                class="form-control <?= isset($errors['password_confirm']) ? 'is-invalid' : '' ?>" 
                id="password_confirm" 
                name="password_confirm"
                placeholder="Confirm Password"
                value="<?= htmlspecialchars($data['password_confirm'] ?? '') ?>"
                required
            >
            <label for="password_confirm">Confirm Password</label>
            <?php if (isset($errors['password_confirm'])): ?>
                <div class="invalid-feedback d-block">
                    <?= htmlspecialchars($errors['password_confirm']) ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- General errors -->
        <?php if (isset($errors['general'])): ?>
            <div class="alert alert-danger mb-3">
                <?= htmlspecialchars($errors['general']) ?>
            </div>
        <?php endif; ?>

        <button type="submit" class="btn btn-primary w-100 py-2">Register</button>

        <p class="text-center mt-3 mb-0">
            Already have an account? 
            <a href="/login" class="text-decoration-none">Login</a>
        </p>
    </form>
</div>