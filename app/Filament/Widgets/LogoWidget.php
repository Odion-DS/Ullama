<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class LogoWidget extends Widget
{
    protected string $view = 'filament.widgets.logo-widget';

    protected int|string|array $columnSpan = 1;
}
