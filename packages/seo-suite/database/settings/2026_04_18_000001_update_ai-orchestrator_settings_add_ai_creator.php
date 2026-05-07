<?php

declare(strict_types=1);

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        if (! $this->migrator->exists('ai-orchestrator.ai_creator')) {
            $this->migrator->add('ai-orchestrator.ai_creator', true);
        }

        if (! $this->migrator->exists('ai-orchestrator.ai_provider')) {
            $this->migrator->add('ai-orchestrator.ai_provider', 'openai');
        }

        if (! $this->migrator->exists('ai-orchestrator.ai_model')) {
            $this->migrator->add('ai-orchestrator.ai_model', 'gpt-4o');
        }

        if (! $this->migrator->exists('ai-orchestrator.ai_api_key')) {
            $this->migrator->add('ai-orchestrator.ai_api_key', '');
        }

        if (! $this->migrator->exists('ai-orchestrator.image_provider')) {
            $this->migrator->add('ai-orchestrator.image_provider', 'openai');
        }

        if (! $this->migrator->exists('ai-orchestrator.image_model')) {
            $this->migrator->add('ai-orchestrator.image_model', 'dall-e-3');
        }

        if (! $this->migrator->exists('ai-orchestrator.image_default_size')) {
            $this->migrator->add('ai-orchestrator.image_default_size', '1024x1024');
        }
    }

    public function down(): void
    {
        $this->migrator->delete('ai-orchestrator.ai_creator');
        $this->migrator->delete('ai-orchestrator.ai_provider');
        $this->migrator->delete('ai-orchestrator.ai_model');
        $this->migrator->delete('ai-orchestrator.ai_api_key');
        $this->migrator->delete('ai-orchestrator.image_provider');
        $this->migrator->delete('ai-orchestrator.image_model');
        $this->migrator->delete('ai-orchestrator.image_default_size');
    }
};
