<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('perpustakaans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigIntegera('user_id');
            $table->unsignedBigInteger('buku_id');
            $table->timestamps();

            // Pastikan foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('buku_id')->references('id')->on('bukus')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('perpustakaans', function (Blueprint $table) {
            if (Schema::hasColumn('perpustakaans', 'buku_id')) {
                $table->dropColumn('buku_id');
            }
        });
    }
};
