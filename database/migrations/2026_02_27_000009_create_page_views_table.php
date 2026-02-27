<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->string('path');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('referer')->nullable();
            $table->string('session_id')->nullable();
            $table->timestamp('viewed_at');

            $table->index('path');
            $table->index('viewed_at');
            $table->index(['path', 'viewed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_views');
    }
};
