<?php
namespace App\Form;

use Symfony\Component\Form\DataTransformerInterface;

class JsonArrayTransformer implements DataTransformerInterface
{
    // Transforme un array en string (pour l'afficher dans un champ texte)
    public function transform($value): string
    {

        if (null === $value || [] === $value) {
            return '';
        }

        if (!is_array($value)) {
            throw new \InvalidArgumentException('Expected an array.');
        }

        return is_array($value) ? json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : '';
    }

    // Transforme une chaîne en array (pour sauvegarder dans l'entité)
    public function reverseTransform($value): array
    {
        if (null === $value || '' === $value) {
            return [];
        }

        $decoded = json_decode($value, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid JSON: ' . json_last_error_msg());
        }

        return $decoded;
    }
}