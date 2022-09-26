<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('journals', function (Blueprint $table) {
            $table->string('code');
            $table->text('slug');
            $table->unsignedBigInteger('user_id');
            $table->string('title');
            $table->string('short_desc');
            $table->string('path');
            $table->boolean('is_published')
                ->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('journals');
    }
};
