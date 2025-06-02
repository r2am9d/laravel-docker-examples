<?php

declare(strict_types=1);

namespace App\Filament\Resources\CategoryResource\RelationManagers;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class PostsRelationManager extends RelationManager
{
    protected static string $relationship = 'posts';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Media')
                    ->collapsible()
                    ->schema([
                        FileUpload::make('thumbnail')
                            ->disk('public')
                            ->directory('thumbnails'),
                    ]),
                Section::make('Information')
                    ->collapsible()
                    ->schema([
                        TextInput::make('title')
                            ->required(),
                        TextInput::make('slug')
                            ->unique(ignoreRecord: true)
                            ->required(),
                        ColorPicker::make('color')->required(),
                        MarkdownEditor::make('content')->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Tags')
                    ->collapsible()
                    ->schema([
                        TagsInput::make('tags')->required(),
                        Checkbox::make('is_published'),
                    ]),
            ]);
    }

    public function table(Table $table): Table
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
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
