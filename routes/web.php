<?php

use App\Http\Livewire\Dashboard\DashboardPage;
use App\Http\Livewire\TradeJournal\TradeJournalPage;
use App\Http\Livewire\TradeJournal\TradeDetail;
use App\Http\Livewire\Portfolio\PortfolioPage;
use App\Http\Livewire\Analytics\AnalyticsPage;
use App\Http\Livewire\Calendar\CalendarPage;
use App\Http\Livewire\Settings\SettingsPage;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('test'))->name('test');
Route::get('/dashboard', DashboardPage::class)->name('dashboard');
Route::get('/trades', TradeJournalPage::class)->name('trades.index');
Route::get('/trades/{trade}', TradeDetail::class)->name('trades.show');
Route::get('/portfolio', PortfolioPage::class)->name('portfolio');
Route::get('/analytics', AnalyticsPage::class)->name('analytics');
Route::get('/calendar', CalendarPage::class)->name('calendar');
Route::get('/settings', SettingsPage::class)->name('settings');
