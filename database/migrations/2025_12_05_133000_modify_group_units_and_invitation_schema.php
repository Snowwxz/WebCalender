<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop foreign key on invitation.id_group if it exists (from older migration)
        if (Schema::hasTable('invitation')) {
            try {
                Schema::table('invitation', function (Blueprint $table) {
                    $table->dropForeign(['id_group']);
                });
            } catch (\Throwable $e) {
                // FK might not exist; ignore
            }
        }

        // Modify group_units: ensure a dedicated primary key `id` and make `id_group` a plain bigint
        if (Schema::hasTable('group_units')) {
            // Attempt to drop PRIMARY KEY using both syntaxes for compatibility
            try {
                DB::statement('ALTER TABLE `group_units` DROP PRIMARY KEY');
            } catch (\Throwable $e) {
                try {
                    DB::statement('ALTER TABLE `group_units` DROP INDEX `PRIMARY`');
                } catch (\Throwable $e2) {
                    // Ignore if there's no primary key
                }
            }

            // Ensure id_group is not auto-increment and is NOT NULL
            try {
                DB::statement('ALTER TABLE `group_units` MODIFY `id_group` BIGINT UNSIGNED NOT NULL');
            } catch (\Throwable $e) {
                // Column may already have correct type; ignore
            }

            // Add dedicated auto-increment primary key column `id` using raw SQL to avoid PK conflicts
            if (!Schema::hasColumn('group_units', 'id')) {
                try {
                    DB::statement('ALTER TABLE `group_units` ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST');
                } catch (\Throwable $e) {
                    // If adding with PRIMARY fails, try add column first then set as PK
                    try {
                        DB::statement('ALTER TABLE `group_units` ADD COLUMN `id` BIGINT UNSIGNED NOT NULL FIRST');
                        DB::statement('ALTER TABLE `group_units` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
                        DB::statement('ALTER TABLE `group_units` ADD PRIMARY KEY (`id`)');
                    } catch (\Throwable $e2) {
                        throw $e2; // bubble up if still failing
                    }
                }
            }

            // Add indexes/unique constraints for better integrity & performance
            Schema::table('group_units', function (Blueprint $table) {
                // Index id_group for fast lookup of a group’s members
                $table->index('id_group');
                // Prevent duplicate unit entries within the same group
                $table->unique(['id_group', 'id_unit']);
            });
        }

        // Invitation table: ensure id_group has an index (not FK)
        if (Schema::hasTable('invitation')) {
            Schema::table('invitation', function (Blueprint $table) {
                $table->unsignedBigInteger('id_group')->nullable(false)->change();
                $table->index('id_group');
            });
        }
    }

    public function down(): void
    {
        // Reverse changes cautiously: drop unique and index; keep data intact
        if (Schema::hasTable('group_units')) {
            Schema::table('group_units', function (Blueprint $table) {
                // Drop constraints added in up()
                $table->dropUnique(['id_group', 'id_unit']);
                $table->dropIndex(['id_group']);
            });

            // We won't revert back to id_group as PRIMARY to avoid data loss.
            // The down migration intentionally keeps `id` as the primary key.
        }

        if (Schema::hasTable('invitation')) {
            Schema::table('invitation', function (Blueprint $table) {
                // Drop index on id_group
                $table->dropIndex(['id_group']);
            });

            // Optionally, you could re-add the foreign key here, but we avoid it
            // because `id_group` in group_units is not a unique identifier.
        }
    }
};
