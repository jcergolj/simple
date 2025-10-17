<?php

namespace Tests\Unit\Models;

use App\Models\Client;
use App\Models\HourlyRate;
use App\Models\Project;
use App\Models\TimeEntry;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ModelCastsTest extends TestCase
{
    #[Test]
    public function user_model_casts_id_to_integer(): void
    {
        $user = new User;
        $casts = $user->getCasts();

        $this->assertArrayHasKey('id', $casts);
        $this->assertSame('int', $casts['id']);
    }

    #[Test]
    public function client_model_casts_id_to_integer(): void
    {
        $client = new Client;
        $casts = $client->getCasts();

        $this->assertArrayHasKey('id', $casts);
        $this->assertSame('int', $casts['id']);
    }

    #[Test]
    public function project_model_casts_id_and_foreign_keys_to_integer(): void
    {
        $project = new Project;
        $casts = $project->getCasts();

        // Primary key
        $this->assertArrayHasKey('id', $casts);
        $this->assertSame('int', $casts['id']);

        // Foreign key
        $this->assertArrayHasKey('client_id', $casts);
        $this->assertSame('integer', $casts['client_id']);
    }

    #[Test]
    public function time_entry_model_casts_id_and_foreign_keys_to_integer(): void
    {
        $timeEntry = new TimeEntry;
        $casts = $timeEntry->getCasts();

        // Primary key
        $this->assertArrayHasKey('id', $casts);
        $this->assertSame('int', $casts['id']);

        // Foreign keys
        $this->assertArrayHasKey('client_id', $casts);
        $this->assertSame('integer', $casts['client_id']);

        $this->assertArrayHasKey('project_id', $casts);
        $this->assertSame('integer', $casts['project_id']);
    }

    #[Test]
    public function hourly_rate_model_casts_id_and_foreign_keys_to_integer(): void
    {
        $hourlyRate = new HourlyRate;
        $casts = $hourlyRate->getCasts();

        // Primary key
        $this->assertArrayHasKey('id', $casts);
        $this->assertSame('int', $casts['id']);

        // Morph foreign key
        $this->assertArrayHasKey('rateable_id', $casts);
        $this->assertSame('integer', $casts['rateable_id']);

        // Other integer fields
        $this->assertArrayHasKey('amount', $casts);
        $this->assertSame('integer', $casts['amount']);
    }

    #[Test]
    public function all_models_cast_primary_keys_to_integer(): void
    {
        $models = [
            new User,
            new Client,
            new Project,
            new TimeEntry,
            new HourlyRate,
        ];

        foreach ($models as $model) {
            $casts = $model->getCasts();
            $this->assertArrayHasKey('id', $casts, 'Model '.$model::class.' should cast id to integer');
            $this->assertSame('int', $casts['id'], 'Model '.$model::class.' id should be cast as int');
        }
    }

    #[Test]
    public function project_model_has_required_foreign_key_casts(): void
    {
        $project = new Project;
        $requiredCasts = [
            'id' => 'int',
            'client_id' => 'integer',
        ];

        foreach ($requiredCasts as $field => $expectedCast) {
            $this->assertArrayHasKey($field, $project->getCasts(), "Project model should cast {$field}");
            $this->assertSame($expectedCast, $project->getCasts()[$field], "Project model {$field} should be cast as {$expectedCast}");
        }
    }

    #[Test]
    public function time_entry_model_has_required_foreign_key_casts(): void
    {
        $timeEntry = new TimeEntry;
        $requiredCasts = [
            'id' => 'int',
            'client_id' => 'integer',
            'project_id' => 'integer',
        ];

        foreach ($requiredCasts as $field => $expectedCast) {
            $this->assertArrayHasKey($field, $timeEntry->getCasts(), "TimeEntry model should cast {$field}");
            $this->assertSame($expectedCast, $timeEntry->getCasts()[$field], "TimeEntry model {$field} should be cast as {$expectedCast}");
        }
    }

    #[Test]
    public function hourly_rate_model_has_required_casts(): void
    {
        $hourlyRate = new HourlyRate;
        $requiredCasts = [
            'id' => 'int',
            'amount' => 'integer',
            'rateable_id' => 'integer',
        ];

        foreach ($requiredCasts as $field => $expectedCast) {
            $this->assertArrayHasKey($field, $hourlyRate->getCasts(), "HourlyRate model should cast {$field}");
            $this->assertSame($expectedCast, $hourlyRate->getCasts()[$field], "HourlyRate model {$field} should be cast as {$expectedCast}");
        }
    }

    #[Test]
    public function models_with_foreign_keys_cast_them_properly(): void
    {
        $modelForeignKeys = [
            Project::class => ['client_id'],
            TimeEntry::class => ['client_id', 'project_id'],
            HourlyRate::class => ['rateable_id'],
        ];

        foreach ($modelForeignKeys as $modelClass => $foreignKeys) {
            $model = new $modelClass;
            $casts = $model->getCasts();

            foreach ($foreignKeys as $foreignKey) {
                $this->assertArrayHasKey($foreignKey, $casts, "{$modelClass} should cast {$foreignKey}");
                $this->assertSame('integer', $casts[$foreignKey], "{$modelClass} {$foreignKey} should be cast as integer");
            }
        }
    }

    #[Test]
    public function models_cast_id_fields_consistently(): void
    {
        $models = [
            User::class,
            Client::class,
            Project::class,
            TimeEntry::class,
            HourlyRate::class,
        ];

        foreach ($models as $modelClass) {
            $model = new $modelClass;
            $casts = $model->getCasts();

            $this->assertArrayHasKey('id', $casts, "{$modelClass} should have id cast");
            $this->assertSame('int', $casts['id'], "{$modelClass} id should be cast as int (Laravel default)");
        }
    }

    #[Test]
    public function no_foreign_keys_are_missing_integer_casts(): void
    {
        // Check for common foreign key patterns
        $foreignKeyPatterns = ['_id'];
        $models = [
            new Project,
            new TimeEntry,
            new HourlyRate,
        ];

        foreach ($models as $model) {
            $fillable = $model->getFillable();
            $casts = $model->getCasts();

            foreach ($fillable as $field) {
                foreach ($foreignKeyPatterns as $pattern) {
                    if (str_ends_with($field, $pattern)) {
                        $this->assertArrayHasKey($field, $casts,
                            $model::class." should cast foreign key field '{$field}' to integer");
                        $this->assertSame('integer', $casts[$field],
                            $model::class." foreign key field '{$field}' should be cast as 'integer'");
                    }
                }
            }
        }
    }
}
