<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use App\Models\ProductCategory;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationLabel = 'Manajemen Produk';
    protected static ?string $navigationGroup = 'Manajemen Menu';

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return parent::getEloquentQuery();
        }

        return parent::getEloquentQuery()->where('user_id', $user->id);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Toko')
                    ->relationship('user', 'name')
                    ->required()
                    ->reactive()
                    ->hidden(fn () => Auth::user()->role === 'store'),
                Forms\Components\Select::make('product_category_id')
                    ->label('Kategori Produk')
                    ->relationship('productCategory', 'name')
                    ->required()
                    ->disabled(fn(callable $get) => $get('user_id') === null)
                    ->options(function (callable $get) {
                        $userId = $get('user_id');

                        if (!$userId) {
                            return [];
                        }

                        return ProductCategory::where('user_id', $userId)->pluck('name', 'id');
                    })
                    ->hidden(fn () => Auth::user()->role === 'store'),
                Forms\Components\Select::make('product_category_id')
                    ->label('Kategori Produk')
                    ->relationship('productCategory', 'name')
                    ->required()
                    ->options(function () {
                        return ProductCategory::where('user_id', Auth::user()->id)->pluck('name', 'id');
                    })
                    ->hidden(fn () => Auth::user()->role === 'admin'),
                Forms\Components\FileUpload::make('image')
                    ->label('Foto Menu')
                    ->image()
					->directory('product-image')
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->label('Nama Menu')
                    ->required(),
                Forms\Components\TextArea::make('description')
                    ->label('Deskripsi Menu')
                    ->required(),
                Forms\Components\TextInput::make('price')
                    ->label('Harga Menu')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('rating')
                    ->label('Rating Menu')
                    ->numeric()
                    ->required(),
                Forms\Components\Toggle::make('is_popular')
                    ->label('Menu Populer')
                    ->required(),
                Forms\Components\Repeater::make('productIngredients')
                    ->label('Bahan Menu')
                    ->relationship('productIngredients')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Bahan')
                            ->required(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Toko')
                    ->hidden(fn () => Auth::user()->role === 'store'),
                Tables\Columns\TextColumn::make('productCategory.name')
                    ->label('Kategori'),
                Tables\Columns\ImageColumn::make('image')
                    ->label('Foto Menu')
                    ->circular(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->formatStateUsing(function (string $state) {
                        return 'Rp ' . number_format($state);
                    }),
                Tables\Columns\TextColumn::make('rating')
                    ->label('Rating Menu'),
                Tables\Columns\ToggleColumn::make('is_popular')
                    ->label('Menu Populer'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Toko')
                    ->relationship('user', 'name')
                    ->hidden(fn () => Auth::user()->role === 'store'),
                Tables\Filters\SelectFilter::make('product_category_id')
                    ->label('Kategori Menu')
                    ->options(function () {
                        if (Auth::user()->role === 'admin') {
                            return ProductCategory::pluck('name', 'id');
                        }
                        return ProductCategory::where('user_id', Auth::user()->id)->pluck('name', 'id');
                    })
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
