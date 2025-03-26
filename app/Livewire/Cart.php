<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class CartComponent extends Component
{
    public $cartItems = [];
    public $totalAmount = 0;

    public function mount()
    {
        $this->loadCart();
    }

    public function loadCart()
    {
        if (Auth::check()) {
            $this->cartItems = Cart::where('user_id', Auth::id())->get();
            $this->totalAmount = $this->cartItems->sum(fn ($item) => $item->quantity * $item->product->price);
        }
    }

    public function removeItem($id)
    {
        Cart::find($id)->delete();
        $this->loadCart();
    }

    public function checkout()
    {
        if (empty($this->cartItems)) {
            session()->flash('error', 'Keranjang kosong!');
            return;
        }

        $transaction = Transaction::create([
            'user_id' => Auth::id(),
            'total_amount' => $this->totalAmount,
            'status' => 'pending'
        ]);

        foreach ($this->cartItems as $item) {
            $transaction->items()->create([
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price
            ]);
        }

        Cart::where('user_id', Auth::id())->delete();
        return redirect()->to('/checkout/' . $transaction->id);
    }

    public function render()
    {
        return view('livewire.cart-component');
    }
}
