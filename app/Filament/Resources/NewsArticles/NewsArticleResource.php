<?php

namespace App\Filament\Resources\NewsArticles;

use App\Filament\Resources\NewsArticles\Pages\CreateNewsArticle;
use App\Filament\Resources\NewsArticles\Pages\EditNewsArticle;
use App\Filament\Resources\NewsArticles\Pages\ListNewsArticles;
use App\Filament\Resources\NewsArticles\Schemas\NewsArticleForm;
use App\Filament\Resources\NewsArticles\Tables\NewsArticlesTable;
use App\Models\NewsArticle;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class NewsArticleResource extends Resource
{
    use \LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;

    protected static ?string $model = NewsArticle::class;

    public static function getNavigationLabel(): string
    {
        return __('News');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Communication');
    }

    public static function getLabel(): ?string
    {
        return __('News');
    }

    public static function getPluralLabel(): ?string
    {
        return __('News');
    }

    protected static ?int $navigationSort = 4;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-newspaper';

    public static function form(Schema $schema): Schema
    {
        return NewsArticleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NewsArticlesTable::configure($table);
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
            'index' => ListNewsArticles::route('/'),
            'create' => CreateNewsArticle::route('/create'),
            'edit' => EditNewsArticle::route('/{record}/edit'),
        ];
    }
}
