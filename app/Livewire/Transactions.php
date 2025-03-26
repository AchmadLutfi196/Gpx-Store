<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class Transactions extends Component
{
    public $transactions;
    public $selectedTransaction = null;

    public function mount()
    {
        $this->transactions = Transaction::where('user_id', Auth::id())->latest()->get();
    }

    public function viewDetail($id)
    {
        $this->selectedTransaction = Transaction::with('items.product')->find($id);
    }

    public function closeDetail()
    {
        $this->selectedTransaction = null;
    }

    public function render()
    {
        return view('livewire.transactions');
    }
}
