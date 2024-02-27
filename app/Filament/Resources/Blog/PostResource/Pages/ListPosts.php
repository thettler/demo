<?php

namespace App\Filament\Resources\Blog\PostResource\Pages;

use App\Filament\Resources\Blog\PostResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\LocaleSwitcher;

class ListPosts extends ListRecords
{
    use ListRecords\Concerns\Translatable;
    protected static string $resource = PostResource::class;

    protected function getActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\CreateAction::make(),
        ];
    }
}
