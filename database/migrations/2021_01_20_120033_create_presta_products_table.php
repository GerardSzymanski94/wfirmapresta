<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrestaProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('presta_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('presta_config_id');
            $table->integer('presta_id');
            $table->text('name');
            $table->string('code');
            $table->integer('count');
            $table->integer('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('presta_products');
    }
}
