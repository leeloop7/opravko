<div>
    <style>
        .plan-main { width: min(920px, calc(100% - 28px)); margin: 34px auto; }
        .plan-header { display: grid; grid-template-columns: 1fr auto; gap: 22px; align-items: end; margin-bottom: 18px; }
        .plan-header h1 { margin: 0 0 4px; color: var(--ink); font-size: clamp(32px, 6vw, 52px); letter-spacing: -0.035em; }
        .plan-subtitle { margin: 0; color: var(--muted); }
        .plan-userbar { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; justify-content: space-between; margin-bottom: 16px; color: var(--muted); font-size: 14px; }
        .plan-userbar strong { color: var(--ink); }
        .plan-userbar a, .plan-userbar button.linkish {
            color: var(--teal); background: none; border: 0; padding: 0; font: inherit; cursor: pointer; text-decoration: none;
        }
        .total-progress {
            min-width: 180px; padding: 14px 16px; border-radius: 14px; background: var(--ink); color: white;
            box-shadow: 0 8px 24px rgba(23, 50, 77, .13);
        }
        .total-progress strong { display: block; font-size: 22px; }
        .total-progress span { font-size: 13px; opacity: .8; }
        .toolbar { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }
        .toolbar button, .plan-btn {
            border: 1px solid var(--line); border-radius: 999px; padding: 8px 14px; background: white;
            color: var(--text); font: inherit; font-size: 14px; cursor: pointer;
        }
        .toolbar button:hover, .plan-btn:hover { border-color: var(--teal); }
        .toolbar button.active, .plan-btn.primary { background: var(--teal); border-color: var(--teal); color: white; }
        .group {
            overflow: hidden; margin: 0 0 18px; border: 1px solid var(--line); border-radius: 16px;
            background: var(--panel); box-shadow: 0 9px 28px rgba(23, 50, 77, .06);
        }
        .group-heading {
            display: flex; justify-content: space-between; gap: 16px; align-items: center;
            padding: 15px 18px; background: var(--ink); color: white;
        }
        .group.overdue .group-heading { background: var(--red); }
        .group.august .group-heading { background: #596f8d; }
        .group.september .group-heading { background: var(--teal); }
        .group.december .group-heading { background: var(--gold); }
        .group-heading h2 { margin: 0; font-size: 17px; letter-spacing: .025em; text-transform: uppercase; }
        .group-count { font-size: 13px; opacity: .85; white-space: nowrap; }
        .tasks { list-style: none; margin: 0; padding: 0; }
        .task {
            display: grid; grid-template-columns: 23px 1fr auto; gap: 13px; align-items: start;
            min-height: 52px; padding: 13px 18px; border-top: 1px solid var(--line);
            transition: background .16s ease;
        }
        .task:first-child { border-top: 0; }
        .task:hover { background: var(--soft); }
        .task.done { background: var(--done); color: #597067; }
        .task.done .task-title { text-decoration: line-through; text-decoration-thickness: 1px; }
        .task input[type="checkbox"] {
            appearance: none; width: 23px; height: 23px; margin: 2px 0 0; border: 2px solid #9cabb5;
            border-radius: 7px; background: white; cursor: pointer;
        }
        .task input[type="checkbox"]:checked {
            border-color: var(--teal); background: var(--teal);
        }
        .task input[type="checkbox"]:checked::after {
            content: ""; display: block; width: 6px; height: 11px; margin: 2px 0 0 7px;
            border: solid white; border-width: 0 2px 2px 0; transform: rotate(45deg);
        }
        .task-title { display: block; font-weight: 600; }
        .task-meta { display: flex; flex-wrap: wrap; gap: 8px 12px; margin-top: 6px; font-size: 12px; color: var(--muted); }
        .task-actions { display: flex; flex-direction: column; gap: 6px; align-items: stretch; min-width: 120px; }
        .task-actions select, .create-form select, .create-form input, .rating-form input, .rating-form textarea, .rating-form select {
            border: 1px solid var(--line); border-radius: 10px; padding: 7px 10px; font: inherit; font-size: 13px; background: white; color: var(--text);
        }
        .rate-btn {
            border: 1px solid var(--line); border-radius: 999px; padding: 6px 10px; background: white;
            font: inherit; font-size: 12px; cursor: pointer; color: var(--text);
        }
        .rate-btn:hover { border-color: var(--gold); color: var(--gold); }
        .stars { color: var(--gold); letter-spacing: .05em; }
        .empty { padding: 22px; text-align: center; color: var(--muted); }
        .create-form, .rating-modal {
            margin-bottom: 18px; padding: 18px; border: 1px solid var(--line); border-radius: 16px;
            background: white; box-shadow: 0 9px 28px rgba(23, 50, 77, .06);
        }
        .create-form h3, .rating-modal h3 { margin: 0 0 12px; color: var(--ink); font-size: 18px; }
        .form-grid { display: grid; gap: 12px; }
        .form-actions { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 4px; }
        .error { color: var(--red); font-size: 13px; margin-top: 4px; }
        .plan-footer { margin-top: 24px; color: var(--muted); text-align: center; font-size: 13px; }
        @media (max-width: 720px) {
            .plan-main { margin-top: 22px; }
            .plan-header { grid-template-columns: 1fr; align-items: start; }
            .total-progress { width: 100%; }
            .task { grid-template-columns: 23px 1fr; }
            .task-actions { grid-column: 2; min-width: 0; }
        }
    </style>

    <main class="plan-main">
        <div class="plan-userbar">
            <div>
                Prijavljen/a kot <strong>{{ auth()->user()->name }}</strong>
            </div>
            <div style="display:flex; gap:14px; align-items:center;">
                <a href="{{ route('profile.edit') }}" wire:navigate>Nastavitve</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="linkish" type="submit">Odjava</button>
                </form>
            </div>
        </div>

        <header class="plan-header">
            <div>
                <h1>Hišni plan 2026</h1>
                <p class="plan-subtitle">Skupni seznam za Leona in Katjo — kljukice, assignee in ocene v živo.</p>
            </div>
            <div class="total-progress" aria-live="polite">
                <strong>{{ $this->totals['done'] }} / {{ $this->totals['total'] }}</strong>
                <span>opravljenih nalog</span>
            </div>
        </header>

        <nav class="toolbar" aria-label="Prikaz nalog">
            <button type="button" class="{{ $filter === 'all' ? 'active' : '' }}" wire:click="setFilter('all')">Vse</button>
            <button type="button" class="{{ $filter === 'open' ? 'active' : '' }}" wire:click="setFilter('open')">Odprto</button>
            <button type="button" class="{{ $filter === 'done' ? 'active' : '' }}" wire:click="setFilter('done')">Končano</button>
            <button type="button" class="{{ $filter === 'mine' ? 'active' : '' }}" wire:click="setFilter('mine')">Moje</button>
            @if ($showCreateForm)
                <button type="button" wire:click="closeCreateForm">Prekliči</button>
            @else
                <button type="button" class="active" wire:click="openCreateForm">+ Nova naloga</button>
            @endif
        </nav>

        @if ($showCreateForm)
            <section class="create-form">
                <h3>Nova naloga</h3>
                <form class="form-grid" wire:submit="createTask">
                    <div>
                        <input type="text" wire:model="newTitle" placeholder="Kaj je treba narediti?" autofocus>
                        @error('newTitle') <div class="error">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <select wire:model="newGroupId">
                            @foreach ($this->groupOptions as $group)
                                <option value="{{ $group->id }}">{{ $group->title }}</option>
                            @endforeach
                        </select>
                        @error('newGroupId') <div class="error">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <select wire:model="newAssignedTo">
                            <option value="">Brez assigneeja</option>
                            @foreach ($this->users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('newAssignedTo') <div class="error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-actions">
                        <button class="plan-btn primary" type="submit">Dodaj nalogo</button>
                        <button class="plan-btn" type="button" wire:click="closeCreateForm">Prekliči</button>
                    </div>
                </form>
            </section>
        @endif

        @if ($ratingTaskId && $this->ratingTask)
            <section class="rating-modal">
                <h3>Oceni: {{ $this->ratingTask->title }}</h3>
                <p class="plan-subtitle" style="margin-bottom:12px;">
                    Ocenjuješ delo: <strong>{{ $this->ratingTask->completer?->name }}</strong>
                </p>
                <form class="form-grid" wire:submit="saveRating">
                    <div>
                        <select wire:model="ratingScore">
                            @for ($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}">{{ $i }} / 5</option>
                            @endfor
                        </select>
                        @error('ratingScore') <div class="error">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <input type="text" wire:model="ratingNote" placeholder="Kratek komentar (neobvezno)">
                        @error('ratingNote') <div class="error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-actions">
                        <button class="plan-btn primary" type="submit">Shrani oceno</button>
                        <button class="plan-btn" type="button" wire:click="closeRating">Prekliči</button>
                    </div>
                </form>
            </section>
        @endif

        @foreach ($this->groups as $groupData)
            @php
                /** @var \App\Models\TaskGroup $group */
                $group = $groupData['model'];
            @endphp
            <section class="group {{ $groupData['tone'] }}" wire:key="group-{{ $group->id }}">
                <div class="group-heading">
                    <h2>{{ $group->title }}</h2>
                    <span class="group-count">{{ $groupData['done'] }} / {{ $groupData['total'] }}</span>
                </div>

                @if ($groupData['tasks']->isEmpty())
                    <p class="empty">V tem sklopu ni nalog za izbrani prikaz.</p>
                @else
                    <ul class="tasks">
                        @foreach ($groupData['tasks'] as $task)
                            <li class="task {{ $task->isCompleted() ? 'done' : '' }}" wire:key="task-{{ $task->id }}">
                                <input
                                    id="task-{{ $task->id }}"
                                    type="checkbox"
                                    @checked($task->isCompleted())
                                    wire:click="toggleTask({{ $task->id }})"
                                >
                                <label for="task-{{ $task->id }}">
                                    <span class="task-title">{{ $task->title }}</span>
                                    <div class="task-meta">
                                        @if ($task->assignee)
                                            <span>Dodeli: {{ $task->assignee->name }}</span>
                                        @else
                                            <span>Brez assigneeja</span>
                                        @endif

                                        @if ($task->isCompleted())
                                            <span>
                                                Končano
                                                {{ $task->completed_at?->timezone(config('app.timezone'))->format('d. m. Y H:i') }}
                                                @if ($task->completer)
                                                    · {{ $task->completer->name }}
                                                @endif
                                            </span>
                                        @endif

                                        @foreach ($task->ratings as $rating)
                                            <span class="stars" title="{{ $rating->note }}">
                                                {{ $rating->rater->name }}: {{ str_repeat('★', $rating->score) }}{{ str_repeat('☆', 5 - $rating->score) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </label>
                                <div class="task-actions">
                                    <select
                                        wire:change="assignTask({{ $task->id }}, $event.target.value || null)"
                                        aria-label="Dodeli nalogo"
                                    >
                                        <option value="">Brez assigneeja</option>
                                        @foreach ($this->users as $user)
                                            <option value="{{ $user->id }}" @selected($task->assigned_to === $user->id)>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @if ($task->isCompleted() && $task->completed_by && $task->completed_by !== auth()->id())
                                        <button
                                            type="button"
                                            class="rate-btn"
                                            wire:click="openRating({{ $task->id }})"
                                        >
                                            {{ $task->ratings->firstWhere('rater_id', auth()->id()) ? 'Uredi oceno' : 'Oceni' }}
                                        </button>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        @endforeach

        <footer class="plan-footer">Leon in Katja · Hišni plan 2026</footer>
    </main>
</div>
