<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('province', 100);
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->unsignedInteger('elevation')->default(0);
            $table->unsignedInteger('capacity')->default(0);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('floras', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('season', 40);
            $table->string('icon', 12)->default('✦');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('flora_region', function (Blueprint $table) {
            $table->foreignId('region_id')->constrained()->cascadeOnDelete();
            $table->foreignId('flora_id')->constrained()->cascadeOnDelete();
            $table->primary(['region_id', 'flora_id']);
        });

        Schema::create('hives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('region_id')->constrained()->cascadeOnDelete();
            $table->string('aks_number', 60);
            $table->unsignedInteger('hive_count');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 40);
            $table->string('severity', 20)->default('warning');
            $table->string('title');
            $table->text('message');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alerts');
        Schema::dropIfExists('hives');
        Schema::dropIfExists('flora_region');
        Schema::dropIfExists('floras');
        Schema::dropIfExists('regions');
    }
};
