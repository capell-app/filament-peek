<?php

declare(strict_types=1);

namespace Capell\Workspaces\Database\Factories;

use Capell\Workspaces\Enums\WorkspaceApprovalActionEnum;
use Capell\Workspaces\Models\WorkspaceApproval;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkspaceApproval>
 */
class WorkspaceApprovalFactory extends Factory
{
    protected $model = WorkspaceApproval::class;

    public function definition(): array
    {
        return [
            'workspace_id' => WorkspaceFactory::new(),
            'level' => 1,
            'action' => WorkspaceApprovalActionEnum::Submitted,
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
