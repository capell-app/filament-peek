<?php

declare(strict_types=1);

namespace Capell\Workspaces\Database\Factories;

use Capell\Workspaces\Enums\WorkspaceKindEnum;
use Capell\Workspaces\Enums\WorkspaceStatusEnum;
use Capell\Workspaces\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Workspace>
 */
class WorkspaceFactory extends Factory
{
    protected $model = Workspace::class;

    public function definition(): array
    {
        $name = fake()->sentence(3);

        return [
            'name' => $name,
            'description' => fake()->optional()->sentence(),
            'color' => fake()->optional()->hexColor(),
            'status' => WorkspaceStatusEnum::Open,
            'kind' => WorkspaceKindEnum::Manual,
        ];
    }
}
