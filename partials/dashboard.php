<header class="top-nav dashboard-nav">
    <div class="brand">Workplace<span>Formula</span></div>
    <nav class="nav-links" aria-label="Navegação do dashboard">
        <a href="#board">Quadro</a>
        <a href="#new-task">Nova tarefa</a>
        <a href="#team">Time</a>
    </nav>
    <div class="user-chip">
        <div class="avatar" aria-hidden="true"><?= e(strtoupper(substr((string) $currentUser['name'], 0, 1))) ?></div>
        <div>
            <strong><?= e((string) $currentUser['name']) ?></strong>
            <span><?= e((string) $currentUser['email']) ?></span>
        </div>
    </div>
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <input type="hidden" name="action" value="logout">
        <button type="submit" class="btn btn-pill btn-light">Sair</button>
    </form>
</header>

<main class="dashboard">
    <section class="hero dashboard-hero">
        <p class="eyebrow">WORKSPACE OPERACIONAL</p>
        <h1>Organize, delegue e entregue<br>com o time alinhado.</h1>
        <p class="hero-copy">
            Um painel compartilhado para registrar demandas, acompanhar progresso e distribuir responsabilidades com clareza.
        </p>
        <div class="hero-actions">
            <a href="#new-task" class="btn btn-pill">Nova tarefa</a>
            <a href="#board" class="btn btn-ghost">Ir para o quadro</a>
        </div>
    </section>

    <section class="stats-strip dashboard-stats" aria-label="Indicadores do workspace">
        <div class="stat-cell">
            <span>Tarefas</span>
            <strong><?= e((string) $stats['total']) ?></strong>
        </div>
        <div class="stat-cell">
            <span>Concluídas</span>
            <strong><?= e((string) $stats['done']) ?> (<?= e((string) $completionRate) ?>%)</strong>
        </div>
        <div class="stat-cell">
            <span>Para hoje</span>
            <strong><?= e((string) $stats['due_today']) ?></strong>
        </div>
        <div class="stat-cell">
            <span>Urgentes</span>
            <strong><?= e((string) $stats['urgent']) ?></strong>
        </div>
        <div class="stat-cell">
            <span>Minhas abertas</span>
            <strong><?= e((string) $myOpenTasks) ?></strong>
        </div>
    </section>

    <section class="workspace-layout">
        <aside class="sidebar-stack">
            <section class="panel" id="new-task">
                <div class="panel-header">
                    <span class="pill-label">Criar tarefa</span>
                    <h2>Nova demanda</h2>
                    <p>Registre uma tarefa e já atribua responsável, prioridade e prazo.</p>
                </div>

                <form method="post" class="form-stack">
                    <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                    <input type="hidden" name="action" value="create_task">

                    <label>
                        <span>Título</span>
                        <input type="text" name="title" maxlength="140" placeholder="Ex.: Revisar landing da campanha" required>
                    </label>

                    <label>
                        <span>Descrição</span>
                        <textarea name="description" rows="4" placeholder="Detalhes, contexto, links ou checklist rápido..."></textarea>
                    </label>

                    <div class="form-row">
                        <label>
                            <span>Status inicial</span>
                            <select name="status">
                                <?php foreach ($statusOptions as $key => $label): ?>
                                    <option value="<?= e($key) ?>"><?= e($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>

                        <label>
                            <span>Prioridade</span>
                            <select name="priority">
                                <?php foreach ($priorityOptions as $key => $label): ?>
                                    <option value="<?= e($key) ?>"<?= $key === 'medium' ? ' selected' : '' ?>><?= e($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                    </div>

                    <div class="form-row">
                        <label>
                            <span>Responsável</span>
                            <select name="assigned_to">
                                <option value="">Sem responsável</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= e((string) $user['id']) ?>"<?= (int) $user['id'] === (int) $currentUser['id'] ? ' selected' : '' ?>>
                                        <?= e((string) $user['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </label>

                        <label>
                            <span>Prazo</span>
                            <input type="date" name="due_date">
                        </label>
                    </div>

                    <button type="submit" class="btn btn-pill">Adicionar ao quadro</button>
                </form>
            </section>

            <section class="panel">
                <div class="panel-header">
                    <span class="pill-label">Resumo rápido</span>
                    <h2>Time & foco</h2>
                    <p id="team">Usuários cadastrados e orientações de uso para o fluxo diário.</p>
                </div>
                <ul class="team-list">
                    <?php if (!$users): ?>
                        <li>Nenhum usuário cadastrado.</li>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <li>
                                <div class="avatar small" aria-hidden="true"><?= e(strtoupper(substr((string) $user['name'], 0, 1))) ?></div>
                                <div>
                                    <strong><?= e((string) $user['name']) ?></strong>
                                    <span><?= e((string) $user['email']) ?></span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
                <div class="tip-box">
                    <strong>Fluxo recomendado</strong>
                    <p>Crie no Backlog, mova para Em andamento quando iniciar, use Revisão para validação e finalize em Concluído.</p>
                </div>
            </section>
        </aside>

        <section class="board-wrap panel" id="board">
            <div class="panel-header board-header">
                <div>
                    <span class="pill-label">Quadro Kanban</span>
                    <h2>Tarefas do time</h2>
                    <p>Visualize tudo em colunas e atualize o status direto em cada card.</p>
                </div>
                <div class="board-summary">
                    <span><?= e((string) count($users)) ?> usuários</span>
                    <span><?= e((string) $stats['total']) ?> tarefas</span>
                </div>
            </div>

            <div class="kanban-grid" role="list">
                <?php foreach ($statusOptions as $statusKey => $statusLabel): ?>
                    <section class="kanban-column status-<?= e($statusKey) ?>" role="listitem" aria-labelledby="col-<?= e($statusKey) ?>">
                        <header class="kanban-column-head">
                            <h3 id="col-<?= e($statusKey) ?>"><?= e($statusLabel) ?></h3>
                            <span><?= e((string) count($groupedTasks[$statusKey] ?? [])) ?></span>
                        </header>

                        <div class="kanban-cards">
                            <?php if (empty($groupedTasks[$statusKey])): ?>
                                <div class="empty-card">
                                    <p>Sem tarefas nesta coluna.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($groupedTasks[$statusKey] as $task): ?>
                                    <?php
                                    $taskId = (int) $task['id'];
                                    $priority = normalizeTaskPriority((string) $task['priority']);
                                    $dueDate = $task['due_date'] ? (new DateTimeImmutable((string) $task['due_date']))->format('d/m/Y') : null;
                                    ?>
                                    <article class="task-card" id="task-<?= e((string) $taskId) ?>">
                                        <div class="task-card-top">
                                            <span class="badge priority-<?= e($priority) ?>"><?= e($priorityOptions[$priority]) ?></span>
                                            <span class="task-id">#<?= e((string) $taskId) ?></span>
                                        </div>

                                        <h4><?= e((string) $task['title']) ?></h4>

                                        <?php if (trim((string) $task['description']) !== ''): ?>
                                            <p class="task-desc"><?= e((string) $task['description']) ?></p>
                                        <?php endif; ?>

                                        <div class="task-meta">
                                            <span><strong>Resp.:</strong> <?= e((string) ($task['assignee_name'] ?? 'Não definido')) ?></span>
                                            <span><strong>Criado por:</strong> <?= e((string) $task['creator_name']) ?></span>
                                            <?php if ($dueDate): ?>
                                                <span><strong>Prazo:</strong> <?= e($dueDate) ?></span>
                                            <?php endif; ?>
                                        </div>

                                        <form method="post" class="inline-form compact-form">
                                            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                                            <input type="hidden" name="action" value="move_task">
                                            <input type="hidden" name="task_id" value="<?= e((string) $taskId) ?>">
                                            <label>
                                                <span>Mover</span>
                                                <select name="status">
                                                    <?php foreach ($statusOptions as $optionKey => $optionLabel): ?>
                                                        <option value="<?= e($optionKey) ?>"<?= $optionKey === $task['status'] ? ' selected' : '' ?>>
                                                            <?= e($optionLabel) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </label>
                                            <button type="submit" class="btn btn-mini">Atualizar</button>
                                        </form>

                                        <details class="task-details">
                                            <summary>Editar detalhes</summary>
                                            <div class="details-content">
                                                <form method="post" class="form-stack compact-edit" id="edit-task-<?= e((string) $taskId) ?>">
                                                    <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                                                    <input type="hidden" name="action" value="update_task">
                                                    <input type="hidden" name="task_id" value="<?= e((string) $taskId) ?>">

                                                    <label>
                                                        <span>Título</span>
                                                        <input type="text" name="title" maxlength="140" value="<?= e((string) $task['title']) ?>" required>
                                                    </label>

                                                    <label>
                                                        <span>Descrição</span>
                                                        <textarea name="description" rows="3"><?= e((string) $task['description']) ?></textarea>
                                                    </label>

                                                    <div class="form-row">
                                                        <label>
                                                            <span>Status</span>
                                                            <select name="status">
                                                                <?php foreach ($statusOptions as $optionKey => $optionLabel): ?>
                                                                    <option value="<?= e($optionKey) ?>"<?= $optionKey === $task['status'] ? ' selected' : '' ?>>
                                                                        <?= e($optionLabel) ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </label>
                                                        <label>
                                                            <span>Prioridade</span>
                                                            <select name="priority">
                                                                <?php foreach ($priorityOptions as $optionKey => $optionLabel): ?>
                                                                    <option value="<?= e($optionKey) ?>"<?= $optionKey === $task['priority'] ? ' selected' : '' ?>>
                                                                        <?= e($optionLabel) ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </label>
                                                    </div>

                                                    <div class="form-row">
                                                        <label>
                                                            <span>Responsável</span>
                                                            <select name="assigned_to">
                                                                <option value="">Sem responsável</option>
                                                                <?php foreach ($users as $user): ?>
                                                                    <option value="<?= e((string) $user['id']) ?>"<?= (int) ($task['assigned_to'] ?? 0) === (int) $user['id'] ? ' selected' : '' ?>>
                                                                        <?= e((string) $user['name']) ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </label>
                                                        <label>
                                                            <span>Prazo</span>
                                                            <input type="date" name="due_date" value="<?= e((string) ($task['due_date'] ?? '')) ?>">
                                                        </label>
                                                    </div>

                                                </form>
                                                <div class="task-edit-actions">
                                                    <button type="submit" form="edit-task-<?= e((string) $taskId) ?>" class="btn btn-mini">Salvar</button>
                                                    <form method="post" onsubmit="return confirm('Remover esta tarefa?');">
                                                        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                                                        <input type="hidden" name="action" value="delete_task">
                                                        <input type="hidden" name="task_id" value="<?= e((string) $taskId) ?>">
                                                        <button type="submit" class="btn btn-mini btn-danger">Excluir</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </details>
                                    </article>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </section>
                <?php endforeach; ?>
            </div>
        </section>
    </section>
</main>
