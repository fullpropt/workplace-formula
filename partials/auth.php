<header class="top-nav">
    <div class="brand">Workplace<span>Formula</span></div>
    <nav class="nav-links" aria-label="Navegação principal">
        <a href="#features">Recursos</a>
        <a href="#auth-panels">Entrar</a>
        <a href="#auth-panels">Cadastrar</a>
    </nav>
    <a href="#auth-panels" class="btn btn-pill btn-light">Acessar time</a>
</header>

<main class="landing">
    <section class="hero">
        <p class="eyebrow">TASK OPS · MULTI-LOGIN · KANBAN</p>
        <h1>Workplace<br>Formula</h1>
        <p class="hero-copy">
            Um workspace para seu time anotar, organizar e acompanhar tarefas em um fluxo compartilhado,
            com login individual e quadro estilo ClickUp.
        </p>
        <div class="hero-actions">
            <a href="#auth-panels" class="btn btn-pill">Criar workspace</a>
            <a href="#features" class="btn btn-ghost">Ver recursos</a>
        </div>
    </section>

    <section class="stats-strip" id="features" aria-label="Recursos principais">
        <div class="stat-cell">
            <span>Login</span>
            <strong>Multiusuário</strong>
        </div>
        <div class="stat-cell">
            <span>Visual</span>
            <strong>Kanban escuro</strong>
        </div>
        <div class="stat-cell">
            <span>Campos</span>
            <strong>Prioridade, prazo, responsável</strong>
        </div>
        <div class="stat-cell">
            <span>Stack</span>
            <strong>PHP + SQLite</strong>
        </div>
    </section>

    <section class="feature-intro">
        <span class="pill-label">Feito para operação diária</span>
        <h2>O que o time ganha com isso?</h2>
        <p>
            Uma central única para registrar demandas, mover status, delegar responsáveis e acompanhar entregas sem depender de planilhas soltas.
        </p>
    </section>

    <section class="feature-cards">
        <article class="glass-card">
            <h3>Autenticação por usuário</h3>
            <p>Cada pessoa entra com seu próprio login e acessa o quadro compartilhado do time.</p>
            <small>01</small>
        </article>
        <article class="glass-card">
            <h3>Fluxo de execução</h3>
            <p>Backlog, em andamento, revisão e concluído em um painel visual e objetivo.</p>
            <small>02</small>
        </article>
        <article class="glass-card">
            <h3>Atribuição e prioridades</h3>
            <p>Defina responsável, prazo e criticidade para cada tarefa sem sair da tela.</p>
            <small>03</small>
        </article>
        <article class="glass-card">
            <h3>Edição rápida</h3>
            <p>Atualize detalhes e status direto no card para manter o fluxo sem fricção.</p>
            <small>04</small>
        </article>
    </section>

    <section class="auth-grid" id="auth-panels">
        <section class="auth-panel">
            <div class="panel-heading">
                <span class="pill-label">Entrar</span>
                <h3>Acesse seu workspace</h3>
                <p>Use seu login para abrir o quadro de tarefas compartilhado.</p>
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
                <h3>Criar usuário para o time</h3>
                <p>Cadastre membros para usar a mesma aplicação em sessões separadas.</p>
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
                    <input type="password" name="password" placeholder="Mínimo 6 caracteres" minlength="6" required>
                </label>

                <label>
                    <span>Confirmar senha</span>
                    <input type="password" name="password_confirm" placeholder="Repita a senha" minlength="6" required>
                </label>

                <button class="btn btn-pill btn-light" type="submit">Criar conta</button>
            </form>
        </section>
    </section>
</main>

