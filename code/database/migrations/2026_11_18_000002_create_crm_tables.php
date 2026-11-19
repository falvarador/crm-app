<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120)->index();
            $table->string('tax_id', 30)->nullable()->unique();
            $table->string('tier', 60)->nullable();
            $table->string('status', 40)->default('Prospecto')->index();
            $table->string('sector', 60)->nullable()->index();
            $table->string('industry', 100)->nullable()->index();
            $table->string('location', 120)->nullable();
            $table->text('address')->nullable();
            $table->string('website', 120)->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('email', 120)->nullable()->index();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('mrr', 12, 2)->default(0);
            $table->string('mrr_plan', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['status', 'sector']);
            $table->index(['owner_id', 'status']);
        });

        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->string('first_name', 60);
            $table->string('last_name', 60);
            $table->string('email', 120)->unique();
            $table->string('phone', 40)->nullable();
            $table->string('position', 100)->nullable();
            $table->string('lead_source', 60)->nullable()->index();
            $table->string('status', 40)->default('Nuevo Lead')->index();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('role_tag', 60)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['company_id', 'status']);
            $table->index(['owner_id', 'status']);
            $table->index(['last_name', 'first_name']);
        });

        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->string('title', 120)->index();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('value', 14, 2);
            $table->char('currency', 3)->default('USD');
            $table->string('stage', 30)->default('prospecto')->index();
            $table->date('expected_close_date')->nullable()->index();
            $table->unsignedTinyInteger('probability')->default(0);
            $table->string('badge', 80)->nullable();
            $table->string('badge_variant', 30)->nullable();
            $table->string('alert', 160)->nullable();
            $table->boolean('critical')->default(false);
            $table->string('lost_reason', 160)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['stage', 'owner_id']);
            $table->index(['company_id', 'stage']);
        });

        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('type', 30)->index();
            $table->string('subject', 160)->index();
            $table->text('description')->nullable();
            $table->dateTime('due_date')->nullable()->index();
            $table->dateTime('completed_at')->nullable()->index();
            $table->string('schedule_label', 60)->nullable();
            $table->string('priority', 40)->default('Normal');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->nullableMorphs('activitable');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['user_id', 'completed_at', 'due_date'], 'activities_user_task_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
        Schema::dropIfExists('deals');
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('companies');
    }
};
