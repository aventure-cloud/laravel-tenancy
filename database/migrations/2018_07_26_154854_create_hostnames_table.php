<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHostnamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hostnames', function (Blueprint $table) {
            $table->increments('id');
            $table->string('fqdn')->unique();
            $table->integer(config('multitenancy.tenant.foreign_key'))->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Add tenant foreign key with auto-delete
            $tenant_class = config('multitenancy.tenant.model');

            $table->foreign(config('multitenancy.tenant.foreign_key'))
                ->references('id')
                ->on((new $tenant_class)->getTable())
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hostnames', function (Blueprint $table){
            $table->dropForeign('hostnames_'.config('multitenancy.tenant.foreign_key').'_foreign');
        });
        Schema::dropIfExists('hostnames');
    }
}
