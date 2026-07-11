# Financia Trader — AI Build Plan

> Referensi untuk sesi AI berikutnya. Berisi arsitektur, file penting, dan state terakhir project.

## Tech Stack
- **Laravel 12** + **Livewire 4** + **Tailwind CSS v4** + **Alpine.js**
- **ApexCharts** (via CDN) untuk grafik
- **MySQL** port 3308, database `financia_trader`, user `root`, no password
- **Vite** sebagai bundler

## Auth
- Single-user (tidak ada register)
- Login: `trader@financia.local` / `password`
- Rute publik (tidak ada auth middleware)

## Desain System
- Base dark: `#0a0a0a` (bukan pure black), base light: `#fafafa`
- Glass effect: `backdrop-blur-xl`, `bg-white/10` dark, `bg-black/5` light
- Warna HANYA: hitam-putih-zinc + emerald (profit) + red (loss)
- Tailwind v4: `@custom-variant dark (&:where(.dark, .dark *))`
- Tema persist via `localStorage('theme')` + inline script di `<head>` (anti flash)

## File Penting

### Routes
```
routes/web.php
├── GET /              → DashboardPage
├── GET /trades        → TradeJournalPage
├── GET /trades/{id}   → TradeDetail
├── GET /portfolio     → PortfolioPage (coming soon)
├── GET /analytics     → AnalyticsPage
├── GET /calendar      → CalendarPage (coming soon)
├── GET /settings      → SettingsPage
└── GET /test          → test page (design system)
```

### Models & Relationships
```
User
├── hasMany → Portfolio, Strategy
└── hasManyThrough → Trade (via Portfolio)

Portfolio
├── belongsTo → User
├── hasMany → Trade, BalanceHistory
└── computed: winRate, totalPnl

Trade
├── belongsTo → Portfolio, Strategy (strategyRef)
├── casts: TradeType, TradeDirection, TradeOutcome
└── scopes: closed(), open(), wins(), losses()

Strategy → belongsTo User, hasMany Trade
BalanceHistory → belongsTo Portfolio
Setting → key/value store (static get/set helpers)
CalendarEvent
```

### Livewire Components
```
app/Http/Livewire/
├── Dashboard/DashboardPage.php        — stats, equity curve, recent trades
├── TradeJournal/TradeJournalPage.php  — CRUD, filters, modals, screenshots
├── TradeJournal/TradeDetail.php       — single trade view
├── Analytics/AnalyticsPage.php        — stat boxes + donut chart
├── Portfolio/PortfolioPage.php        — stub
├── Calendar/CalendarPage.php          — stub
└── Settings/SettingsPage.php          — profile, currency, balance, reset data
```

### Blade Components
```
resources/views/components/
├── glass-card.blade.php       — reusable card glass (hover:shadow + translateY)
├── skeleton-card.blade.php    — loading skeleton shimmer
├── stat-box.blade.php         — stat display
├── icon.blade.php             — 30+ inline SVG icons
├── sidebar.blade.php          — desktop (fixed) + mobile (drawer)
├── _sidebar-nav.blade.php     — nav items partial
├── topbar.blade.php           — sticky header
├── bottom-nav.blade.php       — mobile 5-tab nav
└── theme-toggle.blade.php     — dark/light toggle
```

### CSS Design System (`resources/css/app.css`)
```
@theme tokens:
├── Surface palette (surface, raised, overlay)
├── Text (primary, secondary, muted)
├── Borders
├── Profit (emerald) / Loss (red) / Neutral
└── Glass (background, border, strong)

Components:
├── .glass, .glass-strong, .glass-sidebar, .glass-card
├── .stat-profit, .stat-loss, .stat-neutral (+ bg variants)
├── .btn-primary, .btn-secondary, .btn-ghost, .btn-danger, .btn-profit
├── .input-field, .label-text
├── .page-title, .page-subtitle
├── .skeleton (shimmer animation)
└── .scrollbar-thin, .safe-bottom
```

## Settings Page Features
- **Profile**: Edit nama → simpan ke `users.name`
- **Currency**: Dropdown 8 mata uang → simpan ke `settings` table + active portfolio
- **Initial Balance**: Edit angka → reset portfolio `initial_balance` + `current_balance` + hapus balance history
- **Reset All Data**: Double confirmation modal (Step 1: warning, Step 2: ketik "DELETE") → wipe trades/portfolio/strategies/events/settings, buat fresh portfolio $10k

## Livewire Loading
- Progress bar di atas halaman (hook `request.start/commit/finish`)
- `wire:loading.class` untuk dim content saat fetch
- Skeleton component tersedia (`<x-skeleton-card>`)
- Loading spinner di semua tombol save (settings)

## Micro-Interactions
- Glass cards: `hover:shadow-md` + `translateY(-1px)` + `transition-all duration-200`
- Dark glass: subtle rgba shift on hover
- Bottom nav: `active:scale-95`
- Buttons: `active:scale-[0.98]`

## Database (11 migrations)
1. users, cache, jobs (Laravel default)
2. `trades` — pair, trade_type, direction, entry/exit price, dates, size, SL, TP, pnl, outcome, fees, strategy, notes, screenshot_path
3. `balance_history` — date, balance, equity, deposit, withdrawal, notes
4. `calendar_events` — title, event_date, event_type, impact, pair, notes
5. `settings` — key (unique varchar 50), value (nullable text)
6. `portfolios` — user_id, name, initial/current_balance decimal(16,2), currency, is_active
7. `strategies` — user_id, name, description, unique(user_id, name)
8. add portfolio_id + strategy_id FK to trades
9. add portfolio_id FK to balance_history

## Seeders
- UserSeeder: `trader@financia.local` / `password`
- StrategySeeder: 5 strategies
- PortfolioSeeder: 2 portfolios (Main + Crypto)
- TradeSeeder: 23 trades (mixed outcomes)
- BalanceHistorySeeder: 26 records

## Known Stubs (Belum Dikerjakan)
- **Portfolio Page**: "Coming Soon" placeholder
- **Calendar Page**: "Coming Soon" placeholder
- **Auth middleware**: Tidak ada — semua rute publik

## Build Output
```
public/build/
├── assets/app-*.css   ~86 KB (gzip ~14.6 KB)
└── assets/app-*.js    ~47 KB (gzip ~17.9 KB)
```

## Tips Sesi Baru
1. Selalu cek `routes/web.php` untuk navigasi
2. Settings page sudah fully functional — bisa langsung test
3. Theme persist otomatis — tidak perlu setup ulang
4. Gunakan `<x-glass-card>` untuk card konsisten
5. Gunakan `<x-skeleton-card lines="3">` untuk loading state
6. Warna: hanya zinc + emerald (profit) + red (loss) — jangan tambah warna lain
7. Semua chart pakai `getThemeColors()` + `MutationObserver` untuk auto theme switch
