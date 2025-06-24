<div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6">
    @foreach($products as $product)
        <div class="bg-white shadow-lg rounded-xl overflow-hidden">
            <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" class="w-full h-60 object-cover">
            <div class="p-4">
                <h3 class="text-lg font-semibold">{{ $product['name'] }}</h3>
                <p class="text-gray-500">${{ $product['price'] }}</p>
                <div class="flex gap-2 mt-4">
                    <button wire:click="addToCart({{ $product['id'] }})" class="bg-black text-white px-4 py-1 rounded hover:bg-gray-800">Add to Cart</button>
                    <button wire:click="addToWishlist({{ $product['id'] }})" class="bg-gray-100 text-black px-4 py-1 rounded hover:bg-gray-200">❤️ Wishlist</button>
                </div>
            </div>
        </div>
    @endforeach
</div>
