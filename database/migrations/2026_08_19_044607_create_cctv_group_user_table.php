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
        Schema::create('cctv_group_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cctv_group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // Drop the single cctv_group_id from users since we now use many-to-many
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'cctv_group_id')) {
                $table->dropForeign(['cctv_group_id']);
                $table->dropColumn('cctv_group_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cctv_group_user');
    }
};
