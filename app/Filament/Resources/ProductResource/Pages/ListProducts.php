<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\Product;
use App\Models\Subscription;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        if (Auth::user()->role === 'admin') {
            return [
                Actions\CreateAction::make(),
            ];
        }

        $subscription = Subscription::where('user_id', Auth::user()->id)
            ->where('end_date', '>', now())
            ->where('is_active', true)
            ->latest()
            ->first();

        $countProducts = Product::where('user_id', Auth::user()->id)->count();

        return [
            Actions\Action::make('alert')
                ->label('Produk kamu sudah mencapai batas gratis. Silakan berlangganan untuk menambah produk lebih banyak.')
                ->color('danger')
                ->icon('heroicon-s-exclamation-triangle')
                ->visible(!$subscription && $countProducts >= 5),
            Actions\CreateAction::make(),
        ];
    }
}
