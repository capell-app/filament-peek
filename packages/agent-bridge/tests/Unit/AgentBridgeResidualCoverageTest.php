<?php

declare(strict_types=1);

use Capell\Admin\Data\Bridges\AdminBridgeContextData;
use Capell\Admin\Support\Bridges\AdminBridgeRegistrar;
use Capell\AgentBridge\Bridges\AgentBridgeAdminBridge;
use Capell\AgentBridge\Filament\Pages\CapellAgentBridgePromptBuilderPage;
use Capell\AgentBridge\Filament\Resources\Users\RelationManagers\AgentBridgeAuditEntriesRelationManager;
use Capell\AgentBridge\Filament\Resources\Users\RelationManagers\AgentBridgeConfirmationsRelationManager;
use Capell\AgentBridge\Filament\Resources\Users\RelationManagers\AgentBridgeTokensRelationManager;
use Capell\AgentBridge\Tests\Fixtures\User;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

it('registers the agent bridge admin extension page and user schema extender', function (): void {
    $bridge = new AgentBridgeAdminBridge;
    $registrar = new AdminBridgeRegistrar;
    $context = new AdminBridgeContextData(
        packageName: 'capell-app/agent-bridge',
    );

    expect($bridge->isEnabled($context))->toBeTrue();

    $bridge->register($registrar, $context);

    expect(CapellAgentBridgePromptBuilderPage::shouldRegisterNavigation())->toBeFalse();
});

it('builds agent bridge user relation manager tables and titles', function (): void {
    $user = new User;

    expect(AgentBridgeTokensRelationManager::getTitle($user, 'edit'))->toBe(__('capell-agent-bridge::admin.tokens'))
        ->and(AgentBridgeConfirmationsRelationManager::getTitle($user, 'edit'))->toBe(__('capell-agent-bridge::admin.confirmations'))
        ->and(AgentBridgeAuditEntriesRelationManager::getTitle($user, 'edit'))->toBe(__('capell-agent-bridge::admin.audit_entries'))
        ->and((new AgentBridgeTokensRelationManager)->table(agentBridgeResidualTable())->getColumns())->not->toBeEmpty()
        ->and((new AgentBridgeConfirmationsRelationManager)->table(agentBridgeResidualTable())->getColumns())->not->toBeEmpty()
        ->and((new AgentBridgeAuditEntriesRelationManager)->table(agentBridgeResidualTable())->getColumns())->not->toBeEmpty();
});

function agentBridgeResidualTable(): Table
{
    $livewire = Mockery::mock(HasTable::class);
    $livewire->shouldReceive('makeFilamentTranslatableContentDriver')->andReturn(null);

    return Table::make($livewire);
}
