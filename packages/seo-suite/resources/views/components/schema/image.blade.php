<?php
use Capell\Frontend\Facades\Frontend;
use Capell\SeoSuite\Actions\BuildPageImageSchemaAction;

$page = Frontend::page();
$json = BuildPageImageSchemaAction::run($page);

$jsonFlags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;

?>

@if ($json !== [])
    {!! '<script type="application/ld+json">' . json_encode($json, $jsonFlags) . '</script>' !!}
@endif
