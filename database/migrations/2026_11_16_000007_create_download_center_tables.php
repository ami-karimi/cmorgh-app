<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('download_categories', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->string('slug')->unique(); $table->text('description')->nullable();
            $table->string('icon')->nullable(); $table->boolean('is_active')->default(true); $table->unsignedInteger('sort_order')->default(0); $table->timestamps();
        });
        Schema::create('download_items', function (Blueprint $table) {
            $table->id(); $table->foreignId('category_id')->constrained('download_categories')->cascadeOnDelete();
            $table->string('title'); $table->string('slug')->unique(); $table->string('short_description', 500)->nullable(); $table->longText('description')->nullable();
            $table->string('file_path'); $table->string('file_name'); $table->string('file_mime')->nullable(); $table->unsignedBigInteger('file_size')->default(0);
            $table->string('cover_path')->nullable(); $table->string('version')->nullable(); $table->string('platform')->nullable();
            $table->unsignedBigInteger('downloads')->default(0); $table->boolean('is_featured')->default(false); $table->boolean('is_published')->default(true); $table->timestamps();
            $table->index(['category_id','is_published']); $table->index(['is_featured','is_published']);
        });
    }
    public function down(): void { Schema::dropIfExists('download_items'); Schema::dropIfExists('download_categories'); }
};
