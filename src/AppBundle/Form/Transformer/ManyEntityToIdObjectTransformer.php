<?php

namespace AppBundle\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;

class ManyEntityToIdObjectTransformer implements DataTransformerInterface
{
    /**
     * @var EntityToIdObjectTransformer
     */
    private $entityToIdObjectTransformer;

    /**
     * ManyEntityToIdObjectTransformer constructor.
     * @param EntityToIdObjectTransformer $entityToIdObjectTransformer
     */
    public function __construct(EntityToIdObjectTransformer $entityToIdObjectTransformer)
    {
        $this->entityToIdObjectTransformer = $entityToIdObjectTransformer;
    }

    /**
     * Do nothing
     *
     * @param array $array
     * @return array
     */
    public function transform($array)
    {
        $transformed = [];

        if (empty($array) || null === $array) {
            return $transformed;
        }

        foreach ($array as $k => $v) {
            $transformed[] = $this->entityToIdObjectTransformer->transform($v);
        }

        return $transformed;
    }

    /**
     * Transforms an array of arrays including an identifier to an object.
     *
     * @param array $array
     *
     * @return array
     */
    public function reverseTransform($array)
    {
        if (!is_array($array)) {
            $array = [$array];
        }

        $reverseTransformed = [];

        foreach ($array as $k => $v) {
            $reverseTransformed[] = $this->entityToIdObjectTransformer->reverseTransform($v);
        }

        return $reverseTransformed;
    }
}