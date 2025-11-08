<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Session;

class LanguageSwitcher extends Widget
{
    protected static string $view = 'filament.widgets.language-switcher';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = -100;

    public function switchLanguage(string $locale): void
    {
        Session::put('locale', $locale);
        app()->setLocale($locale);

        $this->dispatch('languageChanged');

        // Refresh the page to apply translations
        $this->redirect(request()->header('Referer'));
    }
}
