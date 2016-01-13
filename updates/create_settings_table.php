<?php 
namespace Julius\Multidomain\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateSettingsTable extends Migration
{

    public function up()
    {
        Schema::create('julius_multidomain_settings', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->text('domain');
            $table->text('page_url');
            $table->text('type');
            $table->boolean('is_protected')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('julius_multidomain_settings');
    }

}
