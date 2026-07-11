<?php

namespace App\Http\Livewire\TradeJournal;

use App\Enums\TradeDirection;
use App\Enums\TradeOutcome;
use App\Enums\TradeType;
use App\Models\BalanceHistory;
use App\Models\Portfolio;
use App\Models\Strategy;
use App\Models\Trade;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TradeJournalPage extends Component
{
    use WithPagination, WithFileUploads;

    // ── Filters ──
    public string $filterPair = '';
    public string $filterStatus = '';
    public string $filterStrategy = '';
    public string $filterDateFrom = '';
    public string $filterDateTo = '';
    public int $perPage = 15;

    // ── Modal state ──
    public bool $showModal = false;
    public bool $isEditing = false;
    public ?int $editingId = null;

    // ── Close trade modal ──
    public bool $showCloseModal = false;
    public ?int $closingTradeId = null;
    public string $closeExitPrice = '';
    public string $closePnlAmount = '';
    public string $closePnlPips = '';
    public string $closeOutcome = '';
    public string $closeNotes = '';

    // ── Delete confirmation ──
    public bool $showDeleteModal = false;
    public ?int $deleteTradeId = null;

    // ── Form fields ──
    public string $pair = '';
    public string $tradeType = 'forex';
    public string $direction = 'long';
    public string $entryPrice = '';
    public string $exitPrice = '';
    public string $entryDate = '';
    public string $exitDate = '';
    public string $size = '';
    public string $stopLoss = '';
    public string $takeProfit = '';
    public string $pnlAmount = '0';
    public string $pnlPips = '';
    public string $outcome = 'open';
    public string $fees = '0';
    public ?int $strategyId = null;
    public string $notes = '';
    public $screenshot;

    // ── Computed lookups ──
    public array $availablePairs = [];
    public array $availableStrategies = [];

    public function mount(): void
    {
        $this->refreshLookups();
    }

    public function refreshLookups(): void
    {
        $this->availablePairs = Trade::distinct()->pluck('pair')->filter()->sort()->values()->toArray();
        $this->availableStrategies = Strategy::orderBy('name')->get(['id', 'name'])->toArray();
    }

    // ──────────────────────────────────────
    // Queries
    // ──────────────────────────────────────

    public function getTradesProperty()
    {
        $query = Trade::with(['portfolio', 'strategyRef'])
            ->latest('entry_date');

        if ($this->filterPair !== '') {
            $query->where('pair', $this->filterPair);
        }

        if ($this->filterStatus === 'open') {
            $query->open();
        } elseif ($this->filterStatus === 'closed') {
            $query->closed();
        }

        if ($this->filterStrategy !== '') {
            $query->where('strategy_id', $this->filterStrategy);
        }

        if ($this->filterDateFrom !== '') {
            $query->whereDate('entry_date', '>=', $this->filterDateFrom);
        }

        if ($this->filterDateTo !== '') {
            $query->whereDate('entry_date', '<=', $this->filterDateTo);
        }

        return $query->paginate($this->perPage);
    }

    public function applyFilters(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->filterPair = '';
        $this->filterStatus = '';
        $this->filterStrategy = '';
        $this->filterDateFrom = '';
        $this->filterDateTo = '';
        $this->resetPage();
    }

    // ──────────────────────────────────────
    // Create / Edit Modal
    // ──────────────────────────────────────

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->editingId = null;
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $trade = Trade::findOrFail($id);

        $this->isEditing = true;
        $this->editingId = $trade->id;

        $this->pair = $trade->pair;
        $this->tradeType = $trade->trade_type->value;
        $this->direction = $trade->direction->value;
        $this->entryPrice = (string) $trade->entry_price;
        $this->exitPrice = $trade->exit_price !== null ? (string) $trade->exit_price : '';
        $this->entryDate = $trade->entry_date ? $trade->entry_date->format('Y-m-d\TH:i') : '';
        $this->exitDate = $trade->exit_date ? $trade->exit_date->format('Y-m-d\TH:i') : '';
        $this->size = (string) $trade->size;
        $this->stopLoss = $trade->stop_loss !== null ? (string) $trade->stop_loss : '';
        $this->takeProfit = $trade->take_profit !== null ? (string) $trade->take_profit : '';
        $this->pnlAmount = (string) $trade->pnl_amount;
        $this->pnlPips = $trade->pnl_pips !== null ? (string) $trade->pnl_pips : '';
        $this->outcome = $trade->outcome->value;
        $this->fees = (string) $trade->fees;
        $this->strategyId = $trade->strategy_id;
        $this->notes = $trade->notes ?? '';
        $this->screenshot = null;

        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->pair = '';
        $this->tradeType = 'forex';
        $this->direction = 'long';
        $this->entryPrice = '';
        $this->exitPrice = '';
        $this->entryDate = '';
        $this->exitDate = '';
        $this->size = '';
        $this->stopLoss = '';
        $this->takeProfit = '';
        $this->pnlAmount = '0';
        $this->pnlPips = '';
        $this->outcome = 'open';
        $this->fees = '0';
        $this->strategyId = null;
        $this->notes = '';
        $this->screenshot = null;
        $this->isEditing = false;
        $this->editingId = null;
        $this->resetValidation();
    }

    // ──────────────────────────────────────
    // Validation rules
    // ──────────────────────────────────────

    protected function rules(): array
    {
        return [
            'pair' => 'required|string|max:20',
            'tradeType' => 'required|in:forex,crypto,stock,futures,commodity',
            'direction' => 'required|in:long,short',
            'entryPrice' => 'required|numeric|min:0',
            'exitPrice' => 'nullable|numeric|min:0',
            'entryDate' => 'required|date',
            'exitDate' => 'nullable|date',
            'size' => 'required|numeric|min:0',
            'stopLoss' => 'nullable|numeric|min:0',
            'takeProfit' => 'nullable|numeric|min:0',
            'pnlAmount' => 'nullable|numeric',
            'pnlPips' => 'nullable|numeric',
            'outcome' => 'required|in:open,win,loss,breakeven',
            'fees' => 'nullable|numeric|min:0',
            'strategyId' => 'nullable|integer|exists:strategies,id',
            'notes' => 'nullable|string|max:5000',
            'screenshot' => 'nullable|image|max:5120',
        ];
    }

    protected function messages(): array
    {
        return [
            'pair.required' => 'Pair / asset wajib diisi.',
            'pair.max' => 'Pair maksimal 20 karakter.',
            'entryPrice.required' => 'Harga entry wajib diisi.',
            'entryPrice.numeric' => 'Harga entry harus berupa angka.',
            'exitPrice.numeric' => 'Harga exit harus berupa angka.',
            'entryDate.required' => 'Tanggal entry wajib diisi.',
            'entryDate.date' => 'Format tanggal tidak valid.',
            'size.required' => 'Lot size wajib diisi.',
            'size.numeric' => 'Lot size harus berupa angka.',
            'outcome.required' => 'Outcome wajib dipilih.',
        ];
    }

    // ──────────────────────────────────────
    // Store
    // ──────────────────────────────────────

    public function store(): void
    {
        $this->validate();

        $portfolio = Portfolio::where('is_active', true)->first();
        if (!$portfolio) {
            session()->flash('error', 'Tidak ada portfolio aktif. Buat portfolio terlebih dahulu.');
            return;
        }

        $screenshotPath = $this->handleScreenshot();

        $strategy = $this->strategyId ? Strategy::find($this->strategyId) : null;

        Trade::create([
            'portfolio_id' => $portfolio->id,
            'strategy_id' => $this->strategyId,
            'pair' => strtoupper(trim($this->pair)),
            'trade_type' => $this->tradeType,
            'direction' => $this->direction,
            'entry_price' => $this->entryPrice,
            'exit_price' => $this->exitPrice !== '' ? $this->exitPrice : null,
            'entry_date' => $this->entryDate,
            'exit_date' => $this->exitDate !== '' ? $this->exitDate : null,
            'size' => $this->size,
            'stop_loss' => $this->stopLoss !== '' ? $this->stopLoss : null,
            'take_profit' => $this->takeProfit !== '' ? $this->takeProfit : null,
            'pnl_amount' => $this->pnlAmount ?? 0,
            'pnl_pips' => $this->pnlPips !== '' ? $this->pnlPips : null,
            'outcome' => $this->outcome,
            'fees' => $this->fees ?? 0,
            'strategy' => $strategy?->name,
            'notes' => $this->notes,
            'screenshot_path' => $screenshotPath,
        ]);

        $this->closeModal();
        $this->refreshLookups();

        session()->flash('success', 'Trade berhasil ditambahkan.');
    }

    // ──────────────────────────────────────
    // Update
    // ──────────────────────────────────────

    public function update(): void
    {
        $this->validate();

        $trade = Trade::findOrFail($this->editingId);

        $screenshotPath = $this->handleScreenshot();

        $strategy = $this->strategyId ? Strategy::find($this->strategyId) : null;

        $data = [
            'pair' => strtoupper(trim($this->pair)),
            'trade_type' => $this->tradeType,
            'direction' => $this->direction,
            'entry_price' => $this->entryPrice,
            'exit_price' => $this->exitPrice !== '' ? $this->exitPrice : null,
            'entry_date' => $this->entryDate,
            'exit_date' => $this->exitDate !== '' ? $this->exitDate : null,
            'size' => $this->size,
            'stop_loss' => $this->stopLoss !== '' ? $this->stopLoss : null,
            'take_profit' => $this->takeProfit !== '' ? $this->takeProfit : null,
            'pnl_amount' => $this->pnlAmount ?? 0,
            'pnl_pips' => $this->pnlPips !== '' ? $this->pnlPips : null,
            'outcome' => $this->outcome,
            'fees' => $this->fees ?? 0,
            'strategy_id' => $this->strategyId,
            'strategy' => $strategy?->name,
            'notes' => $this->notes,
        ];

        if ($screenshotPath) {
            // Delete old screenshot
            if ($trade->screenshot_path && Storage::disk('public')->exists($trade->screenshot_path)) {
                Storage::disk('public')->delete($trade->screenshot_path);
            }
            $data['screenshot_path'] = $screenshotPath;
        }

        $trade->update($data);

        $this->closeModal();
        $this->refreshLookups();

        session()->flash('success', 'Trade berhasil diupdate.');
    }

    // ──────────────────────────────────────
    // Delete
    // ──────────────────────────────────────

    public function confirmDelete(int $id): void
    {
        $this->deleteTradeId = $id;
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->deleteTradeId = null;
        $this->showDeleteModal = false;
    }

    public function deleteTrade(): void
    {
        $trade = Trade::findOrFail($this->deleteTradeId);

        // Delete screenshot
        if ($trade->screenshot_path && Storage::disk('public')->exists($trade->screenshot_path)) {
            Storage::disk('public')->delete($trade->screenshot_path);
        }

        $trade->delete();

        $this->deleteTradeId = null;
        $this->showDeleteModal = false;

        session()->flash('success', 'Trade berhasil dihapus.');
    }

    // ──────────────────────────────────────
    // Close Trade
    // ──────────────────────────────────────

    public function openCloseModal(int $id): void
    {
        $trade = Trade::findOrFail($id);
        $this->closingTradeId = $id;
        $this->closeExitPrice = $trade->exit_price !== null ? (string) $trade->exit_price : '';
        $this->closePnlAmount = '';
        $this->closePnlPips = '';
        $this->closeOutcome = '';
        $this->closeNotes = '';
        $this->showCloseModal = true;
    }

    public function cancelClose(): void
    {
        $this->closingTradeId = null;
        $this->showCloseModal = false;
        $this->resetValidation();
    }

    public function closeTrade(): void
    {
        $trade = Trade::findOrFail($this->closingTradeId);

        // Auto-calculate P&L if exit price is provided but P&L is empty
        if ($this->closePnlAmount === '' && $this->closeExitPrice !== '') {
            $pnl = $this->calculatePnl($trade, (float) $this->closeExitPrice);
            $this->closePnlAmount = (string) $pnl;

            // Auto-determine outcome
            if ($pnl > 0) {
                $this->closeOutcome = 'win';
            } elseif ($pnl < 0) {
                $this->closeOutcome = 'loss';
            } else {
                $this->closeOutcome = 'breakeven';
            }
        }

        // Validate close fields
        $this->validate([
            'closeExitPrice' => 'required|numeric|min:0',
            'closeOutcome' => 'required|in:win,loss,breakeven',
        ], [
            'closeExitPrice.required' => 'Harga exit wajib diisi.',
            'closeExitPrice.numeric' => 'Harga exit harus berupa angka.',
            'closeOutcome.required' => 'Outcome wajib dipilih.',
        ]);

        $trade->update([
            'exit_price' => $this->closeExitPrice,
            'exit_date' => now(),
            'pnl_amount' => $this->closePnlAmount ?? 0,
            'pnl_pips' => $this->closePnlPips !== '' ? $this->closePnlPips : null,
            'outcome' => $this->closeOutcome,
            'notes' => $this->closeNotes !== '' ? $this->closeNotes : $trade->notes,
        ]);

        // Update portfolio balance
        $this->updatePortfolioBalance($trade);

        $this->closingTradeId = null;
        $this->showCloseModal = false;

        session()->flash('success', 'Trade berhasil ditutup. P&L: $' . number_format($trade->pnl_amount, 2));
    }

    protected function calculatePnl(Trade $trade, float $exitPrice): float
    {
        $entry = (float) $trade->entry_price;
        $size = (float) $trade->size;
        $direction = $trade->direction;

        // Calculate raw P&L based on direction
        $pnl = $direction === 'long'
            ? ($exitPrice - $entry) * $size
            : ($entry - $exitPrice) * $size;

        // Subtract fees
        $pnl -= (float) $trade->fees;

        return round($pnl, 2);
    }

    protected function updatePortfolioBalance(Trade $trade): void
    {
        if (!$trade->portfolio_id) return;

        $portfolio = Portfolio::find($trade->portfolio_id);
        if (!$portfolio) return;

        // Update portfolio current balance
        $newBalance = (float) $portfolio->current_balance + (float) $trade->pnl_amount;
        $portfolio->update(['current_balance' => round($newBalance, 2)]);

        // Add balance history record
        BalanceHistory::create([
            'portfolio_id' => $portfolio->id,
            'date' => now()->toDateString(),
            'balance' => round($newBalance, 2),
            'equity' => round($newBalance, 2),
            'deposit' => 0,
            'withdrawal' => 0,
            'notes' => "Trade closed: {$trade->pair} " . ($trade->pnl_amount >= 0 ? '+' : '') . '$' . number_format($trade->pnl_amount, 2),
        ]);
    }

    // ──────────────────────────────────────
    // Screenshot upload
    // ──────────────────────────────────────

    protected function handleScreenshot(): ?string
    {
        if (!$this->screenshot) return null;

        $path = $this->screenshot->store('screenshots', 'public');
        return $path;
    }

    // ──────────────────────────────────────
    // Render
    // ──────────────────────────────────────

    public function render()
    {
        return view('livewire.trade-journal.trade-journal-page')
            ->layout('layouts.app', ['title' => 'Trade Journal — ' . config('app.name')]);
    }
}
