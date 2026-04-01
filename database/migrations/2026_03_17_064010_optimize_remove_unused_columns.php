<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn(['ward', 'district', 'city']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('avatar');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['image', 'description']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }

    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->string('ward', 100)->nullable()->after('address_line');
            $table->string('district', 100)->nullable()->after('ward');
            $table->string('city', 100)->nullable()->after('district');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('phone');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->text('description')->nullable()->after('slug');
            $table->string('image')->nullable()->after('description');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('image')->nullable()->after('description');
        });
    }
};
