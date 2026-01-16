<?php

namespace Database\Seeders;

use App\Models\Info;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SiteInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Info::truncate();

        $siteInfo = [
            'home' => [
                'background_image' => 'url',
                'doctor_image' => 'url',
                'doctor_cv' => 'text',
                'doctor_name' => 'name',
                'first_phrase' => 'text',
                'second_phrase' => 'text',
                'third_phrase' => 'text',
            ],
            'social' => [
                'facebook' => 'facebook',
                'instgram' => 'instgram',
                'youtube' => 'youtube',
                'phone_number_1' => 'phone_number_1',
                'phone_number_2' => 'phone_number_2',
                'behance' => 'behance',
                'tiktok' => 'tiktok',
                'telegram' => 'telegram',
                'email' => 'email',
            ],
            'general' => [
                'privacy_text' => 'text',
                'privacy_image' => 'url',
                'terms_text' => 'text',
                'terms_image' => 'url',
            ],
        ];
        $dataToSeed = [];
        foreach ($siteInfo as $superKey => $datum) {
            foreach ($datum as $key => $item) {
                $dataToSeed[] = [
                    'super_key' => $superKey,
                    'key' => $key,
                    'value' => is_array($item) ? json_encode($item, JSON_UNESCAPED_UNICODE) : $item,
                ];
            }
        }
        Info::insert($dataToSeed);
    }
}
