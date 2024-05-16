<?php

use App\Models\Task;
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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->text('title');
            $table->longText('content');
            $table->char('status')->default(Task::STATUS['to_do']); // 0=to-do, 1=in-progress, 2=done
            $table->longText('files')->nullable();
            $table->boolean('is_published')->default(true); // true=published, false=draft
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
