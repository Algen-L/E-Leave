@extends('layouts.app')

@section('title', $item->name)

@section('content')
<div class="mb-6">
    <a href="{{ route('items.index') }}" class="text-indigo-600 hover:text-indigo-800">
        <i class="fas fa-arrow-left mr-1"></i> Back to Items
    </a>
    <h1 class="text-3xl font-bold text-gray-900 mt-2">{{ $item->name }}</h1>
    <p class="mt-1 text-sm text-gray-600">Item Details</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Details -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Item Information</h2>
            
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                    <dd class="mt-1 text-lg text-gray-900">{{ $item->name }}</dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">SKU</dt>
                    <dd class="mt-1">
                        <span class="px-3 py-1 text-sm font-mono bg-gray-100 rounded">{{ $item->sku }}</span>
                    </dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">Category</dt>
                    <dd class="mt-1">
                        @if($item->category)
                            <span class="px-3 py-1 text-sm bg-indigo-100 text-indigo-800 rounded-full">{{ $item->category }}</span>
                        @else
                            <span class="text-gray-400">Not categorized</span>
                        @endif
                    </dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">Created</dt>
                    <dd class="mt-1 text-gray-900">{{ $item->created_at->format('M d, Y h:i A') }}</dd>
                </div>
            </dl>

            @if($item->description)
                <div class="mt-6 pt-6 border-t">
                    <dt class="text-sm font-medium text-gray-500">Description</dt>
                    <dd class="mt-2 text-gray-900">{{ $item->description }}</dd>
                </div>
            @endif
        </div>
    </div>

    <!-- Side Panel -->
    <div class="space-y-6">
        <!-- Stock Info -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Stock & Pricing</h2>
            
            <div class="space-y-4">
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="text-sm text-gray-500">Quantity in Stock</p>
                        <p class="text-3xl font-bold 
                            @if($item->quantity < 10) text-red-600
                            @elseif($item->quantity < 50) text-yellow-600
                            @else text-green-600
                            @endif">
                            {{ $item->quantity }}
                        </p>
                    </div>
                    <div class="p-3 rounded-full
                        @if($item->quantity < 10) bg-red-100
                        @elseif($item->quantity < 50) bg-yellow-100
                        @else bg-green-100
                        @endif">
                        <i class="fas fa-cubes text-2xl
                            @if($item->quantity < 10) text-red-600
                            @elseif($item->quantity < 50) text-yellow-600
                            @else text-green-600
                            @endif"></i>
                    </div>
                </div>

                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="text-sm text-gray-500">Unit Price</p>
                        <p class="text-3xl font-bold text-gray-900">${{ number_format($item->price, 2) }}</p>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-full">
                        <i class="fas fa-tag text-2xl text-purple-600"></i>
                    </div>
                </div>

                <div class="flex justify-between items-center p-4 bg-indigo-50 rounded-lg">
                    <div>
                        <p class="text-sm text-gray-500">Total Value</p>
                        <p class="text-3xl font-bold text-indigo-600">${{ number_format($item->quantity * $item->price, 2) }}</p>
                    </div>
                    <div class="p-3 bg-indigo-100 rounded-full">
                        <i class="fas fa-dollar-sign text-2xl text-indigo-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Actions</h2>
            
            <div class="space-y-3">
                <a href="{{ route('items.edit', $item) }}" 
                    class="w-full flex items-center justify-center px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600">
                    <i class="fas fa-edit mr-2"></i> Edit Item
                </a>
                
                <form action="{{ route('items.destroy', $item) }}" method="POST" 
                    onsubmit="return confirm('Are you sure you want to delete this item?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                        class="w-full flex items-center justify-center px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                        <i class="fas fa-trash mr-2"></i> Delete Item
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
