<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Storage;

use App\Infrastructure\Exception\FileNotFoundInTheStorageException;

class GetFileService
{
    private const FILE_NOT_FOUND_MESSAGE = 'File %s not found in the storage folder.';

    /**
     * @throws FileNotFoundInTheStorageException
     * @param string $fileName
     * @return string
     */
    public function call(string $fileName): string
    {
        $file = file_get_contents(getenv('WORKLOG_APP_BASE_PATH') . DIRECTORY_SEPARATOR . getenv('WORKLOG_APP_STORAGE_PATH') . DIRECTORY_SEPARATOR . $fileName);
        if (false === $file) {
            throw new FileNotFoundInTheStorageException(sprintf(self::FILE_NOT_FOUND_MESSAGE, $fileName));
        }
        return $file;
    }
}
