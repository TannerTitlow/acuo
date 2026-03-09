# acuo — Product Specification v8
*Sharpen your process. Quiet the noise.*

> **acuo** /ˈa-ku-o/ (verb) — I make pointed, sharpen, whet. I exercise, practice, improve. I spur, stimulate, arouse.

---

## Vision

Most productivity apps assume you already know how to plan. acuo doesn't.

It's built for people whose biggest obstacle isn't motivation — it's the fog between wanting to do something and knowing how to start. ADHD, executive dysfunction, decision paralysis: these aren't laziness, they're a clarity problem. acuo is the sharpening tool.

The philosophy has three layers:
1. **Get to Work** — eliminate the planning tax; generate a clear first step automatically
2. **Tangible Road Map** — replace deadline anxiety with a visible path forward
3. **Learn** — over time, help users internalize better planning instincts through AI feedback loops

Built as a **responsive PWA** — installable, offline-capable, works identically on desktop and mobile.

---

## Core Design Principles

### 1. Blur → Sharpen
One primary action per view. No walls of tasks. If a screen feels overwhelming, it's wrong.

### 2. Progress = Positive Momentum
Color Flip principle: green fills *in* as work is done — red never creeps in as deadlines approach.

### 3. Friction-First Design
Every feature asks: *does this reduce the activation energy to begin?*

### 4. Subtly ADHD-Aware
Thoughtful UX patterns throughout — short task chunks, positive reinforcement, no punishing visual cues — without ever labeling the user.

### 5. Minimal but Complete
Restraint is a feature.

### 6. Rewards Tied to Quality, Not Volume
XP weighting rewards planning well, not just doing a lot.

---

## Tech Stack

| Layer | Choice | Notes |
|---|---|---|
| Backend | Laravel 12 (PHP 8.4) | API-only, REST |
| Frontend | Vue 3 + Vite | SPA, Composition API |
| Database | PostgreSQL | Via Supabase or self-hosted |
| Auth | Laravel Sanctum (via `install:api`) | SPA cookie-based auth |
| AI | Anthropic Claude API | Generation, replanning, feedback loops |
| PWA | Vite PWA Plugin | Service worker, installable, offline shell |
| Styling | Tailwind CSS | Utility-first |
| State | Pinia | Vue 3 native store |
| Queues | Laravel Horizon + Redis | Redis-only; manage via `php artisan horizon` |
| Scheduling | Laravel Scheduler (`routes/console.php`) | Sanctum token pruning, stats cache updates |
| Calendar | Google Calendar API | V2 — two-way sync |
| Deployment | Laravel Forge + DigitalOcean | Run `php artisan optimize` on deploy |

---

## Windows Development Environment — WSL2 + Ubuntu

acuo is developed on **Windows using WSL2 (Windows Subsystem for Linux 2) with Ubuntu**. Everything runs inside the Linux subsystem — PHP, Composer, Node, PostgreSQL, Redis. The Windows browser accesses `localhost:8000` and `localhost:5173` transparently via WSL2's network bridge.

> **Rule #1:** Keep all project files inside the WSL2 filesystem (`~/projects/`), never on the Windows drive (`/mnt/c/`). Cross-filesystem I/O is slow — Vite, Composer, and Artisan all feel laggy when files live on the Windows side.

---

### 1. Enable WSL2 + Install Ubuntu

Run in **PowerShell as Administrator**:

```powershell
wsl --install
# Installs WSL2 + Ubuntu by default. Restart when prompted.
```

If WSL is already installed at version 1:
```powershell
wsl --set-default-version 2
wsl --install -d Ubuntu
```

After restart, open Ubuntu from the Start Menu and create your Unix username + password (separate from your Windows login).

---

### 2. Install the Dev Stack Inside Ubuntu

Everything below runs in your **WSL2 Ubuntu terminal**.

```bash
# Update packages
sudo apt update && sudo apt upgrade -y

# Required for adding PPAs
sudo apt install -y software-properties-common curl ca-certificates

# PHP 8.4 + required extensions
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.4 php8.4-cli php8.4-fpm php8.4-mbstring \
  php8.4-xml php8.4-bcmath php8.4-curl php8.4-zip \
  php8.4-pgsql php8.4-redis php8.4-tokenizer

# Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Node.js via NVM
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.40.1/install.sh | bash
source ~/.bashrc
nvm install --lts
nvm use --lts

# PostgreSQL 17 via official pgdg repo
sudo install -d /usr/share/postgresql-common/pgdg
sudo curl -o /usr/share/postgresql-common/pgdg/apt.postgresql.org.asc --fail \
  https://www.postgresql.org/media/keys/ACCC4CF8.asc
sudo sh -c 'echo "deb [signed-by=/usr/share/postgresql-common/pgdg/apt.postgresql.org.asc] \
  https://apt.postgresql.org/pub/repos/apt $(lsb_release -cs)-pgdg main" \
  > /etc/apt/sources.list.d/pgdg.list'
sudo apt update
sudo apt install -y postgresql-17
sudo service postgresql start
sudo -u postgres psql -c "CREATE USER tanner WITH SUPERUSER;"
sudo -u postgres psql -c "ALTER USER tanner WITH PASSWORD 'Tilion';"
sudo -u postgres createdb acuo_dev

# Redis
sudo apt install -y redis-server
sudo service redis-server start
```

**Verify:**
```bash
php --version       # 8.4.x
composer --version
node --version
psql --version
redis-cli ping      # PONG
```

---

### 3. Laravel Installer

```bash
composer global require laravel/installer

# Add Composer global bin to PATH
echo 'export PATH="$HOME/.config/composer/vendor/bin:$PATH"' >> ~/.bashrc
source ~/.bashrc

# Verify
laravel --version
```

---

### 4. VS Code + WSL2

Install the **WSL extension** in VS Code on Windows. Then from Ubuntu:

```bash
cd ~/projects/acuo
code .   # opens Windows VS Code connected to the WSL2 filesystem
```

This gives you Windows VS Code with full WSL2 integration — the terminal runs in Ubuntu, file watching works correctly, and Claude Code works natively. It's seamless.

---

### 5. Project File Location

```bash
# Correct — fast, Linux-native filesystem
~/projects/acuo             # Laravel backend
~/projects/acuo-frontend    # Vue frontend

# Avoid — slow cross-filesystem I/O
/mnt/c/Users/Tanner/projects/acuo
```

---

### 6. Service Startup

WSL2 doesn't auto-start services on boot by default. Add to `~/.bashrc` or use a startup script:

```bash
# ~/.bashrc — auto-start on WSL open
sudo service postgresql start
sudo service redis-server start
```

Or a manual startup script:
```bash
# ~/start-dev.sh
#!/bin/bash
sudo service postgresql start
sudo service redis-server start
echo "PostgreSQL ✓  Redis ✓"
```
```bash
chmod +x ~/start-dev.sh
```

---

### 7. Running All Services (Daily Dev Flow)

Open four Ubuntu terminal tabs (or use VS Code's split terminal):

```bash
# Tab 1 — Laravel API
cd ~/projects/acuo && php artisan serve

# Tab 2 — Horizon (queue worker)
cd ~/projects/acuo && php artisan horizon

# Tab 3 — Scheduler (dev only — simulates cron)
cd ~/projects/acuo && php artisan schedule:work

# Tab 4 — Vue frontend
cd ~/projects/acuo-frontend && npm run dev
```

Both `localhost:8000` (API) and `localhost:5173` (Vue) are accessible from your **Windows browser** automatically.

---

### 8. Laravel `.env` for WSL2 + PostgreSQL

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=acuo_dev
DB_USERNAME=postgres
DB_PASSWORD=Tilion

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

SANCTUM_STATEFUL_DOMAINS=localhost:5173
SESSION_DOMAIN=localhost
FRONTEND_URL=http://localhost:5173
```

---

## Laravel 12 — Key Implementation Notes

These are version-specific details pulled from the Laravel 12 docs that directly affect how acuo is built. Read these before starting each relevant session.

---

### Installation & Project Setup

```bash
# Requires PHP 8.4, Composer, Node/NPM
laravel new acuo
cd acuo

# Install Sanctum via the official Laravel 12 API install command
# (do NOT use composer require laravel/sanctum directly — this is the L12 way)
php artisan install:api

# Install Horizon for Redis queue management
composer require laravel/horizon
php artisan horizon:install
php artisan horizon:publish
```

**Laravel 12 ships with Carbon 3.x** — Carbon 2 support is removed. Ensure any date manipulation uses Carbon 3 syntax (largely compatible but check for edge cases).

---

### Sanctum — SPA Authentication

acuo uses Sanctum's **cookie-based SPA authentication**, not token-based. The Vue frontend and Laravel API are served from the same domain (or configured subdomain), so cookie auth is simpler and more secure than managing tokens.

```php
// routes/api.php — protect all API routes
Route::middleware('auth:sanctum')->group(function () {
    // all authenticated routes go here
});
```

**CORS configuration** — set `SANCTUM_STATEFUL_DOMAINS` in `.env` to your Vue dev server:
```
SANCTUM_STATEFUL_DOMAINS=localhost:5173
SESSION_DOMAIN=localhost
```

**Token pruning** — schedule in `routes/console.php`:
```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('sanctum:prune-expired --hours=24')->daily();
```

---

### Models — UUIDs (Laravel 12 Change)

**Important Laravel 12 change:** `HasUuids` now generates **UUIDv7** (ordered) by default. This is actually better for acuo — UUIDv7 is time-ordered, which means better database index performance than random UUIDs.

```php
// All acuo models use UUIDv7 primary keys
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Project extends Model
{
    use HasUuids; // generates UUIDv7 in Laravel 12
}
```

**Schema migration impact** — use `uuid` column type, not `id()`:
```php
$table->uuid('id')->primary();
$table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
```

---

### Eloquent Observers — XP Triggers

The XP system is implemented via **Eloquent Observers**, keeping XP logic fully decoupled from controllers. Register observers in `AppServiceProvider` using the `#[ObservedBy]` attribute (Laravel 12 approach):

```php
// app/Models/Task.php
use App\Observers\TaskObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([TaskObserver::class])]
class Task extends Model
{
    // ...
}

// app/Observers/TaskObserver.php
class TaskObserver
{
    public function updated(Task $task): void
    {
        // Fire when completed_at transitions from null to a value
        if ($task->isDirty('completed_at') && $task->completed_at !== null) {
            AwardXpJob::dispatch($task->project->user_id, 'task', $task->id);
        }
    }
}
```

**Apply the same pattern to:**
- `HabitLog` → `created` event → award XP for habit log
- `Project` → `updated` event → watch for `status` changing to `completed`
- `UserAchievement` → `created` event → award achievement XP bonus

**All XP jobs should be queued** (dispatched to Horizon via Redis) — never block the request.

---

### Queues & Horizon

Horizon manages **Redis queues only**. Ensure your queue connection is set to `redis` before using Horizon.

```env
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

**Horizon configuration** (`config/horizon.php`) — define named queues for acuo's different job types:

```php
'environments' => [
    'production' => [
        'supervisor-1' => [
            'maxProcesses' => 10,
            'balanceMaxShift' => 1,
            'balanceCooldown' => 3,
            'queues' => ['xp', 'ai', 'notifications', 'default'],
        ],
    ],
    'local' => [
        'supervisor-1' => [
            'maxProcesses' => 3,
            'queues' => ['xp', 'ai', 'notifications', 'default'],
        ],
    ],
],
```

**Named queues by job priority:**
- `ai` — Claude API calls (Smart Project generation, Brain Dump processing, Replanning)
- `xp` — XP award jobs, achievement checks, level-up processing
- `notifications` — Reminders, push notifications, email
- `default` — Stats cache updates, calendar sync

**Dispatch jobs to specific queues:**
```php
AwardXpJob::dispatch($userId, 'task', $taskId)->onQueue('xp');
CallClaudeForProjectJob::dispatch($project)->onQueue('ai');
```

**Start Horizon in development:**
```bash
php artisan horizon
```

**Deploy Horizon with Supervisor** on production (Forge handles this automatically).

---

### Soft Deletes

Several models use soft deletes to preserve history for replanning and stats.

**Models that use soft deletes:**
- `ScheduleBlock` — old blocks are soft-deleted during replanning (not hard deleted)
- `Project` — archived projects are soft deleted, not removed

```php
use Illuminate\Database\Eloquent\SoftDeletes;

class ScheduleBlock extends Model
{
    use SoftDeletes;
}
```

**Migration:**
```php
$table->softDeletes(); // adds nullable deleted_at timestamp
```

**Replan flow** — when a replan is approved, soft-delete old incomplete blocks:
```php
// Soft delete old incomplete blocks
$project->scheduleBlocks()
    ->whereNull('completed_at')
    ->whereNull('deleted_at')
    ->delete(); // triggers soft delete via SoftDeletes trait

// Reassign tasks to new blocks
foreach ($newBlockAssignments as $taskId => $blockId) {
    Task::find($taskId)->update(['schedule_block_id' => $blockId]);
}
```

**Route model binding with soft deletes** — if you need to access soft-deleted blocks (e.g., replan history view):
```php
Route::get('/blocks/{block}', ...)->withTrashed();
```

---

### Task Scheduling (`routes/console.php`)

All scheduled tasks are defined in `routes/console.php` (Laravel 12 convention — not `Kernel.php`):

```php
use Illuminate\Support\Facades\Schedule;

// Prune expired Sanctum tokens daily
Schedule::command('sanctum:prune-expired --hours=24')->daily();

// Update user_stats cache every hour (async, via queue)
Schedule::job(new UpdateUserStatsJob)->hourly()->onQueue('default');

// Check for auto-replan triggers (inactive projects behind schedule)
Schedule::job(new CheckAutoReplanJob)->twiceDaily()->onQueue('default');

// Send scheduled reminders (check every 5 minutes)
Schedule::job(new ProcessRemindersJob)->everyFiveMinutes()->onQueue('notifications');

// Prune old replan_history records (keep 90 days)
Schedule::command('model:prune --model=ReplanHistory')->weekly();
```

**Single cron entry on server:**
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

---

### Resource Controllers

Use `--resource` flag and `--api` flag for API-only controllers (no `create`/`edit` views):

```bash
# Generate all resource controllers
php artisan make:controller ProjectController --resource --api --model=Project
php artisan make:controller ScheduleBlockController --resource --api --model=ScheduleBlock
php artisan make:controller TaskController --resource --api --model=Task
php artisan make:controller HabitController --resource --api --model=Habit
php artisan make:controller BrainDumpController --resource --api --model=BrainDump
```

**Nested resource routes** in `routes/api.php`:
```php
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('projects', ProjectController::class);
    Route::apiResource('projects.blocks', ScheduleBlockController::class)->shallow();
    Route::apiResource('blocks.tasks', TaskController::class)->shallow();
    Route::apiResource('habits', HabitController::class);
    Route::apiResource('brain-dumps', BrainDumpController::class);

    // Custom actions
    Route::post('projects/generate', [ProjectController::class, 'generate']);
    Route::post('projects/{project}/replan', [ProjectController::class, 'replan']);
    Route::patch('tasks/{task}/complete', [TaskController::class, 'complete']);
    Route::patch('tasks/{task}/assign', [TaskController::class, 'assign']);
    Route::patch('blocks/{block}/snooze', [ScheduleBlockController::class, 'snooze']);
    Route::post('habits/{habit}/log', [HabitController::class, 'log']);
    Route::post('brain-dumps/{brainDump}/process', [BrainDumpController::class, 'process']);

    // Gamification
    Route::get('xp/summary', [XpController::class, 'summary']);
    Route::get('achievements', [AchievementController::class, 'index']);
    Route::patch('achievements/{userAchievement}/seen', [AchievementController::class, 'markSeen']);
    Route::get('unlockables', [UnlockableController::class, 'index']);
    Route::post('unlockables/{unlockable}/activate', [UnlockableController::class, 'activate']);

    // Stats
    Route::get('stats/overview', [StatsController::class, 'overview']);
    Route::get('stats/planning-score', [StatsController::class, 'planningScore']);
});
```

---

### Model Pruning

Use Laravel's built-in `Prunable` trait for automatic cleanup of old records:

```php
// app/Models/ReplanHistory.php
use Illuminate\Database\Eloquent\Prunable;

class ReplanHistory extends Model
{
    use Prunable;

    public function prunable(): Builder
    {
        // Auto-prune records older than 90 days
        return static::where('created_at', '<=', now()->subDays(90));
    }
}

// app/Models/BrainDump.php
use Illuminate\Database\Eloquent\Prunable;

class BrainDump extends Model
{
    use Prunable;

    public function prunable(): Builder
    {
        // Auto-prune processed dumps older than 30 days
        return static::where('processed', true)
                     ->where('created_at', '<=', now()->subDays(30));
    }
}
```

Schedule in `routes/console.php`:
```php
Schedule::command('model:prune')->daily();
```

---

### Artisan Commands for Session 1

Run all of these at the start of Session 1 to scaffold the full project before writing any feature code:

```bash
# Create the project
laravel new acuo --no-interaction
cd acuo

# Install Sanctum (L12 way)
php artisan install:api

# Install Horizon
composer require laravel/horizon
php artisan horizon:install

# Generate all models with migrations
php artisan make:model Project -m
php artisan make:model ProjectParameter -m
php artisan make:model ScheduleBlock -m
php artisan make:model Task -m
php artisan make:model BrainDump -m
php artisan make:model Habit -m
php artisan make:model HabitLog -m
php artisan make:model Reminder -m
php artisan make:model ReplanHistory -m
php artisan make:model XpEvent -m
php artisan make:model UserLevel -m
php artisan make:model Level -m
php artisan make:model Unlockable -m
php artisan make:model UserUnlockable -m
php artisan make:model Achievement -m
php artisan make:model UserAchievement -m
php artisan make:model UserStat -m

# Generate observers
php artisan make:observer TaskObserver --model=Task
php artisan make:observer HabitLogObserver --model=HabitLog
php artisan make:observer ProjectObserver --model=Project
php artisan make:observer UserAchievementObserver --model=UserAchievement

# Generate jobs
php artisan make:job AwardXpJob
php artisan make:job CheckAchievementsJob
php artisan make:job ProcessLevelUpJob
php artisan make:job CallClaudeForProjectJob
php artisan make:job CallClaudeForBrainDumpJob
php artisan make:job CallClaudeForReplanJob
php artisan make:job UpdateUserStatsJob
php artisan make:job CheckAutoReplanJob
php artisan make:job ProcessRemindersJob

# Generate resource controllers
php artisan make:controller ProjectController --resource --api --model=Project
php artisan make:controller ScheduleBlockController --resource --api --model=ScheduleBlock
php artisan make:controller TaskController --resource --api --model=Task
php artisan make:controller HabitController --resource --api --model=Habit
php artisan make:controller BrainDumpController --resource --api --model=BrainDump
php artisan make:controller HabitLogController --api --model=HabitLog
php artisan make:controller XpController
php artisan make:controller AchievementController
php artisan make:controller UnlockableController
php artisan make:controller StatsController

# Generate seeders
php artisan make:seeder LevelSeeder
php artisan make:seeder AchievementSeeder
php artisan make:seeder UnlockableSeeder
```

---

## Vue 3 + Vite Frontend — Setup & Structure

The frontend lives in a **separate repo** (`acuo-frontend`). It's a Vue 3 SPA served independently from the Laravel API — no Inertia, no Blade, no starter kit.

---

### Initial Scaffold

```bash
# Scaffold with Vite's Vue template
npm create vite@latest acuo-frontend -- --template vue
cd acuo-frontend

# Core dependencies
npm install vue-router@4 pinia axios

# Tailwind CSS v4 (no tailwind.config.js — registered as a Vite plugin)
npm install tailwindcss @tailwindcss/vite

# PWA plugin
npm install -D vite-plugin-pwa

# Inter font via Fontsource (avoids Google Fonts privacy/GDPR issues)
npm install @fontsource-variable/inter
```

> **Note on Tailwind v4:** There is no `tailwind.config.js` in v4. Tailwind is registered as a Vite plugin and imported directly in CSS. See config below.

---

### `vite.config.js`

```js
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import tailwindcss from '@tailwindcss/vite'
import { VitePWA } from 'vite-plugin-pwa'
import { fileURLToPath, URL } from 'node:url'

export default defineConfig({
  plugins: [
    vue(),
    tailwindcss(),
    VitePWA({
      registerType: 'autoUpdate',
      manifest: {
        name: 'acuo',
        short_name: 'acuo',
        description: 'Sharpen your process. Quiet the noise.',
        theme_color: '#1a1a1a',
        background_color: '#ffffff',
        display: 'standalone',
        start_url: '/',
        icons: [
          { src: '/icons/icon-192.png', sizes: '192x192', type: 'image/png' },
          { src: '/icons/icon-512.png', sizes: '512x512', type: 'image/png' },
          { src: '/icons/icon-512-maskable.png', sizes: '512x512',
            type: 'image/png', purpose: 'maskable' }
        ]
      },
      workbox: {
        globPatterns: ['**/*.{js,css,html,ico,png,svg,woff2}'],
        runtimeCaching: [{
          urlPattern: /^https:\/\/yourapi\.com\/api\//,
          handler: 'NetworkFirst',
          options: {
            cacheName: 'api-cache',
            networkTimeoutSeconds: 5,
            expiration: { maxAgeSeconds: 86400 }
          }
        }]
      }
    })
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    }
  }
})
```

---

### CSS — `src/assets/style.css`

```css
@import "tailwindcss";

/* acuo design tokens */
:root {
  --acuo-dark:        #1a1a1a;
  --acuo-mid:         #3d3d3d;
  --acuo-surface:     #2a2a2a;
  --acuo-light:       #f5f5f5;
  --acuo-white:       #ffffff;
  --acuo-dot-grid:    rgba(0,0,0,0.06);
  --acuo-success:     #4caf82;
  --acuo-moving:      #5b8fcf;
  --acuo-neutral:     #9e9e9e;
  --acuo-nudge:       #e8c56d;
  --acuo-xp:          #a78bfa;
  --acuo-text-muted:  #888888;
  --acuo-font:        'Inter Variable', system-ui, sans-serif;
  --acuo-radius:      12px;
  --acuo-radius-pill: 999px;
}
```

---

### `src/main.js`

```js
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router'
import App from './App.vue'
import '@fontsource-variable/inter'
import './assets/style.css'

const app = createApp(App)
app.use(createPinia())
app.use(router)
app.mount('#app')
```

---

### Axios — `src/lib/axios.js`

Sanctum cookie auth requires both `withCredentials` and `withXSRFToken`. Without these, every authenticated request will fail.

```js
import axios from 'axios'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
  withCredentials: true,   // required for Sanctum cookie auth
  withXSRFToken: true,     // required for CSRF protection
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  }
})

export default api
```

```
# .env
VITE_API_URL=http://localhost:8000
```

> **CSRF note:** The Sanctum CSRF cookie fetch (`/sanctum/csrf-cookie`) must happen before your first POST. The auth store handles this — see below. If you ever get 419 errors, this is why.

---

### Router — `src/router/index.js`

All routes lazy-load their view components. Auth guard checks the Pinia auth store before each navigation.

```js
import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes = [
  // Guest routes
  { path: '/login',    component: () => import('@/views/Login.vue'),    meta: { guest: true } },
  { path: '/register', component: () => import('@/views/Register.vue'), meta: { guest: true } },

  // Auth-required routes
  { path: '/',                    component: () => import('@/views/Dashboard.vue'),     meta: { requiresAuth: true } },
  { path: '/onboarding',          component: () => import('@/views/Onboarding.vue'),    meta: { requiresAuth: true } },
  { path: '/projects',            component: () => import('@/views/Projects.vue'),      meta: { requiresAuth: true } },
  { path: '/projects/new',        component: () => import('@/views/NewProject.vue'),    meta: { requiresAuth: true } },
  { path: '/projects/new/smart',  component: () => import('@/views/SmartWizard.vue'),   meta: { requiresAuth: true } },
  { path: '/projects/:id',        component: () => import('@/views/ProjectDetail.vue'), meta: { requiresAuth: true } },
  { path: '/brain-dump',          component: () => import('@/views/BrainDump.vue'),     meta: { requiresAuth: true } },
  { path: '/habits',              component: () => import('@/views/Habits.vue'),         meta: { requiresAuth: true } },
  { path: '/stats',               component: () => import('@/views/Stats.vue'),          meta: { requiresAuth: true } },
  { path: '/settings',            component: () => import('@/views/Settings.vue'),       meta: { requiresAuth: true } },
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach(async (to) => {
  const auth = useAuthStore()
  if (to.meta.requiresAuth && !auth.user) return '/login'
  if (to.meta.guest && auth.user) return '/'
})

export default router
```

---

### Pinia Stores — `src/stores/`

#### `auth.js`

```js
import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/lib/axios'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)

  async function fetchUser() {
    try {
      const { data } = await api.get('/api/user')
      user.value = data
    } catch {
      user.value = null
    }
  }

  async function login(credentials) {
    // Must fetch CSRF cookie before first POST
    await api.get('/sanctum/csrf-cookie')
    await api.post('/login', credentials)
    await fetchUser()
  }

  async function logout() {
    await api.post('/logout')
    user.value = null
  }

  return { user, fetchUser, login, logout }
})
```

#### Other stores to create (scaffold in Session 2):

```
src/stores/
  auth.js       — user, login, logout, fetchUser
  projects.js   — project list, active project, CRUD actions
  tasks.js      — tasks by block, complete, assign
  braindump.js  — capture state, processing state, suggestions
  xp.js         — total XP, current level, unseen achievements queue
  ui.js         — theme, active unlockables, modal state (achievement/levelup)
```

The `ui.js` store is particularly important — it manages which unlockable theme is active, whether the `AchievementModal` or `LevelUpModal` is currently showing, and the queue of unseen achievements. Boot this store on app init so unlockables are applied before first render.

---

### Folder Structure

```
acuo-frontend/
├── public/
│   └── icons/                  — PWA icons (192, 512, 512-maskable)
├── src/
│   ├── assets/
│   │   └── style.css            — Tailwind import + design tokens
│   ├── components/
│   │   ├── ui/                 — NudgeCard, StreakBadge, ProgressRing, XPBar
│   │   ├── projects/           — ProjectCard, ScheduleBlock, TaskRow, TaskChecklist
│   │   ├── wizard/             — WizardShell, StepCategory, StepParameters,
│   │   │                           StepTimeline, StepReview, StepConfirm
│   │   ├── braindump/          — BrainDumpCapture, BrainDumpReview
│   │   └── gamification/       — AchievementModal, LevelUpModal,
│   │                               AchievementBadge, UnlockableCard
│   ├── views/                  — one file per route
│   ├── stores/                 — auth.js, projects.js, tasks.js,
│   │                               braindump.js, xp.js, ui.js
│   ├── router/
│   │   └── index.js
│   ├── lib/
│   │   └── axios.js            — configured Axios instance
│   └── main.js
├── .env                        — VITE_API_URL
├── vite.config.js
└── package.json
```

---

### Laravel CORS — Required for Two-Repo Setup

Since the frontend and backend are on separate origins in development, the Laravel API needs CORS correctly configured or every Sanctum request will fail silently.

**`.env` (Laravel backend):**
```
SANCTUM_STATEFUL_DOMAINS=localhost:5173
SESSION_DOMAIN=localhost
FRONTEND_URL=http://localhost:5173
```

**`config/cors.php`:**
```php
'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout'],
'allowed_origins' => [env('FRONTEND_URL', 'http://localhost:5173')],
'allowed_methods' => ['*'],
'allowed_headers' => ['*'],
'supports_credentials' => true,  // critical — must be true for cookie auth
```

> **`supports_credentials: true` is non-negotiable** for Sanctum SPA auth across separate origins. Without it, the browser will reject the `Set-Cookie` header on the CSRF response and every subsequent authenticated request will 401.

---

## Data Model

### Concept Overview

```
Project
  └── Schedule Blocks  (when you plan to work — time-boxed sessions)
        └── Tasks       (what you do — discrete checklist items within a block)

Habits                  (Rituals | Recurring Tasks)
Brain Dumps             (raw input → processed into projects/tasks by AI)
Gamification            (XP ledger → levels → unlockables + achievements)
```

**Why separate Tasks from Schedule Blocks:**
Replanning only moves tasks to new blocks (`schedule_block_id` update) — `completed_at` is never touched. Claude replans by seeing what's done and reassigning remaining tasks to new blocks.

---

### Full Schema

```sql
-- Users (extend default Laravel users table)
users
  id (uuid, UUIDv7 via HasUuids), name, email, password,
  timezone, theme (light/dark), onboarding_completed (bool),
  remember_token, created_at, updated_at

-- Projects
projects
  id (uuid), user_id (uuid FK), title, category, subcategory,
  description, deadline (date), status (active/completed/archived),
  ai_generated (bool), last_replanned_at (timestamp nullable),
  deleted_at (nullable — soft delete for archiving),
  created_at, updated_at

-- Project Parameters
project_parameters
  id (uuid), project_id (uuid FK), key (varchar), value (text)

-- Schedule Blocks
schedule_blocks
  id (uuid), project_id (uuid FK), title, description (text nullable),
  scheduled_date (date), order (int), estimated_minutes (int),
  actual_minutes (int nullable), completed_at (timestamp nullable),
  snoozed_to (date nullable),
  deleted_at (nullable — soft delete; used during replanning)
  created_at, updated_at

-- Tasks
tasks
  id (uuid), project_id (uuid FK),
  schedule_block_id (uuid FK nullable — null = unassigned),
  title, description (text nullable), order (int),
  estimated_minutes (int nullable), actual_minutes (int nullable),
  completed_at (timestamp nullable),
  created_at, updated_at

-- Brain Dumps
brain_dumps
  id (uuid), user_id (uuid FK), content (text),
  processed (bool default false),
  linked_project_id (uuid FK nullable),
  created_at
  -- auto-pruned after 30 days if processed (Prunable trait)

-- Habits
habits
  id (uuid), user_id (uuid FK), title,
  type (enum: ritual, recurring_task),
  frequency (enum: daily, weekly),
  target_days (json — int array e.g. [1,3,5] for Mon/Wed/Fri),
  linked_project_id (uuid FK nullable),  -- recurring_task only
  current_streak (int default 0),
  longest_streak (int default 0),
  created_at, updated_at

-- Habit Logs
habit_logs
  id (uuid), habit_id (uuid FK), user_id (uuid FK),
  logged_at (timestamp), note (varchar nullable)

-- Reminders
reminders
  id (uuid), user_id (uuid FK),
  project_id (uuid FK nullable),
  habit_id (uuid FK nullable),
  schedule_block_id (uuid FK nullable),
  task_id (uuid FK nullable),
  message (varchar), remind_at (timestamp),
  sent_at (timestamp nullable),
  channel (enum: push, email),
  created_at

-- Replan History
replan_history
  id (uuid), project_id (uuid FK),
  triggered_by (enum: manual, auto, threshold),
  original_blocks (json), new_blocks (json),
  tasks_moved (int), tasks_completed_at_time (int),
  created_at
  -- auto-pruned after 90 days (Prunable trait)

-- XP Events (append-only ledger)
xp_events
  id (uuid), user_id (uuid FK),
  source_type (enum: task, habit, project, achievement, accuracy_bonus),
  source_id (uuid), xp_awarded (int), reason (varchar),
  created_at

-- User Level State (denormalized cache)
user_levels
  id (uuid), user_id (uuid FK unique),
  total_xp (int default 0), current_level (int default 1),
  current_level_xp (int default 0), xp_to_next_level (int),
  updated_at

-- Levels Reference (seeded via LevelSeeder)
levels
  id (int — the level number, PK), title (varchar),
  xp_required (int — cumulative), unlock_key (varchar nullable)

-- Unlockables Reference (seeded via UnlockableSeeder)
unlockables
  key (varchar PK), name, description,
  category (enum: theme, feature, cosmetic),
  unlock_condition (varchar)

-- User Unlockables
user_unlockables
  id (uuid), user_id (uuid FK), unlockable_key (varchar FK),
  earned_at (timestamp), activated_at (timestamp nullable)

-- Achievements Reference (seeded via AchievementSeeder)
achievements
  id (uuid), key (varchar unique), title, description,
  icon (varchar), category (enum: progress, streak, planning, milestone),
  xp_reward (int), created_at

-- User Achievements
user_achievements
  id (uuid), user_id (uuid FK), achievement_id (uuid FK),
  earned_at (timestamp), seen_at (timestamp nullable)

-- User Stats Cache
user_stats
  id (uuid), user_id (uuid FK unique),
  total_projects_completed (int default 0),
  total_tasks_completed (int default 0),
  total_habits_logged (int default 0),
  current_task_streak (int default 0),
  avg_estimate_accuracy (decimal nullable),
  planning_score (int default 0),
  updated_at

-- Calendar Sync (V2)
calendar_connections
  id (uuid), user_id (uuid FK), provider (enum: google),
  access_token (text encrypted), refresh_token (text encrypted),
  token_expires_at (timestamp), sync_enabled (bool default true),
  last_synced_at (timestamp nullable), created_at

calendar_event_map
  id (uuid), user_id (uuid FK), schedule_block_id (uuid FK),
  external_event_id (varchar), provider (enum: google),
  last_synced_at (timestamp)
```

---

## Feature Specifications

---

### Feature: Brain Dump Mode

- Persistent *"What's on your mind?"* button always on dashboard
- Full-screen distraction-free textarea
- On submit → dispatches `CallClaudeForBrainDumpJob` to `ai` queue
- Job calls Claude API, parses response into projects/tasks/non-actionable
- User reviews in reveal UI, one-taps to accept
- Completing a brain dump → `AwardXpJob` dispatched to `xp` queue (20 XP)
- Processed dumps auto-pruned after 30 days via `Prunable`

**Claude prompt:**
```
System:
You are helping someone externalize and organize their thoughts.
Parse the brain dump and identify:
1. Projects — things with scope or deadline
2. Tasks — discrete single actions
3. Non-actionable — worries or venting to acknowledge, not schedule

If something is vague, include a clarifying question. Be generous.
Return only valid JSON — no preamble, no markdown fences.

{
  "projects": [{ "title": "...", "description": "...", "urgency": "high|medium|low" }],
  "tasks": [{ "title": "...", "suggested_project": "..." }],
  "non_actionable": ["..."],
  "clarifying_questions": ["..."]
}
```

---

### Feature: Smart Project Wizard

**Step flow:** Category → Subcategory/Type → Parameters → Timeline → Generation → Review & Approve

**Generation:** Dispatches `CallClaudeForProjectJob` to `ai` queue. Response is a JSON object of blocks with nested task arrays. On approval, project + blocks + tasks are batch-inserted. XP awarded via observer on project creation with `ai_generated = true`.

**Claude prompt:**
```
System:
You are a planning assistant for someone who struggles with executive function.
Generate schedule blocks (work sessions, 30–90 min max), each with specific tasks.
Be concrete. "Research topic" is bad. "Find 3 sources on X and save links" is good.
Make the first task a 10–15 min warmup. Output only valid JSON — no markdown fences.

{
  "suggested_title": "...",
  "overview": "...",
  "estimated_total_hours": X,
  "schedule_blocks": [
    {
      "title": "...", "description": "...",
      "suggested_day_offset": 0, "estimated_minutes": 60,
      "tasks": [
        { "title": "...", "description": "...", "estimated_minutes": 20, "warmup": true }
      ]
    }
  ]
}
```

**AI personalization (injected when planning history exists):**
```
User planning profile:
  avg estimate accuracy: 78% (tends to underestimate by ~15%)
  preferred session length: medium (60 min)
  most productive days: Tuesday, Thursday

Adjust estimates upward ~15%. Prefer heavier blocks on Tue/Thu.
```

---

### Feature: Progress System

- **ProgressRing** — SVG ring, fills green as tasks complete (task-based %, not block-based)
- **Color Flip** — completion % drives color signal, not time remaining (see table in previous version)
- **"In the Zone" streak** — 3+ consecutive days with ≥1 task completed; flame icon + count on dashboard; resets quietly

---

### Feature: Adaptive Replanning (AI)

**Replanning with the Tasks model:**
1. Collect all incomplete tasks for the project (not soft-deleted, `completed_at` is null)
2. Dispatch `CallClaudeForReplanJob` to `ai` queue with completion state + remaining tasks
3. Claude returns new block assignments (task IDs → new block date/title)
4. Show diff review UI — user approves or cancels
5. On approval:
   - Soft-delete old incomplete `ScheduleBlock` records (`->delete()` via `SoftDeletes`)
   - Batch-create new `ScheduleBlock` records
   - Update `schedule_block_id` on remaining tasks
   - Log to `replan_history`
6. `completed_at` is **never modified** — history always intact

**Claude prompt:**
```
System:
This project is being replanned. Generate new schedule blocks for
remaining incomplete tasks only. Start from today. If deadline is
tight, say so and prioritize highest-value tasks first.
Output only valid JSON — no markdown fences.

Completed tasks: [list]
Remaining tasks + estimates: [list]
Today: [date] | Deadline: [date]
Available: [X hrs/day] | Triggered by: [manual|auto|threshold]

{
  "overview": "...", "tight_deadline_warning": true/false,
  "schedule_blocks": [
    { "title": "...", "suggested_date": "YYYY-MM-DD",
      "estimated_minutes": 60, "task_ids": [uuid, uuid] }
  ]
}
```

---

### Feature: Habits

**Two types:**

**Ritual** — standalone recurring practice (exercise, journaling)
- `type: ritual`, frequency daily or specific days
- One-tap log from dashboard → `HabitLog` created → `AwardXpJob` dispatched (8 XP)
- No project link

**Recurring Task** — repeating action tied to a project ("Write 200 words")
- `type: recurring_task`, links to `project_id`
- One-tap log → `HabitLog` created → `AwardXpJob` dispatched (12 XP)
- If linked project archived → habit auto-pauses (scoped query excludes it from dashboard)

**Shared:**
- `HabitLogObserver` updates `current_streak` and `longest_streak` on `HabitLog::created`
- Streak language always positive — "5-day streak" on log, "Today's a fresh start" on miss (shown once, dismissed)
- Weekly dot view: 7 `habit_logs` lookups for current week per habit

---

### Feature: Gamification — Full Points, Levels & Unlockables

#### XP System

All XP flows through the **`xp_events` ledger** (append-only). `user_levels` is a denormalized cache updated by `ProcessLevelUpJob` after each `AwardXpJob` completes.

**XP weights:**

| Action | XP | Notes |
|---|---|---|
| Complete a task | 10 | Base |
| Complete task on scheduled date | +5 bonus | Rewards following the plan |
| Estimate accuracy bonus (within 20%) | +5 bonus | Requires `actual_minutes` logged |
| Complete a schedule block (all tasks done) | 25 | Rewards session completion |
| Complete a project | 100 | Major milestone |
| Log a ritual habit | 8 | |
| Log a recurring task habit | 12 | Tied to real project work |
| Process a brain dump | 20 | Rewards the hardest step |
| Use adaptive replanning | 15 | Rewards course-correcting |
| Earn an achievement | 15–100 | Per achievement |
| Planning Score milestone (every 10pts) | 30 | Rewards quality over time |

**Anti-farming rules (enforced in `AwardXpJob`):**
- Tasks with `estimated_minutes < 5` award 2 XP (not 10)
- Max 200 XP per day from task completions — surplus deferred to next day
- Each habit awards XP once per day per habit (deduplicated via `xp_events` query)

#### Level System (seeded via `LevelSeeder`)

| Level | Title | Cumulative XP |
|---|---|---|
| 1 | Dull | 0 |
| 2 | Rough | 150 |
| 3 | Grinding | 400 |
| 4 | Honing | 800 |
| 5 | Edged | 1,400 |
| 6 | Keen | 2,200 |
| 7 | Whetted | 3,200 |
| 8 | Fine | 4,500 |
| 9 | Polished | 6,000 |
| 10 | Sharp | 8,000 |
| 11 | Acute | 10,500 |
| 12 | Precise | 13,500 |
| 13 | Honed | 17,000 |
| 14 | Razor | 21,000 |
| 15 | Acuo | 26,000 |

**Level-up flow:**
1. `AwardXpJob` fires → updates `user_levels.total_xp`
2. Check if `total_xp >= levels[current_level + 1].xp_required`
3. If yes → dispatch `ProcessLevelUpJob` to `xp` queue
4. `ProcessLevelUpJob` increments `current_level`, checks for unlockable, creates `UserUnlockable` if applicable, fires `LevelUpEvent`
5. Frontend polls `GET /api/xp/summary` or listens via Echo — triggers `LevelUpModal`

#### Unlockables (seeded via `UnlockableSeeder`)

**Themes:**
| Key | Unlock | Description |
|---|---|---|
| `theme_midnight` | Level 3 | Deep navy + white |
| `theme_forest` | Level 5 | Muted greens, earthy neutrals |
| `theme_slate` | Level 7 | Cool grey monochrome |
| `theme_ember` | Achievement: 30-day streak | Warm amber tones |
| `theme_void` | Level 15 | Pure black — prestige theme |

**Features (functional):**
| Key | Unlock | Description |
|---|---|---|
| `brain_dump_templates` | Level 4 | Pre-structured Brain Dump prompts |
| `advanced_stats` | Level 6 | Full planning score breakdown |
| `custom_block_colors` | Level 8 | Color-tag blocks by energy/type |
| `priority_replan` | Level 10 | Tell Claude which tasks matter most during replan |
| `ai_session_warmup` | Level 12 | Claude generates a personalized warmup task per session |

**Cosmetic:**
| Key | Unlock | Description |
|---|---|---|
| `custom_streak_icon` | Level 5 | Choose flame/spark/lightning variant |
| `dashboard_layouts` | Level 9 | Compact, focus, or expanded mode |
| `acuo_badge` | Level 15 | Prestige profile badge |

**Activation:** `POST /api/unlockables/{key}/activate` sets `activated_at` on `user_unlockables`. Frontend reads active unlockables on app boot and applies them via Pinia store.

#### Achievements (seeded via `AchievementSeeder`)

Checked in `CheckAchievementsJob`, dispatched to `xp` queue after XP events. Job receives `user_id` and checks all unearned achievements against current `user_stats`.

**Progress:**
| Key | Trigger | XP |
|---|---|---|
| `first_step` | Complete first task | 15 |
| `sharpened` | Complete first project | 50 |
| `on_a_roll` | Complete 3 projects | 75 |
| `builder` | Complete 10 projects | 100 |
| `brain_cleared` | Process first brain dump | 30 |
| `cleared_the_fog` | Process 10 brain dumps | 75 |

**Streaks:**
| Key | Trigger | XP |
|---|---|---|
| `in_the_zone` | 3-day task streak | 20 |
| `consistent` | 7-day streak | 40 |
| `unstoppable` | 30-day streak | 100 |
| `ritual_habit` | 7 habit logs in a row | 30 |
| `committed_habit` | 30 habit logs in a row | 75 |

**Planning:**
| Key | Trigger | XP |
|---|---|---|
| `sharp_eye` | First AI-generated project | 25 |
| `adapted` | First adaptive replan | 25 |
| `resilient` | Complete replanned project | 50 |
| `planner` | Planning Score hits 50 | 50 |
| `precision` | Planning Score hits 80 | 100 |
| `clockwork` | 5 tasks within 10% of estimate | 60 |

**Milestones:**
| Key | Trigger | XP |
|---|---|---|
| `hundred_tasks` | 100 tasks completed | 75 |
| `five_hundred_tasks` | 500 tasks completed | 150 |
| `synced` | Google Calendar connected (V2) | 30 |

**Reveal flow:**
1. `UserAchievement` created → `seen_at = null`
2. Frontend: `GET /api/achievements/unseen` on each page navigation
3. `AchievementModal` shows one at a time (queued if multiple)
4. If level-up also pending: achievement first, then `LevelUpModal`
5. `PATCH /api/achievements/{id}/seen` marks as seen
6. Locked achievements in `/stats` shown as grey silhouettes with unlock condition

---

### Feature: Google Calendar Integration (V2)

**OAuth flow:** Settings → Integrations → Connect Google Calendar → OAuth → select/create calendar

**Sync behavior:**
- Block created → Google event created, `calendar_event_map` record saved
- Block rescheduled → Google event updated
- Block completed → event title prefixed with ✓
- Block deleted → Google event deleted
- Google event moved → acuo block `scheduled_date` updated on next sync
- Google event deleted → block marked snoozed (user confirms)

**What does NOT sync:** Tasks, Habits, completed projects.

**Queue:** `CalendarSyncJob` dispatched to `default` queue every 15 minutes via scheduler, plus triggered on block mutations.

---

### Feature: AI Feedback Loop & Planning Score

**Planning Score (0–100):**
```
40% — Estimate accuracy  (actual vs estimated minutes, when logged)
35% — On-time completion (tasks completed on scheduled date)
25% — Replan frequency   (replanning rewarded vs. abandonment)
```

Updated async via `UpdateUserStatsJob` (hourly scheduler). Surfaced on `/stats` as trend line + insight callouts.

**Insights examples:**
- *"You tend to underestimate tasks by ~20% — we've adjusted AI suggestions"*
- *"Your most productive days are Tuesday and Thursday"*
- *"Projects with tasks under 30 min complete 2x more often"*

---

## Frontend — Pages & Components

### Pages

| Route | Page | V |
|---|---|---|
| `/` | Dashboard — today's tasks, projects, Brain Dump, habits, streak, XP bar | 1 |
| `/login` | Login | 1 |
| `/register` | Register | 1 |
| `/onboarding` | First-time walkthrough | 1 |
| `/projects` | All projects | 1 |
| `/projects/new` | New project | 1 |
| `/projects/new/smart` | Smart Wizard | 1 |
| `/projects/:id` | Project detail — blocks, tasks, progress, replan | 1 |
| `/brain-dump` | Full-screen capture + AI review | 1 |
| `/habits` | Habit list, weekly dot view | 2 |
| `/stats` | Planning Score, Achievements tab, Unlockables tab | 2 |
| `/settings` | Theme, timezone, notifications, integrations | 1/2 |

### Key Components

```
AppShell.vue                  — nav, layout, Brain Dump button, XP bar
XPBar.vue                     — compact level + XP progress in nav
DashboardToday.vue            — tasks + habits unified, today's focus
ProjectCard.vue               — ProgressRing, title, deadline nudge
ProgressRing.vue              — SVG, task-based %, color-flip
ScheduleBlock.vue             — block: date, estimate, expand/collapse
TaskRow.vue                   — checkbox, title, estimate, inline edit
TaskChecklist.vue             — ordered TaskRows inside a block
SmartWizard/
  WizardShell.vue, StepCategory.vue, StepParameters.vue,
  StepTimeline.vue, StepReview.vue, StepConfirm.vue
BrainDumpCapture.vue          — fullscreen textarea
BrainDumpReview.vue           — AI-parsed suggestions, one-tap accept
ReplanReview.vue              — diff: grey old blocks, white new blocks
HabitRow.vue                  — icon, title, streak count, log button
HabitWeekView.vue             — 7-dot weekly completion
AchievementModal.vue          — full-screen reveal, queued
LevelUpModal.vue              — level-up overlay, unlockable reveal
AchievementBadge.vue          — collected badge display
UnlockableCard.vue            — locked/unlocked/activated state
PlanningScore.vue             — score + trend + insight callouts
NudgeCard.vue                 — gentle contextual prompts (never red)
StreakBadge.vue               — flame/spark/lightning icon + count
ThemeToggle.vue
OnboardingFlow.vue
```

---

## V1 Scope

| Feature | |
|---|---|
| Auth (register, login, logout) | ✅ |
| Dashboard — tasks, projects, Brain Dump, XP bar, streak | ✅ |
| Brain Dump (capture + AI + review) | ✅ Core |
| Smart Project Wizard (blocks + tasks) | ✅ Core |
| Manual project + block + task creation | ✅ |
| Project detail — blocks, task checklists | ✅ |
| Progress System — ProgressRing, Color Flip | ✅ Core |
| In the Zone streak | ✅ |
| Task snooze / block reschedule | ✅ |
| Full gamification — XP, levels, achievements, unlockable themes | ✅ |
| AchievementModal + LevelUpModal | ✅ |
| Light / dark theme + unlockable themes | ✅ |
| Responsive PWA — installable, offline shell | ✅ |
| Onboarding | ✅ |

**Out of V1:** Habits, Adaptive Replanning, Stats/Planning Score, Google Calendar, functional unlockables (earned in V1, activated in V2 when features exist)

---

## V2 Scope

- Adaptive Replanning (manual + auto, diff UI, task reassignment)
- Habits Module (Ritual + Recurring Task, one-tap logging, weekly dots)
- Stats & Planning Score (score calculation, insights, achievement collection, unlockables page)
- Google Calendar Sync (OAuth, two-way, queue-based)
- Functional unlockables activated (Brain Dump Templates, Advanced Stats, etc.)
- Drag-and-drop task/block reordering
- Keyboard shortcuts
- Actual-time logging on task completion (optional)
- AI suggestions personalized by planning history

## V3 Horizon

- Accountability partner / shared projects
- Recurring project templates
- Mobile wrapper (Capacitor)
- Apple Calendar sync

---

## PWA Configuration

```js
VitePWA({
  registerType: 'autoUpdate',
  manifest: {
    name: 'acuo',
    short_name: 'acuo',
    description: 'Sharpen your process. Quiet the noise.',
    theme_color: '#1a1a1a',
    background_color: '#ffffff',
    display: 'standalone',
    start_url: '/',
    icons: [
      { src: '/icons/icon-192.png', sizes: '192x192', type: 'image/png' },
      { src: '/icons/icon-512.png', sizes: '512x512', type: 'image/png' },
      { src: '/icons/icon-512-maskable.png', sizes: '512x512',
        type: 'image/png', purpose: 'maskable' }
    ]
  },
  workbox: {
    globPatterns: ['**/*.{js,css,html,ico,png,svg,woff2}'],
    runtimeCaching: [{
      urlPattern: /^https:\/\/yourapi\.com\/api\//,
      handler: 'NetworkFirst',
      options: {
        cacheName: 'api-cache',
        networkTimeoutSeconds: 5,
        expiration: { maxAgeSeconds: 86400 }
      }
    }]
  }
})
```

---

## Design Tokens

```css
--acuo-dark:         #1a1a1a;
--acuo-mid:          #3d3d3d;
--acuo-surface:      #2a2a2a;
--acuo-light:        #f5f5f5;
--acuo-white:        #ffffff;
--acuo-dot-grid:     rgba(0,0,0,0.06);

--acuo-success:      #4caf82;
--acuo-moving:       #5b8fcf;
--acuo-neutral:      #9e9e9e;
--acuo-nudge:        #e8c56d;   /* no red anywhere */
--acuo-xp:           #a78bfa;   /* XP bar accent */
--acuo-text-muted:   #888888;

--acuo-font:         'Inter', system-ui, sans-serif;
--acuo-radius:       12px;
--acuo-radius-pill:  999px;
/* Spacing: 4 / 8 / 12 / 16 / 24 / 32 / 48 / 64 */
```

---

## Claude Code — Build Strategy

### Session Order

```
Session 1:  laravel new + php artisan install:api + Horizon install
            ALL migrations (full schema — do this once, UUIDv7 everywhere)
            ALL model generation + SoftDeletes traits applied
            LevelSeeder, AchievementSeeder, UnlockableSeeder written + run
            ALL observers generated + registered via #[ObservedBy] attribute
            ALL jobs generated
            ALL resource controllers generated
            routes/api.php fully scaffolded (routes return 501 for now)

Session 2:  Vue 3 + Vite + Tailwind + Pinia + auth pages + AppShell + XPBar

Session 3:  Project CRUD + Schedule Block CRUD (API + UI)

Session 4:  Task CRUD — API + TaskChecklist + TaskRow components

Session 5:  Smart Project Wizard — Claude API, blocks + tasks, StepReview

Session 6:  Brain Dump — capture + Claude processing + BrainDumpReview UI

Session 7:  Progress System — ProgressRing (task-based %), Color Flip, streak

Session 8:  XP system — AwardXpJob, user_levels cache, ProcessLevelUpJob,
            XPBar component

Session 9:  Achievements — CheckAchievementsJob + AchievementModal queue +
            LevelUpModal

Session 10: Unlockables — UnlockableCard, activate endpoint, Pinia theme store

Session 11: Dashboard — unified today view (tasks, Brain Dump button,
            XP bar, streak, habit placeholders)

Session 12: Onboarding flow

Session 13: PWA config + offline shell + installability

Session 14: Polish, edge cases, deploy
            (php artisan optimize on deploy)
```

### Context Tips
- **Session 1 is the most important session.** Migrate everything. Change schema later = pain.
- `PROGRESS.md` in repo root — update every session end. Start every session with it.
- Feed Claude Code: `acuo-spec.md` + `PROGRESS.md` + only the files in scope
- One feature per session
- The `#[ObservedBy]` attribute (L12) keeps observer registration clean — use it on every model
- Horizon manages Redis only — never add a `database` queue driver alongside it

### PROGRESS.md Template
```markdown
# acuo — Build Progress

## Completed Sessions
- [ ] Session 1: Full scaffold

## Current Session Goal
...

## Decisions That Differ From Spec
...

## Known Issues / TODOs
...

## Schema Changes Since Spec
...

## Deployed URL
...
```

---

## Resume Bullet Points (post-ship)

```
Designed and built acuo, a full-stack AI-powered productivity PWA targeting
users with ADHD and executive function challenges — built on Laravel 12,
Vue 3, and PostgreSQL with Claude API integration for intelligent project
planning, adaptive replanning, and a full gamification system.

Architected a three-layer data model (Projects → Schedule Blocks → Tasks)
enabling AI-driven adaptive replanning via Eloquent soft deletes and task
reassignment — completed task history preserved while Claude generates a
clean revised schedule from today forward.

Engineered a Smart Project Wizard and Brain Dump feature using structured
LLM prompting dispatched via Laravel Horizon Redis queues, converting raw
user input into time-blocked work sessions with concrete task checklists —
reducing planning overhead from hours to under two minutes.

Built a full XP/leveling/unlockables gamification system using Eloquent
Observers, an append-only XP ledger, and async Horizon job chains — with
anti-farming weight logic, 15 levels following the acuo sharpening metaphor,
and functional unlockables that reward planning quality over volume.
```

---

*spec version 8.0 — March 2026 — references Laravel 12.x docs*
