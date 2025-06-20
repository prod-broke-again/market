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
        Schema::table('users_infos', function (Blueprint $table) {
            // Добавляем поле для связи с адресом
            $table->foreignId('address_id')->nullable()->constrained('addresses')->onDelete('set null');

            // Добавляем уникальный индекс на user_id
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users_infos', function (Blueprint $table) {
            $table->dropForeign(['address_id']);
            $table->dropColumn('address_id');
            $table->dropUnique(['user_id']);
        });
    }
};
