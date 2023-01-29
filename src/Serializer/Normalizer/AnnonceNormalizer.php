<?php

namespace App\Serializer\Normalizer;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class AnnonceNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    private UrlGeneratorInterface $urlGenerator;
    private NormalizerInterface $normalizer;

    public function __construct(NormalizerInterface $normalizer, UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
        $this->normalizer = $normalizer;
    }

    public function normalize($annonce, string $format = null, array $context = []): array
    {
        $data = $this->normalizer->normalize($annonce, $format, $context);
        $data['link'] = $this->urlGenerator->generate('app_annonce_show', ['id' => $annonce->getId(), 'slug' => $annonce->getSlug()]);

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof \App\Entity\Annonce;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
