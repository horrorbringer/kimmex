<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('org_units', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('type')->default('STAFF');
            $table->uuid('parentId')->nullable();
            $table->uuid('employeeId')->nullable();
            $table->uuid('departmentId')->nullable();
            $table->integer('orderIndex')->default(0);
            $table->timestamps();

            // Foreign keys usually go here via foreignUuid, but since old schema didn't enforce 
            // tight uuid constraints, index is enough, or we can use foreign() if we strictly want.
            $table->foreign('parentId')->references('id')->on('org_units')->nullOnDelete();
            $table->foreign('employeeId')->references('id')->on('employees')->nullOnDelete();
            $table->foreign('departmentId')->references('id')->on('departments')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('org_units');
    }
};
