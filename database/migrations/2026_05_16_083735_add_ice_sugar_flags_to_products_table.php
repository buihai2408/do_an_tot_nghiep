<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    


    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('has_ice_level')->default(true)->after('is_featured');
            $table->boolean('has_sugar_level')->default(true)->after('has_ice_level');
        });
    }

    


    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['has_ice_level', 'has_sugar_level']);
        });
    }
};
