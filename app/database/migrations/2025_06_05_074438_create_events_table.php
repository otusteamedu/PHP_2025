<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Events;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable(Events::TABLE_NAME)) {
            Schema::create(Events::TABLE_NAME, function (Blueprint $table) {
                $table->id();
                $table->integer('number')->unique();
                $table->boolean('is_done')->default(false);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(Events::TABLE_NAME);
    }
};
