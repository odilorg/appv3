<?php

namespace App\Filament\Resources;

use App\Enums\CustomerType;
use App\Filament\Resources\CustomerResource\Pages;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class CustomerResource extends \Filament\Resources\Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'CRM';
    protected static ?string $navigationLabel = 'Customers';
    protected static ?string $modelLabel = 'Customer';
    protected static ?string $pluralModelLabel = 'Customers';
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Customer')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('type')
                        ->label('Type')
                        ->options(CustomerType::options())
                        ->required()
                        ->default(CustomerType::INDIVIDUAL->value)
                        ->live(),

                    Forms\Components\TextInput::make('name')
                        ->label('Display Name')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->maxLength(255)
                        ->required(fn (Forms\Get $get) => $get('type') === CustomerType::COMPANY->value),

                    Forms\Components\TextInput::make('phone')
                        ->tel()
                        ->maxLength(255)
                        ->required(fn (Forms\Get $get) => $get('type') === CustomerType::INDIVIDUAL->value),

                    Forms\Components\TextInput::make('preferred_language')->maxLength(255),
                    Forms\Components\TextInput::make('country_code')->maxLength(2),
                    Forms\Components\TextInput::make('city')->maxLength(255),
                    Forms\Components\TextInput::make('source')->maxLength(255),

                    Forms\Components\Toggle::make('marketing_opt_in')->default(false),

                    Forms\Components\Textarea::make('notes')
                        ->columnSpanFull(),
                ]),

            // INDIVIDUAL profile subform (hasOne)
            Forms\Components\Section::make('Individual Details')
                ->visible(fn (Forms\Get $get) => $get('type') === CustomerType::INDIVIDUAL->value)
                ->relationship('individualProfile')
                ->columns(2)
                ->schema([
                    Forms\Components\DatePicker::make('dob'),
                    Forms\Components\TextInput::make('nationality')->maxLength(120),
                    Forms\Components\TextInput::make('passport_number')->maxLength(120),
                    Forms\Components\DatePicker::make('passport_expiry'),
                ]),

            // COMPANY profile subform (hasOne)
            Forms\Components\Section::make('Company Details')
                ->visible(fn (Forms\Get $get) => $get('type') === CustomerType::COMPANY->value)
                ->relationship('companyProfile')
                ->columns(2)
                ->schema([
                    // Pick (or create) an existing Company record that you already use for suppliers
                    Forms\Components\Select::make('company_id')
                        ->label('Company')
                        ->relationship('company', 'name') // belongsTo on CompanyProfile
                        ->searchable()
                        ->preload()
                        ->createOptionForm([
                            // Quick inline create form for Company (adjust fields to your existing model)
                            Forms\Components\TextInput::make('name')->required()->maxLength(255),
                            Forms\Components\TextInput::make('address_street')->maxLength(255),
                            Forms\Components\TextInput::make('address_city')->maxLength(255),
                            Forms\Components\TextInput::make('phone')->maxLength(255),
                            Forms\Components\TextInput::make('email')->email()->maxLength(255),
                            Forms\Components\TextInput::make('inn')->maxLength(255),
                            Forms\Components\TextInput::make('account_number')->maxLength(255),
                            Forms\Components\TextInput::make('bank_name')->maxLength(255),
                            Forms\Components\TextInput::make('bank_mfo')->maxLength(255),
                            Forms\Components\TextInput::make('director_name')->maxLength(255),
                            Forms\Components\TextInput::make('license_number')->maxLength(255),
                            Forms\Components\Toggle::make('is_operator')->default(false),
                            // You can add file upload for 'logo' if your Company model supports it
                        ])
                        ->required(),

                    Forms\Components\Toggle::make('is_agency')->label('This is an agency'),
                    Forms\Components\TextInput::make('commission_rate')
                        ->numeric()
                        ->suffix('%')
                        ->minValue(0)
                        ->maxValue(100),
                    Forms\Components\TextInput::make('account_manager')->maxLength(255),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('marketing_opt_in')
                    ->boolean()
                    ->label('Marketing'),

                Tables\Columns\TextColumn::make('city')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->since(), // nice relative display
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(CustomerType::options()),
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

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit'   => Pages\EditCustomer::route('/{record}/edit'),
            'view'   => Pages\ViewCustomer::route('/{record}'),
        ];
    }
}
