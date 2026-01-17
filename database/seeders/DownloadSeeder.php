<?php

namespace Database\Seeders;

use App\Models\Download;
use Illuminate\Database\Seeder;

class DownloadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fullDownloads = [
            [
                'name' => 'Full Client from Google Drive v8 (3GB)',
                'link' => 'https://drive.google.com/file/d/1vRuWdZyEv_ouPa_pk9G1OZi8UwH4FvrN/view?usp=drive_link',
                'button_style' => Download::BUTTON_STYLE_PRIMARY,
            ],
            [
                'name' => 'Full Client from Mega v8 (3GB)',
                'link' => 'https://mega.nz/file/mc0S1TbA#0gIqKxDO8bcWpNPCltAcc1YgOCkmQPvEe0jCfRBXiN0',
                'button_style' => Download::BUTTON_STYLE_SECONDARY,
            ],
        ];

        $androidDownloads = [
            [
                'name' => 'v526 - Android using Discord (3MB)',
                'link' => 'https://cdn.discordapp.com/attachments/1213304912029679677/1337133059316781159/XileRetro2_v526_20250206182933.apk?ex=67f812b4&is=67f6c134&hm=73c0b8b873597d5aa3931ebf3717abf504c3be31c1d62d7c9d35d9aef0dbc114&',
                'button_style' => Download::BUTTON_STYLE_PRIMARY,
                'version' => '511.2',
            ],
            [
                'name' => 'v526 - Android using Discord (3MB)',
                'link' => 'https://cdn.discordapp.com/attachments/1213304912029679677/1337133059316781159/XileRetro2_v526_20250206182933.apk?ex=67f812b4&is=67f6c134&hm=73c0b8b873597d5aa3931ebf3717abf504c3be31c1d62d7c9d35d9aef0dbc114&',
                'button_style' => Download::BUTTON_STYLE_SECONDARY,
                'version' => '511.2',
            ],
        ];

        foreach ($fullDownloads as $index => $download) {
            Download::create([
                'name' => $download['name'],
                'type' => Download::TYPE_FULL,
                'link' => $download['link'],
                'button_style' => $download['button_style'],
                'display_order' => $index,
                'enabled' => true,
            ]);
        }

        foreach ($androidDownloads as $index => $download) {
            Download::create([
                'name' => $download['name'],
                'type' => Download::TYPE_ANDROID,
                'link' => $download['link'],
                'version' => $download['version'] ?? null,
                'button_style' => $download['button_style'],
                'display_order' => $index,
                'enabled' => true,
            ]);
        }
    }
}
