<?php

namespace App\Livewire;

use Livewire\Component;

class ProductList extends Component
{
    public $products = [];

    public function mount()
    {
        $this->products = [
            ['id' => 1, 'name' => 'Sneaker 1', 'price' => 99, 'image' => '/images/sneaker1.jpg'],
            ['id' => 2, 'name' => 'Sneaker 2', 'price' => 120, 'image' => '/images/sneaker2.jpg'],
            ['id' => 3, 'name' => 'Sneaker 3', 'price' => 89, 'image' => '/images/sneaker3.jpg'],
        ];
    }

    public function addToCart($id)
    {
        session()->flash('message', "Product $id added to cart.");
    }

    public function addToWishlist($id)
    {
        session()->flash('message', "Product $id added to wishlist.");
    }

    public function render()
    {
        return view('livewire.product-list');
    }
}
