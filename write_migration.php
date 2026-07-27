<?php

$content = <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table("teaching_assignments", function (Blueprint $table) {
            $table->dropForeign(["teacher_id"]);
            $table->unsignedBigInteger("teacher_id")->nullable()->change();
            $table->foreign("teacher_id")->references("id")->on("users")->onDelete("set null");
        });

        Schema::table("attendance", function (Blueprint $table) {
            $table->dropForeign(["recorded_by"]);
            $table->unsignedBigInteger("recorded_by")->nullable()->change();
            $table->foreign("recorded_by")->references("id")->on("users")->onDelete("set null");
        });

        Schema::table("teacher_notes", function (Blueprint $table) {
            $table->dropForeign(["author_id"]);
            $table->unsignedBigInteger("author_id")->nullable()->change();
            $table->foreign("author_id")->references("id")->on("users")->onDelete("set null");
        });

        Schema::table("submission_grades", function (Blueprint $table) {
            $table->dropForeign(["graded_by"]);
            $table->unsignedBigInteger("graded_by")->nullable()->change();
            $table->foreign("graded_by")->references("id")->on("users")->onDelete("set null");
        });
    }

    public function down(): void
    {
        Schema::table("teaching_assignments", function (Blueprint $table) {
            $table->dropForeign(["teacher_id"]);
            $table->unsignedBigInteger("teacher_id")->nullable(false)->change();
            $table->foreign("teacher_id")->references("id")->on("users");
        });

        Schema::table("attendance", function (Blueprint $table) {
            $table->dropForeign(["recorded_by"]);
            $table->unsignedBigInteger("recorded_by")->nullable(false)->change();
            $table->foreign("recorded_by")->references("id")->on("users");
        });

        Schema::table("teacher_notes", function (Blueprint $table) {
            $table->dropForeign(["author_id"]);
            $table->unsignedBigInteger("author_id")->nullable(false)->change();
            $table->foreign("author_id")->references("id")->on("users");
        });

        Schema::table("submission_grades", function (Blueprint $table) {
            $table->dropForeign(["graded_by"]);
            $table->unsignedBigInteger("graded_by")->nullable(false)->change();
            $table->foreign("graded_by")->references("id")->on("users");
        });
    }
};
PHP;

file_put_contents('database/migrations/2026_07_27_165740_fix_users_foreign_keys_on_delete.php', $content);
echo "Done\n";