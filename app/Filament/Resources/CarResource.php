<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CarResource\Pages;
use App\Filament\Resources\CarResource\RelationManagers\MediaRelationManager;
use App\Models\Car;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CarResource extends Resource
{
    protected static ?string $model = Car::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Vehicle')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                $set('slug', Str::slug($state));
                            }),
                        Forms\Components\TextInput::make('brand')->required()->maxLength(120),
                        Forms\Components\TextInput::make('model')->required()->maxLength(120),
                        Forms\Components\TextInput::make('year')->numeric()->required(),
                        Forms\Components\TextInput::make('price')->numeric()->required(),
                        Forms\Components\TextInput::make('mileage')->numeric()->required(),
                        Forms\Components\Select::make('fuel_type')
                            ->options([
                                'Gasoline' => 'Gasoline',
                                'Diesel' => 'Diesel',
                                'Hybrid' => 'Hybrid',
                                'Electric' => 'Electric',
                            ])
                            ->required(),
                        Forms\Components\Select::make('transmission')
                            ->options([
                                'Automatic' => 'Automatic',
                                'Manual' => 'Manual',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->rows(6),
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Published',
                            ])
                            ->default('draft')
                            ->required(),
                    ])->columns(2),
                Forms\Components\Section::make('SEO')
                    ->schema([
                        Forms\Components\TextInput::make('meta_title')->maxLength(255),
                        Forms\Components\Textarea::make('meta_description')->rows(3),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255),
                    ])->columns(2),
                Forms\Components\Section::make('Media')
                    ->schema([
                        Forms\Components\FileUpload::make('featured_image_path')
                            ->image()
                            ->directory('cars/featured')
                            ->disk('public')
                            ->imagePreviewHeight('180'),
                        Forms\Components\FileUpload::make('video_path')
                            ->acceptedFileTypes(['video/mp4', 'video/webm'])
                            ->directory('cars/videos')
                            ->disk('public')
                            ->helperText('Upload a short MP4/WebM clip.'),
                        Forms\Components\TextInput::make('video_url')
                            ->url()
                            ->maxLength(255)
                            ->helperText('Optional YouTube/Vimeo URL.'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('brand')->sortable(),
                Tables\Columns\TextColumn::make('model')->sortable(),
                Tables\Columns\TextColumn::make('year')->sortable(),
                Tables\Columns\TextColumn::make('price')->money('USD')->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'draft',
                        'success' => 'published',
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('brand')
                    ->options(Car::query()->distinct()->pluck('brand', 'brand')->toArray()),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                    ]),
                Tables\Filters\Filter::make('year')
                    ->form([
                        Forms\Components\TextInput::make('from')->numeric(),
                        Forms\Components\TextInput::make('to')->numeric(),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'] ?? null, fn ($q, $value) => $q->where('year', '>=', $value))
                            ->when($data['to'] ?? null, fn ($q, $value) => $q->where('year', '<=', $value));
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            MediaRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCars::route('/'),
            'create' => Pages\CreateCar::route('/create'),
            'edit' => Pages\EditCar::route('/{record}/edit'),
        ];
    }
}
