<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class FigmaClient
{
    private string $apiBase = 'https://api.figma.com/v1';

    public function __construct(private readonly string $token)
    {
    }

    public static function fromConfig(): self
    {
        $token = (string) config('services.figma.token', '');
        if ($token === '') {
            throw new \RuntimeException('FIGMA_TOKEN не задан в окружении или config/services.php');
        }
        return new self($token);
    }

    private function http()
    {
        $verify = config('services.figma.verify', true);
        return Http::withOptions(['verify' => $verify])->withToken($this->token);
    }

    /**
     * Получить данные по указанным узлам (node ids) файла.
     * @param string $fileKey
     * @param array<int,string> $nodeIds
     * @return array<string,mixed>
     */
    public function getNodes(string $fileKey, array $nodeIds): array
    {
        $ids = implode(',', $nodeIds);
        $url = $this->apiBase . "/files/{$fileKey}/nodes?ids={$ids}";
        $resp = $this->http()->get($url)->throw();
        return $resp->json('nodes', []);
    }

    /**
     * Рекурсивно собрать все дочерние узлы под корневым nodeId.
     * Возвращает массив [nodeId => name].
     */
    public function collectDescendantNodeIds(string $fileKey, string $rootNodeId): array
    {
        $nodes = $this->getNodes($fileKey, [$rootNodeId]);
        $root = $nodes[$rootNodeId]['document'] ?? null;
        if (!$root) {
            return [];
        }
        $result = [];
        $stack = [$root];
        while ($stack) {
            $curr = array_pop($stack);
            $id = $curr['id'] ?? null;
            $name = $curr['name'] ?? '';
            if ($id) {
                $result[$id] = $name;
            }
            $children = $curr['children'] ?? [];
            foreach ($children as $child) {
                $stack[] = $child;
            }
        }
        return $result;
    }

    /**
     * Рекурсивно собрать метаданные узлов: [nodeId => ['name' => string, 'type' => string]].
     */
    public function collectDescendantNodeMeta(string $fileKey, string $rootNodeId): array
    {
        $nodes = $this->getNodes($fileKey, [$rootNodeId]);
        $root = $nodes[$rootNodeId]['document'] ?? null;
        if (!$root) {
            return [];
        }
        $result = [];
        $stack = [$root];
        while ($stack) {
            $curr = array_pop($stack);
            $id = $curr['id'] ?? null;
            if ($id) {
                $result[$id] = [
                    'name' => (string) ($curr['name'] ?? ''),
                    'type' => (string) ($curr['type'] ?? ''),
                ];
            }
            $children = $curr['children'] ?? [];
            foreach ($children as $child) {
                $stack[] = $child;
            }
        }
        return $result;
    }

    /**
     * Получить ссылки на рендер изображений узлов.
     * @param string $fileKey
     * @param array<int,string> $nodeIds
     * @param string $format png|svg|jpg
     * @param float $scale Например 1, 2
     * @return array<string,string> [nodeId => url]
     */
    public function getImages(string $fileKey, array $nodeIds, string $format = 'png', float $scale = 2.0): array
    {
        if (empty($nodeIds)) {
            return [];
        }
        $ids = implode(',', $nodeIds);
        $params = http_build_query([
            'ids' => $ids,
            'format' => $format,
            'scale' => $scale,
        ]);
        $url = $this->apiBase . "/images/{$fileKey}?{$params}";
        $resp = $this->http()->get($url)->throw();
        return (array) $resp->json('images', []);
    }
}


