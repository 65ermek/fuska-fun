<?php

namespace Database\Seeders;

use App\Models\Job;
use App\Models\JobCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RealisticJobsSeeder extends Seeder
{
    public function run(): void
    {
        // сначала подчистим прошлые демо-объявления этого сидера
        Job::where('ua', 'realistic-seeder')->delete();

        // словарь по slug категорий
        $data = [
            'stavba' => [
                'titles' => [
                    'Pomocník na stavbě',
                    'Zedník – brigádně',
                    'Výpomoc při rekonstrukci bytu',
                    'Lehké bourací práce',
                ],
                'descs' => [
                    'Hledáme šikovného pomocníka na stavbě rodinného domu v Praze 9. Vyplaceno ihned po práci.',
                    'Zednické práce – omítky, ytong. Zkušenost výhodou, nářadí máme.',
                    'Jednoduchá výpomoc, odnos materiálu, úklid stavby.',
                ],
            ],
            'uklid' => [
                'titles' => [
                    'Úklid bytu 2+kk',
                    'Pravidelný úklid kanceláře',
                    'Jednorázový úklid po malování',
                ],
                'descs' => [
                    'Úklid bytu v centru, cca 3 hodiny, vlastní prostředky výhodou.',
                    'Hledáme paní na úklid 1× týdně, Praha 4.',
                    'Požadujeme pečlivost, vyplaceno na ruku.',
                ],
            ],
            'doprava' => [
                'titles' => [
                    'Řidič B – rozvoz balíků',
                    'Pomoc při stěhování',
                    'Kurýr po Praze – vlastní auto výhodou',
                ],
                'descs' => [
                    'Rozvoz zásilek po Praze, start v 8:00, konec dle situace.',
                    'Naložit, vyložit, ideálně někdo zdatnější.',
                    'Mzda podle počtu rozvozů.',
                ],
            ],
            'montaz' => [
                'titles' => [
                    'Montáž nábytku – brigáda',
                    'Skladání kuchyně – výpomoc',
                ],
                'descs' => [
                    'Jednoduchá montáž nábytku IKEA, nářadí máme.',
                    'Hledáme kutila na montáž kuchyňské linky, ideálně zkušenost.',
                ],
            ],
            'malovani' => [
                'titles' => [
                    'Malování kanceláře',
                    'Vymalování bytu 3+1',
                ],
                'descs' => [
                    'Materiál zajištěn, nutná čistá práce.',
                    'Možno po částech, dle domluvy.',
                ],
            ],
            'zahrada' => [
                'titles' => [
                    'Sekání trávy',
                    'Údržba zahrady – výpomoc',
                    'Stříhání keřů',
                ],
                'descs' => [
                    'Menší zahrada u RD, Praha-západ.',
                    'Sekání, hrabání, odvoz bio odpadu.',
                ],
            ],
            'sklad' => [
                'titles' => [
                    'Skladník – vykládka',
                    'Manipulační dělník',
                ],
                'descs' => [
                    'Vykládka kontejneru, jednorázově, hotově.',
                    'Naskladňování zboží, práce ve dvojici.',
                ],
            ],
            'gastro' => [
                'titles' => [
                    'Výpomoc do kuchyně',
                    'Servírka na víkend',
                ],
                'descs' => [
                    'Myčka, příprava zeleniny, 120–140 Kč/h.',
                    'Malá hospoda, směny dle domluvy.',
                ],
            ],
            'brigady' => [
                'titles' => [
                    'Jednoduchá brigáda – balení',
                    'Rozdávání letáků',
                ],
                'descs' => [
                    'Balení drobného zboží, vhodné i pro studenty.',
                    'Rozdávání letáků u metra, 3 hodiny.',
                ],
            ],
            'ostatni' => [
                'titles' => [
                    'Pomoc seniorovi',
                    'Přepis textů',
                ],
                'descs' => [
                    'Nákup, doprovod k lékaři, Praha 5.',
                    'Jednoduché přepisy do PC, možno z domu.',
                ],
            ],
        ];

        $cities = ['Praha', 'Brno', 'Ostrava', 'Plzeň', 'Kladno'];

        // берём все реальные категории из БД
        $categories = JobCategory::all();

        foreach ($categories as $cat) {
            $pack = $data[$cat->slug] ?? null;

            // если для этой категории в словаре нет данных — создадим хотя бы 3 универсальные
            if (!$pack) {
                for ($i = 1; $i <= 3; $i++) {
                    Job::create([
                        'job_category_id'  => $cat->id,
                        'city'             => $cities[array_rand($cities)],
                        'title'            => $cat->name . ' – brigáda #' . $i,
                        'description'      => 'Výpomoc v oboru: '.$cat->name.'. Vyplaceno po práci.',
                        'pay_type'         => 'per_hour',
                        'price'            => rand(140, 200),
                        'price_negotiable' => false,
                        'contact_name'     => 'Demo uživatel',
                        'phone'            => '+420777'.rand(100,999),
                        'email'            => 'demo+'.$cat->slug.$i.'@example.com',
                        'status'           => 'published',
                        'edit_token'       => Str::random(48),
                        'lang'             => 'cs',
                        'ip'               => '127.0.0.1',
                        'ua'               => 'realistic-seeder',
                    ]);
                }
                continue;
            }

            // есть данные по этой категории
            // создаём до 8 объявлений, чтобы не было однообразия
            for ($i = 1; $i <= 8; $i++) {
                $title = $pack['titles'][array_rand($pack['titles'])];
                $desc  = $pack['descs'][array_rand($pack['descs'])];

                Job::create([
                    'job_category_id'  => $cat->id,
                    'city'             => $cities[array_rand($cities)],
                    'title'            => $title,
                    'description'      => $desc,
                    'pay_type'         => 'per_hour',
                    'price'            => rand(140, 240),
                    'price_negotiable' => (bool)rand(0,1),
                    'contact_name'     => 'Demo uživatel',
                    'phone'            => '+420777'.rand(100,999),
                    'email'            => 'demo+'.$cat->slug.$i.'@example.com',
                    'status'           => 'published',
                    'edit_token'       => Str::random(48),
                    'lang'             => 'cs',
                    'ip'               => '127.0.0.1',
                    'ua'               => 'realistic-seeder',
                ]);
            }
        }
    }
}
