<?php

declare(strict_types=1);

namespace Capell\DeveloperTools\Palette;

use Capell\Admin\Contracts\Palette\PaletteCommandProvider;
use Capell\Admin\Data\PaletteCommandData;
use Capell\Admin\Data\PaletteCommandParameterData;
use Capell\Admin\Enums\PaletteCommandDanger;
use Capell\Admin\Enums\PaletteCommandParameterType;
use Capell\Admin\Enums\PaletteCommandType;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;

final class CapellArtisanPaletteCommandProvider implements PaletteCommandProvider
{
    /**
     * @return array<string, PaletteCommandData>
     */
    public function paletteCommands(): array
    {
        $commands = [];

        foreach (Artisan::all() as $name => $consoleCommand) {
            if (! str_starts_with($name, 'capell:')) {
                continue;
            }

            $command = new PaletteCommandData(
                id: "artisan.{$name}",
                label: Str::headline(str_replace(['capell:', '-'], ['', ' '], $name)),
                description: $consoleCommand->getDescription() ?: null,
                type: PaletteCommandType::Artisan,
                ability: 'palette.run.' . str_replace([':', '-'], '_', $name),
                command: $name,
                danger: $this->dangerForCommand($name),
                requiresConfirmation: $this->dangerForCommand($name) !== PaletteCommandDanger::Safe,
                parameters: $this->parametersForCommand($consoleCommand),
                group: 'Developer tools',
                sort: 80,
                keywords: [$name],
            );

            $commands[$command->id] = $command;
        }

        return $commands;
    }

    private function dangerForCommand(string $name): PaletteCommandDanger
    {
        if (Str::contains($name, ['demo', 'install', 'setup', 'upgrade'])) {
            return PaletteCommandDanger::Dangerous;
        }

        if (Str::contains($name, ['clear', 'cache', 'publish'])) {
            return PaletteCommandDanger::Confirm;
        }

        return PaletteCommandDanger::Safe;
    }

    /**
     * @return array<int, PaletteCommandParameterData>
     */
    private function parametersForCommand(Command $command): array
    {
        $parameters = [];
        $definition = $command->getDefinition();

        foreach ($definition->getArguments() as $argument) {
            $parameters[] = new PaletteCommandParameterData(
                name: $argument->getName(),
                label: Str::headline($argument->getName()),
                type: PaletteCommandParameterType::String,
                required: $argument->isRequired(),
                description: $argument->getDescription() ?: null,
                default: $argument->getDefault(),
            );
        }

        foreach ($definition->getOptions() as $option) {
            if ($this->isGlobalOption($option)) {
                continue;
            }

            $parameters[] = new PaletteCommandParameterData(
                name: '--' . $option->getName(),
                label: Str::headline($option->getName()),
                type: $option->acceptValue() ? PaletteCommandParameterType::String : PaletteCommandParameterType::Boolean,
                required: $option->isValueRequired(),
                description: $option->getDescription() ?: null,
                default: $this->defaultForOption($option),
            );
        }

        return $parameters;
    }

    private function defaultForOption(InputOption $option): mixed
    {
        if (! $option->acceptValue()) {
            return false;
        }

        return $option->getDefault();
    }

    private function isGlobalOption(InputOption $option): bool
    {
        return in_array($option->getName(), [
            'help',
            'quiet',
            'verbose',
            'version',
            'ansi',
            'no-ansi',
            'no-interaction',
            'env',
        ], true);
    }
}
