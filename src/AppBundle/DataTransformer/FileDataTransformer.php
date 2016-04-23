<?php

namespace AppBundle\DataTransformer;

use AppBundle\DTO\FileDTO;
use AppBundle\Model\FileInterface;

class FileDataTransformer
{
    /**
     * @param FileInterface $file
     * @return FileDTO
     */
    public function convertToDTO(FileInterface $file)
    {
        $dto = new FileDTO();

        $dto->setName($file->getDisplayedFileName());

        return $dto;
    }

    /**
     * @param FileInterface $file
     * @param FileDTO $dto
     * @return FileInterface
     */
    public function updateFromDTO(FileInterface $file, FileDTO $dto)
    {
        if ($file->getDisplayedFileName() !== $dto->getName()) {
            $file->changeDisplayedFileName($dto->getName());
        }

        return $file;
    }
}