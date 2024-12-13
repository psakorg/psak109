<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        // Membuat tabel users
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Primary key 'id' dengan auto-increment
            $table->string('name'); // Nama pengguna
            $table->string('nama_pt'); // Nama Perusahaan
            $table->string('alamat_pt'); // Alamat Perusahaan
            $table->string('company_type'); // Jenis perusahaan
            $table->string('nomor_wa'); // Nomor WhatsApp, bisa kosong
            $table->string('email')->unique(); // Email yang unik
            $table->string('role')->default('admin'); // Default role adalah admin
            $table->string('password'); // Password
            $table->boolean('is_activated')->default(true); // Status aktivasi default ke true
            $table->timestamp('email_verified_at')->nullable(); // Waktu verifikasi email
            $table->rememberToken(); // Token untuk mengingat pengguna
            $table->timestamps(); // Menambahkan created_at dan updated_at
        });

        // Membuat tabel password_reset_tokens
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary(); // Primary key untuk email
            $table->string('token'); // Token untuk reset password
            $table->timestamp('created_at')->nullable(); // Waktu saat token dibuat
        });

        // Membuat tabel sessions
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary(); // Primary key untuk session ID
            $table->foreignId('user_id')->nullable()->index(); // Foreign key ke tabel users
            $table->string('ip_address', 45)->nullable(); // Alamat IP (IPv4 atau IPv6)
            $table->text('user_agent')->nullable(); // String user agent
            $table->longText('payload'); // Payload session
            $table->integer('last_activity')->index(); // Timestamp aktivitas terakhir

            // Menambahkan foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Membalik migrasi.
     */
    public function down(): void
    {
        // Menghapus foreign key sebelum menghapus tabel
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropForeign(['user_id']); // Menghapus foreign key untuk user_id
        });

        // Menghapus tabel
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
