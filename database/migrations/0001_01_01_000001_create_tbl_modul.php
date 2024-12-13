<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblModul extends Migration // Pastikan nama kelas ini unik
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_modul', function (Blueprint $table) {
            $table->bigIncrements('modul_id'); // Primary key with auto-increment
            $table->string('nama_modul', 255); // Column for the module name with max length
            $table->string('effective')->default(false); // Kolom untuk effective
            $table->string('simple_interest')->default(false); // Kolom untuk simple interest
            $table->timestamps(); // Adds created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_modul');
    }
}
