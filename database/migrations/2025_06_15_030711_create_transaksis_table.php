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
        Schema::create('transaksis', function (Blueprint $table) {
            $table->uuid('kode')->primary();
            // $table->string('pembuat');
            $table->unsignedBigInteger('pembuat_id');
            // $table->string('pekerja');
            $table->unsignedBigInteger('pekerja_id');
            $table->integer('jumlah');
            // $table->enum('jenis', ['masuk', 'keluar']);
            $table->enum('status', ['tertunda', 'sukses', 'gagal'])->default('tertunda');
            $table->dateTimeTz('dibuat');
            $table->timestamps();

            $table->foreign('pembuat_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('pekerja_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
