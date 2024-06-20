<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateNameFieldInProcessesTable extends Migration
{
    public function up()
    {
        Schema::table('processes', function (Blueprint $table) {
            // Добавление уникального индекса к полю name
            $table->string('name')->unique()->change();
        });
    }

    public function down()
    {
        Schema::table('processes', function (Blueprint $table) {
            // Удаление уникального индекса. Имя индекса формируется Laravel автоматически.
            $table->dropUnique(['name']);
            // Восстановление поля name в исходное состояние
            $table->string('name')->change();
        });
    }
}
