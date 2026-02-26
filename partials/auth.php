<header class="top-nav auth-nav">
    <div class="brand">Workplace<span>Formula</span></div>
    <div class="auth-nav-badge">Login do time</div>
</header>

<main class="landing landing-auth">
    <section class="auth-shell" id="auth-panels">
        <div class="auth-shell-head">
            <h1>Entrar / Cadastrar</h1>
        </div>

        <section class="auth-grid auth-grid-clean">
            <section class="auth-panel">
                <div class="panel-heading">
                    <span class="pill-label">Entrar</span>
                    <h3>Login</h3>
                </div>
                <form method="post" class="form-stack">
                    <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                    <input type="hidden" name="action" value="login">

                    <label>
                        <span>E-mail</span>
                        <input type="email" name="email" placeholder="voce@empresa.com" required>
                    </label>

                    <label>
                        <span>Senha</span>
                        <input type="password" name="password" placeholder="Sua senha" required>
                    </label>

                    <button class="btn btn-pill" type="submit">Entrar</button>
                </form>
            </section>

            <section class="auth-panel">
                <div class="panel-heading">
                    <span class="pill-label">Cadastrar</span>
                    <h3>Novo usuario</h3>
                </div>
                <form method="post" class="form-stack">
                    <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                    <input type="hidden" name="action" value="register">

                    <label>
                        <span>Nome</span>
                        <input type="text" name="name" placeholder="Nome completo" required>
                    </label>

                    <label>
                        <span>E-mail</span>
                        <input type="email" name="email" placeholder="voce@empresa.com" required>
                    </label>

                    <label>
                        <span>Senha</span>
                        <input type="password" name="password" placeholder="Minimo 6 caracteres" minlength="6" required>
                    </label>

                    <label>
                        <span>Confirmar senha</span>
                        <input type="password" name="password_confirm" placeholder="Repita a senha" minlength="6" required>
                    </label>

                    <button class="btn btn-pill btn-light" type="submit">Criar conta</button>
                </form>
            </section>
        </section>
    </section>
</main>

