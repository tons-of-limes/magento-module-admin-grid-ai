<?php
/**
 * Copyright © tons of limes <https://github.com/tons-of-limes>. All rights reserved.
 */
declare(strict_types=1);

namespace TonsOfLimes\AdminGridAI\Model\Prompt;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Module\Dir;
use Magento\Framework\Module\Dir\Reader as ModuleReader;
use Magento\Framework\Filesystem\Driver\File;

/**
 * Reads template content from module files.
 */
class FileReader
{
    public function __construct(
        private readonly ModuleReader $moduleReader,
        private readonly File $fileDriver
    ) {}

    /**
     * Read template content from module path.
     *
     * @param string $templatePath Path in format "Module_Name::path/to/template.md"
     * @return string
     * @throws FileSystemException
     */
    public function read(string $templatePath): string
    {
        [$moduleName, $filePath] = explode('::', $templatePath, 2);
        $moduleDir = $this->getModuleDir($moduleName);

        $fullPath = $moduleDir . DIRECTORY_SEPARATOR . $filePath;

        if (!$this->fileDriver->isExists($fullPath)) {
            throw new FileSystemException(__('Template file "%1" does not exist.', $fullPath));
        }

        return $this->fileDriver->fileGetContents($fullPath);
    }

    /**
     * Get module root dir.
     *
     * @param string $moduleName
     * @return string
     */
    private function getModuleDir(string $moduleName): string
    {
        $etcDirPath = $this->moduleReader->getModuleDir(Dir::MODULE_ETC_DIR, $moduleName);

        return dirname(realpath($etcDirPath));
    }
}

