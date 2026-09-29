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
        Schema::create('cats', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('gender', 10);
            $table->unsignedInteger('age');
            $table->foreignId('mother_id')
                ->nullable()
                ->constrained('cats')
                ->nullOnDelete();
            $table->timestamps();

            $table->index(['gender', 'age']);
        });

        Schema::create('cat_fathers', function (Blueprint $table) {
            $table->foreignId('cat_id')
                ->constrained('cats')
                ->cascadeOnDelete();
            $table->foreignId('father_id')
                ->constrained('cats')
                ->cascadeOnDelete();
            $table->timestamps();

            $table->primary(['cat_id', 'father_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cat_fathers');
        Schema::dropIfExists('cats');
    }
};
