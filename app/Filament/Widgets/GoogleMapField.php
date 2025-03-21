<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\Field;
use Filament\Forms\Get;
use Filament\Forms\Set;

class GoogleMapField extends Field
{
    protected  string $view = 'filament.widgets.google-map-field';

     public static function make(string $name): static
    {
        return parent::make($name);
    }
}
