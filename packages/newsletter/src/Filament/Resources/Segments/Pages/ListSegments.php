<?php

declare(strict_types=1);

namespace Capell\Newsletter\Filament\Resources\Segments\Pages;

use Capell\Newsletter\Actions\ExportSubscribersAction;
use Capell\Newsletter\Filament\Resources\Segments\SegmentResource;
use Capell\Newsletter\Models\Segment;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListSegments extends ListRecords
{
    protected static string $resource = SegmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('export_segment')
                ->label(__('capell-newsletter::actions.export_subscribers'))
                ->form([
                    Select::make('segment_id')
                        ->label(__('capell-newsletter::navigation.segments'))
                        ->options(fn (): array => Segment::query()->pluck('name', 'id')->all())
                        ->required(),
                ])
                ->action(function (array $data): StreamedResponse {
                    $segment = Segment::query()->findOrFail((int) $data['segment_id']);

                    return $this->exportSegment($segment);
                }),
        ];
    }

    private function exportSegment(Segment $segment): StreamedResponse
    {
        return response()->streamDownload(function () use ($segment): void {
            $output = fopen('php://output', 'w');

            if ($output === false) {
                return;
            }

            fputcsv($output, ['email', 'first_name', 'last_name', 'status', 'subscribed_at', 'unsubscribed_at']);

            ExportSubscribersAction::run($segment->site_id, $segment)
                ->each(static function (array $row) use ($output): void {
                    fputcsv($output, $row);
                });
        }, 'newsletter-segment-' . $segment->handle . '.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}
