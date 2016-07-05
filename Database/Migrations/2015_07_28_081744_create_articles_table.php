<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function($table) {
            $table->increments('id');
            $table->string('title', 255)->index();
            $table->string('slug', 255)->unique()->index();
            $table->text('description');
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('published')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('articles');
    }
}
