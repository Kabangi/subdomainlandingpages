<?php 

namespace Julius\Multidomain\Seeds;

use Seeder;
use System\Classes\PluginManager;

class JuliusMigrate extends Seeder
{
    public function run()
    {
        if (\Schema::hasTable('julius_multidomain_settings')) {
            $rows = \DB::table('julius_multidomain_settings')->get(
                [
                    'domain',
                    'page_url',
                    'is_protected',
                    'type',
                    'created_at',
                    'updated_at',
                ]
            );

            $data = [];
            foreach ($rows as $row) {
                $data[] = get_object_vars($row);
            }

            \DB::table('julius_multidomain_settings')->insert($data);

            if (PluginManager::instance()->exists('julius.Multidomain')) {
                \Artisan::call('plugin:remove', ['name' => 'julius.Multidomain', '--force' => true]);
            }

            \Schema::dropIfExists('julius_multisite_settings');
        }

    }
}