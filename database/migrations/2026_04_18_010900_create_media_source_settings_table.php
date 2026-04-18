<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_source_settings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('media_source_id')->constrained()->cascadeOnDelete();
            $table->string('key');
            $table->text('value')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['media_source_id', 'sort_order']);
            $table->unique(['media_source_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_source_settings');
    }
};
