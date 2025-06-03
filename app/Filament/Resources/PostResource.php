<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers\UsersRelationManager;
use App\Models\Post;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'far-sign-posts';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make()->tabs([
                    Tab::make('Media')
                        ->icon('far-image')
                        // ->iconPosition(IconPosition::After)
                        // ->badge('Badge')
                        ->schema([
                            FileUpload::make('thumbnail')
                                ->disk('public')
                                ->directory('thumbnails'),
                        ]),
                    Tab::make('Info')
                        ->icon('far-circle-info')
                        ->schema([
                            TextInput::make('title')
                                ->required(),
                            TextInput::make('slug')
                                ->unique(ignoreRecord: true)
                                ->required(),
                            Select::make('category_id')
                                ->label('Category')
                                // ->searchable()
                                ->relationship('category', 'name'),
                            ColorPicker::make('color')->required(),
                            MarkdownEditor::make('content')->required()
                                ->columnSpanFull(),
                        ]),
                    Tab::make('Tags')
                        ->icon('far-tags')
                        ->schema([
                            TagsInput::make('tags')->required(),
                            Checkbox::make('is_published'),
                        ]),
                ]),
                // ->persistTabInQueryString()
                // ->activeTab(2)
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail')
                    ->toggleable(),
                ColorColumn::make('color')
                    ->toggleable(),
                TextColumn::make('title')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('tags')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                CheckboxColumn::make('is_published')
                    ->label('Published')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Published On')
                    ->date()
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->actionsColumnLabel('Actions')
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
            UsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
