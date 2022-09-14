<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('console_tokens', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained(app(config('legit-artisan-commands.relationships.user'))->getTable())->onDelete('cascade');
            $table->string('value')->index();
            $table->dateTime('expires_at');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('console_tokens');
    }
};
