<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tbl_mapping', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('user_id'); // Foreign key untuk user
            $table->unsignedBigInteger('modul_id'); // Foreign key untuk modul
            $table->string('periode')->nullable(); // Kolom untuk periode (misal: yearly, monthly)
            $table->timestamps(); // Kolom created_at dan updated_at

            // Menambahkan foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('modul_id')->references('modul_id')->on('tbl_modul')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_mapping', function (Blueprint $table) {
            // Menghapus foreign key sebelum menghapus tabel
            $table->dropForeign(['user_id']);
            $table->dropForeign(['modul_id']);
            $table->string('effective')->default(false); // Kolom untuk effective
            $table->string('simple_interest')->default(false); // Kolom untuk simple interest
        });

        Schema::dropIfExists('tbl_mapping');
    }
};
