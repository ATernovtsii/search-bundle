<?php


namespace ATSearchBundle\CacheWarmer;

use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use ATSearchBundle\Elastic\Generator\IndexDocumentMetadataGenerator;

final readonly class CompiledClassCacheWarmer implements CacheWarmerInterface
{
    public function __construct(
        private IndexDocumentMetadataGenerator $documentMetadataGenerator,
    ) {
    }

    public function warmUp(string $cacheDir): array
    {
        if (!$this->documentMetadataGenerator->cacheBaseDir) {
            $this->documentMetadataGenerator->cacheBaseDir = $cacheDir;
        }

        $this->documentMetadataGenerator->compile();

        return [];
    }

    public function isOptional(): bool
    {
        return true;
    }
}