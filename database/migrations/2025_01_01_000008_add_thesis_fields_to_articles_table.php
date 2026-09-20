<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->string('author_name')->nullable()->after('title');
            $table->string('institution')->nullable()->after('author_name'); // universidade
            $table->string('course')->nullable()->after('institution');
            $table->string('academic_level')->nullable()->after('course'); // Licenciatura | Mestrado | Doutoramento
            $table->unsignedSmallInteger('completion_year')->nullable()->after('academic_level');
            $table->string('country')->nullable()->after('completion_year');
            $table->string('document_path')->nullable()->after('image');
            $table->string('document_original_name')->nullable()->after('document_path');
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn([
                'author_name', 'institution', 'course', 'academic_level',
                'completion_year', 'country', 'document_path', 'document_original_name',
            ]);
        });
    }
};
