<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Language;

class LanguageSeeder extends Seeder
{
    public function run(): void
    {
        $languages = [
    ['code' => 'en', 'name' => 'English'],
    ['code' => 'es', 'name' => 'Spanish'],
    ['code' => 'fr', 'name' => 'French'],
    ['code' => 'de', 'name' => 'German'],
    ['code' => 'it', 'name' => 'Italian'],
    ['code' => 'pt', 'name' => 'Portuguese'],
    ['code' => 'ru', 'name' => 'Russian'],
    ['code' => 'zh', 'name' => 'Chinese'],
    ['code' => 'ja', 'name' => 'Japanese'],
    ['code' => 'ko', 'name' => 'Korean'],
    ['code' => 'ar', 'name' => 'Arabic'],
    ['code' => 'hi', 'name' => 'Hindi'],
    ['code' => 'bn', 'name' => 'Bengali'],
    ['code' => 'ur', 'name' => 'Urdu'],
    ['code' => 'tr', 'name' => 'Turkish'],
    ['code' => 'fa', 'name' => 'Persian'],
    ['code' => 'pl', 'name' => 'Polish'],
    ['code' => 'uk', 'name' => 'Ukrainian'],
    ['code' => 'nl', 'name' => 'Dutch'],
    ['code' => 'sv', 'name' => 'Swedish'],
    ['code' => 'no', 'name' => 'Norwegian'],
    ['code' => 'da', 'name' => 'Danish'],
    ['code' => 'fi', 'name' => 'Finnish'],
    ['code' => 'cs', 'name' => 'Czech'],
    ['code' => 'el', 'name' => 'Greek'],
    ['code' => 'he', 'name' => 'Hebrew'],
    ['code' => 'th', 'name' => 'Thai'],
    ['code' => 'id', 'name' => 'Indonesian'],
    ['code' => 'ms', 'name' => 'Malay'],
    ['code' => 'vi', 'name' => 'Vietnamese'],
];


        Language::upsert($languages, ['code'], ['name']);
    }
}
