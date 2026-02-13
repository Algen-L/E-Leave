@extends('layouts.app')

@section('title', 'Inventory Items')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Inventory Items</h1>
    <p class="mt-1 text-sm text-gray-600">Manage your inventory items</p>
</div>

<!-- Search and Filter -->
<div class="bg-white rounded-lg shadow mb-6 p-4">
    <form action="{{ route('items.index') }}" method="GET" class="flex flex-wrap gap-4">
        <div class="flex-1 min-w-64">
            <input type="text" name="search" value="{{ request('search') }}" 
                placeholder="Search by name, SKU, or category..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div class="w-48">
            <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                        {{ $category }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">
            <i class="fas fa-search mr-1"></i> Search
        </button>
        <a href="{{ route('items.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300">
            <i class="fas fa-times mr-1"></i> Clear
        </a>
    </form>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="p-3 bg-indigo-100 rounded-full">
                <i class="fas fa-box text-indigo-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Total Items</p>
                <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Item::count() }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="p-3 bg-green-100 rounded-full">
                <i class="fas fa-cubes text-green-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Total Stock</p>
                <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Item::sum('quantity') }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="p-3 bg-yellow-100 rounded-full">
                <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Low Stock</p>
                <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Item::where('quantity', '<', 10)->count() }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="p-3 bg-purple-100 rounded-full">
                <i class="fas fa-dollar-sign text-purple-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Total Value</p>
                <p class="text-2xl font-bold text-gray-900">${{ number_format(\App\Models\Item::selectRaw('SUM(quantity * price) as total')->value('total') ?? 0, 2) }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Items Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($items as $item)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $item->name }}</div>
                        @if($item->description)
                            <div class="text-sm text-gray-500 truncate max-w-xs">{{ Str::limit($item->description, 50) }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-mono bg-gray-100 rounded">{{ $item->sku }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($item->category)
                            <span class="px-2 py-1 text-xs bg-indigo-100 text-indigo-800 rounded-full">{{ $item->category }}</span>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($item->quantity < 10)
                            <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">{{ $item->quantity }}</span>
                        @elseif($item->quantity < 50)
                            <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">{{ $item->quantity }}</span>
                        @else
                            <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">{{ $item->quantity }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        ${{ number_format($item->price, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('items.show', $item) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('items.edit', $item) }}" class="text-yellow-600 hover:text-yellow-900 mr-3">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('items.destroy', $item) }}" method="POST" class="inline" 
                            onsubmit="return confirm('Are you sure you want to delete this item?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="text-gray-500">
                            <i class="fas fa-box-open text-4xl mb-4"></i>
                            <p class="text-lg">No items found</p>
                            <p class="text-sm">Get started by adding your first item.</p>
                            <a href="{{ route('items.create') }}" class="mt-4 inline-block bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                                <i class="fas fa-plus mr-1"></i> Add Item
                            </a>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    @if($items->hasPages())
        <div class="px-6 py-4 border-t">
            {{ $items->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
