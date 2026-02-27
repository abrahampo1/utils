<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            [
                'name' => 'Asoft',
                'charge' => 'Full Stack Developer',
                'url_label' => 'asoft.es',
                'image' => 'https://www.asoft.es/asoft-logo.png',
                'url' => 'https://asoft.es',
                'dark_mode' => false,
            ],
            [
                'name' => 'BloxyCorp',
                'charge' => 'Full Stack Developer',
                'url_label' => 'bloxycorp.com',
                'url' => 'https://bloxycorp.com',
                'image' => 'https://www.bloxycorp.com/logo.svg',
                'dark_mode' => true,
            ],
            [
                'name' => 'Tubuencamino',
                'charge' => 'Full Stack Developer',
                'url_label' => 'tubuencamino.com',
                'url' => 'https://tubuencamino.com',
                'image' => 'https://tubuencamino.com/imagenes/logotipo/logo-tbc.compressed.png',
                'dark_mode' => false,
            ],
            [
                'name' => 'Dr.Whisk3rs',
                'charge' => 'Full Stack Developer',
                'url_label' => 'drwhisk3rs.com',
                'url' => 'https://drwhisk3rs.com',
                'image' => 'https://i.imgur.com/R8Wo52T.png',
                'dark_mode' => false,
            ],
            [
                'name' => 'Pumm',
                'charge' => 'Designer',
                'url' => 'https://pumm.io',
                'url_label' => 'pumm.io',
                'image' => 'https://pumm.io/assets/pummlogo.a0d7a614.png',
                'dark_mode' => false,
            ],
            [
                'name' => 'Roam',
                'charge' => 'Full Stack Developer',
                'url' => 'https://abrahampo1.github.io/roam/',
                'url_label' => 'github.io/roam',
                'image' => 'https://github.com/abrahampo1/roam/blob/master/src/image/logo.png?raw=true',
                'dark_mode' => false,
            ],
            [
                'name' => 'TBI',
                'url_label' => 'tbi-software.com',
                'charge' => 'Full Stack Developer',
                'image' => 'https://tbi-software.com/tbi-logo.png',
                'url' => 'https://tbi-software.com',
                'dark_mode' => false,
            ],
            [
                'name' => 'Cannagest',
                'charge' => 'Full Stack Developer',
                'url_label' => 'cannagest.com',
                'image' => 'https://www.asoft.es/asoft-logo.png',
                'url' => 'https://cannagest.com',
                'dark_mode' => false,
            ],
            [
                'name' => 'CryptoGest',
                'charge' => 'Full Stack Developer',
                'url_label' => 'cryptogest.app',
                'image' => 'https://cryptogest.app/images/logo.png',
                'url' => 'https://cryptogest.app',
                'dark_mode' => false,
            ],
        ];

        foreach ($projects as $position => $project) {
            Project::updateOrCreate(
                ['name' => $project['name']],
                array_merge($project, ['position' => $position, 'is_visible' => true])
            );
        }
    }
}
