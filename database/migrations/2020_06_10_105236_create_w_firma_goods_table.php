<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWFirmaGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('w_firma_goods', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('good_id');
            $table->integer('w_firma_config_id');
            $table->string('name')->nullable();
            $table->string('count')->nullable();
            $table->string('tags')->nullable();
            $table->string('documents')->nullable();
            $table->string('notes')->nullable();
            $table->string('description')->nullable();
            $table->string('discount')->nullable();
            $table->string('classification')->nullable();
            $table->string('lumpcode')->nullable();
            $table->string('brutto')->nullable();
            $table->string('netto')->nullable();
            $table->string('unit')->nullable();
            $table->string('code')->nullable();
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
        Schema::dropIfExists('w_firma_goods');
    }
}
