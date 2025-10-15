<?php

namespace App\Console\Commands;

use App\Services\FigmaClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class FigmaExportCommand extends Command
{
    protected $signature = 'figma:export {fileKey} {nodeId} {--format=} {--scale=2} {--out=resources/figma} {--token=} {--verify=}';

    protected $description = 'Экспортирует указанный узел (frame) из Figma и его дочерние как изображения в resources.';

    public function handle(): int
    {
        $fileKey = (string) $this->argument('fileKey');
        $nodeId = (string) $this->argument('nodeId');
        // Нормализуем node-id из URL (2035-1189 -> 2035:1189)
        if (preg_match('~^\d+-\d+$~', $nodeId)) {
            $nodeId = str_replace('-', ':', $nodeId);
        }
        $format = (string) $this->option('format');
        $scale = (float) $this->option('scale');
        $outDir = (string) $this->option('out');

        $overrideToken = $this->option('token');
        $verifyOpt = $this->option('verify');
        if ($verifyOpt !== null) {
            $verifyBool = filter_var($verifyOpt, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($verifyBool !== null) {
                config(['services.figma.verify' => $verifyBool]);
            }
        }
        $client = $overrideToken ? new FigmaClient((string)$overrideToken) : FigmaClient::fromConfig();

        $this->info("Сбор узлов из Figma: file={$fileKey}, root={$nodeId}");
        try {
            $nodeMeta = $client->collectDescendantNodeMeta($fileKey, $nodeId);
        } catch (\Throwable $e) {
            $this->error('Ошибка запроса к Figma: ' . $e->getMessage());
            return self::FAILURE;
        }
        if (empty($nodeMeta)) {
            $this->warn('Не удалось получить узлы. Проверьте fileKey и nodeId.');
            return self::FAILURE;
        }

        $nodeIds = array_keys($nodeMeta);
        $this->info('Всего узлов: ' . count($nodeIds));

        // Если формат не указан, экспортируем векторные узлы как SVG, остальные как PNG
        $images = [];
        if ($format) {
            $images = $client->getImages($fileKey, $nodeIds, $format, $scale);
        } else {
            $vectorLike = ['VECTOR', 'LINE', 'POLYGON', 'STAR', 'ELLIPSE', 'REGULAR_POLYGON', 'BOOLEAN_OPERATION'];
            $textLike = ['TEXT'];
            $frameLike = ['FRAME', 'GROUP', 'COMPONENT', 'INSTANCE', 'SECTION', 'AUTO_LAYOUT_FRAME'];

            $svgIds = [];
            $pngIds = [];
            foreach ($nodeMeta as $id => $meta) {
                $type = strtoupper((string) ($meta['type'] ?? ''));
                if (in_array($type, $vectorLike, true)) {
                    $svgIds[] = $id;
                } elseif (in_array($type, $textLike, true)) {
                    // Текст лучше растеризовать с макетом
                    $pngIds[] = $id;
                } else {
                    $pngIds[] = $id;
                }
            }
            if ($svgIds) {
                $images = $images + $client->getImages($fileKey, $svgIds, 'svg', 1.0);
            }
            if ($pngIds) {
                $images = $images + $client->getImages($fileKey, $pngIds, 'png', $scale);
            }
        }
        if (empty($images)) {
            $this->warn('Не удалось получить ссылки на изображения.');
            return self::FAILURE;
        }

        $outDir = rtrim($outDir, '/');
        File::ensureDirectoryExists($outDir);

        $manifest = [];

        foreach ($images as $id => $url) {
            $name = (string) ($nodeMeta[$id]['name'] ?? $id);
            $safe = preg_replace('~[^a-zA-Z0-9-_]+~', '-', $name) ?: $id;
            $ext = str_contains($url, '.svg') || (isset($nodeMeta[$id]['type']) && strtoupper((string)$nodeMeta[$id]['type']) !== '' && !isset($format)) ? (str_contains($url, 'format=svg') ? 'svg' : pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION)) : ($format ?: 'png');
            $filePath = $outDir . '/' . $safe . '.' . $ext;

            $this->line("Скачиваю: {$name} -> {$filePath}");
            $resp = Http::withOptions(['verify' => config('services.figma.verify', true)])->get($url);
            if (!$resp->successful()) {
                $this->warn("Пропуск: {$name}");
                continue;
            }
            File::put($filePath, $resp->body());
            $manifest[] = [
                'id' => $id,
                'name' => $name,
                'file' => $filePath,
                'format' => $ext,
                'scale' => $scale,
            ];
        }

        $manifestPath = $outDir . '/manifest.json';
        File::put($manifestPath, json_encode($manifest, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        $this->info('Готово. Manifest: ' . $manifestPath);

        return self::SUCCESS;
    }
}


