<div>
    <style>
        .plan-main {
            width: min(920px, calc(100% - 32px));
            margin: 28px auto 48px;
            position: relative;
            z-index: 1;
        }

        .plan-userbar {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            padding-bottom: 14px;
            border-bottom: 1px solid var(--op-line);
            color: var(--op-muted);
            font-size: 14px;
        }

        .plan-userbar__brand {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }

        .plan-userbar__meta {
            padding-left: 14px;
            border-left: 1px solid var(--op-line-strong);
            color: var(--op-faint);
            letter-spacing: 0.01em;
        }

        .plan-userbar strong { color: var(--op-text); font-weight: 560; }

        .plan-userbar a,
        .plan-userbar button.linkish {
            color: var(--op-muted);
            background: none;
            border: 0;
            padding: 0;
            font: inherit;
            cursor: pointer;
            text-decoration: none;
            transition: color .15s ease;
        }

        .plan-userbar a:hover,
        .plan-userbar button.linkish:hover { color: var(--op-teal); }

        .plan-userbar__actions { display: flex; gap: 16px; align-items: center; }

        .plan-subtitle {
            margin: 0;
            color: var(--op-muted);
            font-size: 15px;
            line-height: 1.45;
            max-width: 36ch;
        }

        .toolbar-row {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
        }

        .total-progress {
            display: inline-flex;
            align-items: baseline;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 10px;
            background: linear-gradient(180deg, rgba(255,255,255,0.04), rgba(255,255,255,0.015));
            border: 1px solid var(--op-line-strong);
            color: var(--op-text);
            white-space: nowrap;
        }

        .total-progress strong {
            font-size: 15px;
            font-weight: 650;
            letter-spacing: -0.02em;
            color: var(--op-teal);
        }

        .total-progress span {
            font-size: 12px;
            color: var(--op-muted);
            letter-spacing: 0.02em;
        }

        .toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .toolbar button,
        .plan-btn {
            border: 1px solid var(--op-line-strong);
            border-radius: 10px;
            padding: 8px 13px;
            background: rgba(255,255,255,0.02);
            color: var(--op-muted);
            font: inherit;
            font-size: 13.5px;
            font-weight: 500;
            cursor: pointer;
            transition: background .15s ease, border-color .15s ease, color .15s ease;
        }

        .toolbar button:hover,
        .plan-btn:hover {
            border-color: rgba(110, 184, 176, 0.45);
            color: var(--op-text);
            background: rgba(255,255,255,0.04);
        }

        .toolbar button.active,
        .plan-btn.primary {
            background: var(--op-teal-soft);
            border-color: rgba(110, 184, 176, 0.45);
            color: #d7f3ef;
        }

        .group {
            overflow: hidden;
            margin: 0 0 14px;
            border: 1px solid var(--op-line);
            border-radius: calc(var(--op-radius) + 2px);
            background: linear-gradient(180deg, rgba(255,255,255,0.03), rgba(255,255,255,0.015));
        }

        .group-heading {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: center;
            width: 100%;
            padding: 14px 18px;
            border: 0;
            border-bottom: 1px solid var(--op-line);
            background: rgba(0,0,0,0.14);
            color: inherit;
            font: inherit;
            text-align: left;
            cursor: pointer;
            transition: background .15s ease;
        }

        .group-heading:hover { background: rgba(255,255,255,0.03); }

        .group.is-collapsed .group-heading { border-bottom-color: transparent; }

        .group-heading__left {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .group-chevron {
            width: 16px;
            height: 16px;
            flex: 0 0 16px;
            color: var(--op-faint);
            transition: transform .18s ease;
        }

        .group-chevron svg {
            width: 16px;
            height: 16px;
            stroke: currentColor;
            fill: none;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .group.is-collapsed .group-chevron { transform: rotate(-90deg); }

        .group-heading h2 {
            margin: 0;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--op-muted);
        }

        .group-count {
            font-size: 12px;
            color: var(--op-faint);
            white-space: nowrap;
            font-variant-numeric: tabular-nums;
        }

        .group-body {
            display: block;
        }

        .group.is-collapsed .group-body {
            display: none;
        }

        .group.overdue .group-heading { box-shadow: inset 3px 0 0 var(--op-rose); }
        .group.overdue .group-heading h2 { color: #efc0bc; }
        .group.august .group-heading { box-shadow: inset 3px 0 0 var(--op-slate); }
        .group.august .group-heading h2 { color: #c5d0da; }
        .group.september .group-heading { box-shadow: inset 3px 0 0 var(--op-teal); }
        .group.september .group-heading h2 { color: #c7ebe6; }
        .group.december .group-heading { box-shadow: inset 3px 0 0 var(--op-gold); }
        .group.december .group-heading h2 { color: #edd7b3; }
        .group.later .group-heading { box-shadow: inset 3px 0 0 #7f8b96; }
        .group.later .group-heading h2 { color: #b7c2cb; }
        .group.none .group-heading { box-shadow: inset 3px 0 0 #66727c; }
        .group.none .group-heading h2 { color: #9aa6b0; }

        .due-presets {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .due-presets button {
            border: 1px solid var(--op-line-strong);
            border-radius: 999px;
            padding: 6px 10px;
            background: rgba(255,255,255,0.03);
            color: var(--op-muted);
            font: inherit;
            font-size: 12px;
            cursor: pointer;
        }

        .due-presets button:hover {
            color: var(--op-text);
            border-color: rgba(47, 214, 168, 0.4);
        }

        .person-chip--due {
            border-color: rgba(143, 160, 176, 0.35);
            background: rgba(143, 160, 176, 0.1);
        }

        .person-chip--due .person-chip__avatar {
            background: rgba(143, 160, 176, 0.25);
            color: #dde7ef;
        }

        .person-chip--due-overdue {
            border-color: rgba(255, 126, 179, 0.45);
            background: rgba(255, 126, 179, 0.12);
        }

        .person-chip--due-overdue .person-chip__avatar {
            background: rgba(255, 126, 179, 0.28);
            color: #ffe7f2;
        }

        .tasks { list-style: none; margin: 0; padding: 0; }

        .task {
            display: grid;
            grid-template-columns: 22px 1fr auto;
            gap: 14px;
            align-items: start;
            min-height: 56px;
            padding: 14px 18px;
            border-top: 1px solid var(--op-line);
            transition: background .16s ease, box-shadow .16s ease;
            box-shadow: inset 3px 0 0 transparent;
        }

        .task:first-child { border-top: 0; }
        .task:hover { filter: brightness(1.06); }

        .task--leon {
            background: rgba(47, 214, 168, 0.14);
            box-shadow: inset 4px 0 0 var(--op-leon);
        }

        .task--katja {
            background: rgba(255, 110, 168, 0.14);
            box-shadow: inset 4px 0 0 var(--op-katja);
        }

        .task--unassigned {
            background: rgba(109, 122, 132, 0.08);
            box-shadow: inset 4px 0 0 #7a8791;
        }

        .task.done { opacity: 0.78; }
        .task.done .task-title {
            color: var(--op-muted);
            text-decoration: line-through;
            text-decoration-thickness: 1px;
            text-decoration-color: rgba(147, 161, 171, 0.55);
        }

        .task input[type="checkbox"] {
            appearance: none;
            width: 22px;
            height: 22px;
            margin: 2px 0 0;
            border: 1.5px solid rgba(232, 238, 242, 0.28);
            border-radius: 7px;
            background: rgba(255,255,255,0.02);
            cursor: pointer;
            transition: border-color .15s ease, background .15s ease;
        }

        .task input[type="checkbox"]:hover { border-color: var(--op-teal); }

        .task input[type="checkbox"]:checked {
            border-color: var(--op-teal);
            background: var(--op-teal);
        }

        .task input[type="checkbox"]:checked::after {
            content: "";
            display: block;
            width: 6px;
            height: 10px;
            margin: 2px 0 0 7px;
            border: solid #102018;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .task input[type="checkbox"]:focus-visible {
            outline: 2px solid rgba(110, 184, 176, 0.45);
            outline-offset: 2px;
        }

        label { cursor: pointer; width: 100%; }

        .task-title {
            display: block;
            font-weight: 560;
            font-size: 15.5px;
            letter-spacing: -0.015em;
            color: var(--op-text);
            line-height: 1.35;
        }

        .task-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 9px;
            align-items: center;
        }

        .person-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            max-width: 100%;
            padding: 4px 9px 4px 5px;
            border-radius: 999px;
            border: 1px solid var(--op-line-strong);
            background: rgba(255,255,255,0.03);
            color: var(--op-muted);
            font-size: 12px;
            line-height: 1.2;
            white-space: nowrap;
        }

        .person-chip__avatar {
            display: inline-grid;
            place-items: center;
            width: 18px;
            height: 18px;
            border-radius: 999px;
            background: rgba(110, 184, 176, 0.18);
            color: #d7f3ef;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0;
        }

        .person-chip__label {
            color: var(--op-faint);
            font-size: 10.5px;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .person-chip__name {
            color: var(--op-text);
            font-weight: 560;
        }

        .person-chip__time {
            color: var(--op-faint);
        }

        .person-chip--leon {
            border-color: rgba(47, 214, 168, 0.45);
            background: rgba(47, 214, 168, 0.16);
        }

        .person-chip--leon .person-chip__avatar {
            background: rgba(47, 214, 168, 0.35);
            color: #d9fff3;
        }

        .person-chip--katja {
            border-color: rgba(255, 110, 168, 0.5);
            background: rgba(255, 110, 168, 0.16);
        }

        .person-chip--katja .person-chip__avatar {
            background: rgba(255, 110, 168, 0.38);
            color: #ffe7f2;
        }

        .person-chip--done {
            border-color: rgba(201, 165, 109, 0.28);
            background: rgba(201, 165, 109, 0.08);
        }

        .person-chip--done .person-chip__avatar {
            background: rgba(201, 165, 109, 0.22);
            color: #f0e0c4;
        }

        .person-chip--empty {
            border-style: dashed;
            color: var(--op-faint);
        }

        .person-chip--empty .person-chip__name {
            color: var(--op-faint);
            font-weight: 500;
        }

        .task-actions {
            display: flex;
            flex-direction: column;
            gap: 8px;
            align-items: flex-end;
            min-width: 44px;
        }

        .icon-stack {
            display: flex;
            flex-direction: column;
            gap: 4px;
            align-items: stretch;
        }

        .icon-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border: 1px solid var(--op-line-strong);
            border-radius: 10px;
            background: rgba(255,255,255,0.03);
            color: var(--op-muted);
            cursor: pointer;
            transition: border-color .15s ease, color .15s ease, background .15s ease;
        }

        .icon-btn svg {
            width: 16px;
            height: 16px;
            stroke: currentColor;
            fill: none;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .icon-btn:hover {
            color: var(--op-text);
            border-color: rgba(232, 238, 242, 0.28);
            background: rgba(255,255,255,0.06);
        }

        .icon-btn.active {
            color: #d9fff3;
            border-color: rgba(47, 214, 168, 0.45);
            background: rgba(47, 214, 168, 0.14);
        }

        .icon-btn--danger:hover {
            color: #ffc2d4;
            border-color: rgba(255, 110, 168, 0.5);
            background: rgba(255, 110, 168, 0.12);
        }

        .assign-btn {
            position: relative;
            overflow: hidden;
        }

        .assign-btn select {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
            border: 0;
            background: transparent;
            font-size: 16px; /* iOS ne zoomira */
        }

        .assign-btn:focus-within {
            color: #d9fff3;
            border-color: rgba(47, 214, 168, 0.45);
            background: rgba(47, 214, 168, 0.14);
        }

        .modal-card select,
        .modal-card input {
            border: 1px solid var(--op-line-strong);
            border-radius: 10px;
            padding: 8px 10px;
            font: inherit;
            font-size: 13px;
            background: var(--op-panel);
            color: var(--op-text);
            width: 100%;
        }

        .modal-card select:focus,
        .modal-card input:focus {
            outline: none;
            border-color: rgba(47, 214, 168, 0.55);
            box-shadow: 0 0 0 3px rgba(47, 214, 168, 0.12);
        }

        .rate-btn {
            border: 1px solid var(--op-line-strong);
            border-radius: 10px;
            padding: 7px 10px;
            background: transparent;
            font: inherit;
            font-size: 12px;
            cursor: pointer;
            color: var(--op-muted);
            transition: border-color .15s ease, color .15s ease, background .15s ease;
            width: 100%;
        }

        .rate-btn:hover {
            border-color: rgba(201, 165, 109, 0.5);
            color: var(--op-gold);
            background: var(--op-gold-soft);
        }

        .stars { color: var(--op-gold); letter-spacing: .04em; }

        .empty {
            padding: 26px 18px;
            text-align: center;
            color: var(--op-faint);
            font-size: 14px;
        }

        .modal-root {
            position: fixed;
            inset: 0;
            z-index: 60;
            display: grid;
            place-items: center;
            padding: 20px;
        }

        .modal-backdrop {
            position: absolute;
            inset: 0;
            border: 0;
            padding: 0;
            background: rgba(6, 9, 12, 0.72);
            backdrop-filter: blur(4px);
            cursor: pointer;
        }

        .modal-card {
            position: relative;
            z-index: 1;
            width: min(440px, 100%);
            max-height: min(86vh, 720px);
            overflow: auto;
            padding: 20px;
            border: 1px solid var(--op-line-strong);
            border-radius: calc(var(--op-radius) + 4px);
            background:
                linear-gradient(180deg, rgba(255,255,255,0.05), rgba(255,255,255,0.02)),
                #151a1f;
            box-shadow: 0 24px 64px rgba(0, 0, 0, 0.45);
        }

        .modal-card h3 {
            margin: 0 0 14px;
            color: var(--op-text);
            font-size: 18px;
            font-weight: 650;
            letter-spacing: -0.02em;
            padding-right: 36px;
        }

        .modal-close {
            position: absolute;
            top: 14px;
            right: 14px;
            width: 32px;
            height: 32px;
            display: inline-grid;
            place-items: center;
            border: 1px solid var(--op-line-strong);
            border-radius: 10px;
            background: rgba(255,255,255,0.03);
            color: var(--op-muted);
            cursor: pointer;
            font: inherit;
            font-size: 18px;
            line-height: 1;
        }

        .modal-close:hover {
            color: var(--op-text);
            border-color: rgba(232, 238, 242, 0.28);
        }

        .form-grid { display: grid; gap: 12px; }
        .form-actions { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 2px; }
        .error { color: var(--op-rose); font-size: 13px; margin-top: 4px; }

        .plan-footer {
            margin-top: 28px;
            color: var(--op-faint);
            text-align: center;
            font-size: 12.5px;
            letter-spacing: 0.02em;
        }

        @media (max-width: 720px) {
            .plan-main { margin-top: 20px; }
            .toolbar-row { align-items: stretch; }
            .total-progress { width: 100%; justify-content: center; }
            .task { grid-template-columns: 22px 1fr auto; padding-inline: 14px; }
            .task-actions { min-width: 40px; }
            .plan-userbar__meta { display: none; }
        }
    </style>

    <main class="plan-main">
        <div class="plan-userbar">
            <div class="plan-userbar__brand">
                <a href="{{ route('plan') }}" wire:navigate aria-label="Opravko">
                    <x-opravko-wordmark size="sm" :on-dark="true" />
                </a>
                <span class="plan-userbar__meta">
                    <strong>{{ auth()->user()->name }}</strong>
                </span>
            </div>
            <div class="plan-userbar__actions">
                <a href="{{ route('profile.edit') }}" wire:navigate>Nastavitve</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="linkish" type="submit">Odjava</button>
                </form>
            </div>
        </div>

        <div class="toolbar-row">
            <nav class="toolbar" aria-label="Prikaz nalog">
                <button type="button" class="{{ $filter === 'all' ? 'active' : '' }}" wire:click="setFilter('all')">Vse</button>
                <button type="button" class="{{ $filter === 'open' ? 'active' : '' }}" wire:click="setFilter('open')">Odprto</button>
                <button type="button" class="{{ $filter === 'done' ? 'active' : '' }}" wire:click="setFilter('done')">Končano</button>
                <button type="button" class="{{ $filter === 'mine' ? 'active' : '' }}" wire:click="setFilter('mine')">Moje</button>
                <button type="button" class="active" wire:click="openCreateForm">+ Nova naloga</button>
            </nav>
            <div class="total-progress" aria-live="polite">
                <strong>{{ $this->totals['done'] }} / {{ $this->totals['total'] }}</strong>
                <span>opravljenih</span>
            </div>
        </div>

        @foreach ($this->groups as $groupData)
            @php
                $groupKey = $groupData['key'];
                $isOpen = $this->isGroupOpen($groupKey);
            @endphp
            <section
                class="group {{ $groupData['tone'] }} {{ $isOpen ? '' : 'is-collapsed' }}"
                wire:key="group-{{ $groupKey }}"
            >
                <button
                    type="button"
                    class="group-heading"
                    wire:click="toggleGroup('{{ $groupKey }}')"
                    aria-expanded="{{ $isOpen ? 'true' : 'false' }}"
                    aria-controls="group-body-{{ $groupKey }}"
                >
                    <span class="group-heading__left">
                        <span class="group-chevron" aria-hidden="true">
                            <svg viewBox="0 0 24 24">
                                <path d="m6 9 6 6 6-6"/>
                            </svg>
                        </span>
                        <h2>{{ $groupData['title'] }}</h2>
                    </span>
                    <span class="group-count">{{ $groupData['done'] }} / {{ $groupData['total'] }}</span>
                </button>

                <div class="group-body" id="group-body-{{ $groupKey }}">
                @if ($groupData['tasks']->isEmpty())
                    <p class="empty">V tem sklopu ni nalog za izbrani prikaz.</p>
                @else
                    <ul class="tasks">
                        @foreach ($groupData['tasks'] as $task)
                            @php
                                $assigneeAccent = $task->assignee?->accent() ?? 'unassigned';
                                $taskTone = match ($assigneeAccent) {
                                    'leon' => 'task--leon',
                                    'katja' => 'task--katja',
                                    default => 'task--unassigned',
                                };
                                $isOverdue = $task->due_date
                                    && $task->due_date->startOfDay()->lt(now()->startOfDay())
                                    && ! $task->isCompleted();
                            @endphp
                            <li class="task {{ $taskTone }} {{ $task->isCompleted() ? 'done' : '' }}" wire:key="task-{{ $task->id }}">
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
                                            <span class="person-chip person-chip--{{ $task->assignee->accent() }}" title="Dodeljeno">
                                                <span class="person-chip__avatar">{{ $task->assignee->initials() }}</span>
                                                <span class="person-chip__label">Za:</span>
                                                <span class="person-chip__name">{{ $task->assignee->name }}</span>
                                            </span>
                                        @else
                                            <span class="person-chip person-chip--empty">
                                                <span class="person-chip__label">Za:</span>
                                                <span class="person-chip__name">nedodeljeno</span>
                                            </span>
                                        @endif

                                        @if ($task->due_date)
                                            <span class="person-chip {{ $isOverdue ? 'person-chip--due-overdue' : 'person-chip--due' }}" title="Rok">
                                                <span class="person-chip__avatar">R</span>
                                                <span class="person-chip__label">Rok:</span>
                                                <span class="person-chip__name">{{ $task->due_date->format('d. m. Y') }}</span>
                                            </span>
                                        @endif

                                        @if ($task->isCompleted() && $task->completer)
                                            <span class="person-chip person-chip--done" title="Zaključil/a nalogo">
                                                <span class="person-chip__avatar">{{ $task->completer->initials() }}</span>
                                                <span class="person-chip__label">Nalogo zaključil:</span>
                                                <span class="person-chip__name">{{ $task->completer->name }}</span>
                                                <span class="person-chip__time">
                                                    · {{ $task->completed_at?->timezone(config('app.timezone'))->format('d. m. Y H:i') }}
                                                </span>
                                            </span>
                                        @endif

                                        @foreach ($task->ratings as $rating)
                                            <span class="stars" title="{{ $rating->note }}">
                                                {{ $rating->rater->name }} → {{ $rating->ratee?->name }}:
                                                {{ str_repeat('★', $rating->score) }}{{ str_repeat('☆', 5 - $rating->score) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </label>
                                <div class="task-actions">
                                    <div class="icon-stack">
                                        <div class="icon-btn assign-btn" title="Assign to">
                                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                                <circle cx="9" cy="7" r="4"/>
                                                <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                            </svg>
                                            <select
                                                wire:change="assignTask({{ $task->id }}, $event.target.value || null)"
                                                aria-label="Assign to"
                                            >
                                                <option value="">Nedodeljeno</option>
                                                @foreach ($this->users as $user)
                                                    <option value="{{ $user->id }}" @selected($task->assigned_to === $user->id)>
                                                        {{ $user->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <button
                                            type="button"
                                            class="icon-btn"
                                            title="Edit"
                                            aria-label="Edit"
                                            wire:click="openEditForm({{ $task->id }})"
                                        >
                                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="M12 20h9"/>
                                                <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                                            </svg>
                                        </button>

                                        <button
                                            type="button"
                                            class="icon-btn icon-btn--danger"
                                            title="Delete"
                                            aria-label="Delete"
                                            wire:click="deleteTask({{ $task->id }})"
                                            wire:confirm="Res želiš zbrisati to nalogo?"
                                        >
                                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="M3 6h18"/>
                                                <path d="M8 6V4h8v2"/>
                                                <path d="M19 6l-1 14H6L5 6"/>
                                                <path d="M10 11v6"/>
                                                <path d="M14 11v6"/>
                                            </svg>
                                        </button>
                                    </div>

                                    @if ($task->canBeRatedBy(auth()->user()))
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
                </div>
            </section>
        @endforeach

        <footer class="plan-footer">Leon in Katja · Opravko</footer>
    </main>

    @if ($showCreateForm)
        <div class="modal-root" wire:key="task-modal">
            <button
                type="button"
                class="modal-backdrop"
                aria-label="Zapri"
                wire:click="closeCreateForm"
            ></button>
            <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="task-modal-title">
                <button type="button" class="modal-close" wire:click="closeCreateForm" aria-label="Zapri">×</button>
                <h3 id="task-modal-title">{{ $editingTaskId ? 'Uredi nalogo' : 'Nova naloga' }}</h3>
                <form class="form-grid" wire:submit="saveTask">
                    <div>
                        <input type="text" wire:model="newTitle" placeholder="Kaj je treba narediti?" autofocus>
                        @error('newTitle') <div class="error">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <div class="due-presets" style="margin-bottom:8px">
                            <button type="button" wire:click="setDuePreset('7')">+7 dni</button>
                            <button type="button" wire:click="setDuePreset('14')">+14 dni</button>
                            <button type="button" wire:click="setDuePreset('week')">Konec tedna</button>
                            <button type="button" wire:click="setDuePreset('month')">Konec meseca</button>
                            <button type="button" wire:click="setDuePreset('none')">Brez roka</button>
                        </div>
                        <input type="date" wire:model="newDueDate">
                        @error('newDueDate') <div class="error">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <select wire:model="newAssignedTo">
                            <option value="">Nedodeljeno</option>
                            @foreach ($this->users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('newAssignedTo') <div class="error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-actions">
                        <button class="plan-btn primary" type="submit">
                            {{ $editingTaskId ? 'Shrani spremembe' : 'Dodaj nalogo' }}
                        </button>
                        <button class="plan-btn" type="button" wire:click="closeCreateForm">Prekliči</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if ($ratingTaskId && $this->ratingTask && $this->ratingRatee)
        <div class="modal-root" wire:key="rating-modal">
            <button
                type="button"
                class="modal-backdrop"
                aria-label="Zapri"
                wire:click="closeRating"
            ></button>
            <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="rating-modal-title">
                <button type="button" class="modal-close" wire:click="closeRating" aria-label="Zapri">×</button>
                <h3 id="rating-modal-title">Oceni: {{ $this->ratingTask->title }}</h3>
                <p class="plan-subtitle" style="margin-bottom:12px; max-width:none;">
                    Ocenjuješ:
                    <strong style="color:var(--op-text)">{{ $this->ratingRatee->name }}</strong>
                    @if ($this->ratingTask->completed_by === auth()->id() && $this->ratingTask->assigned_to !== auth()->id())
                        <span style="color:var(--op-faint)"> · izvajalec naloge</span>
                    @else
                        <span style="color:var(--op-faint)"> · zaključil/a nalogo</span>
                    @endif
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
            </div>
        </div>
    @endif
</div>
