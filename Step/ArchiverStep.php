<?php

namespace Extensions\Bundle\ArchiverStepBundle\Step;

use Akeneo\Tool\Component\Batch\Step\AbstractStep;
use Akeneo\Tool\Component\Batch\Model\StepExecution;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

/**
 * Class ArchiverStep
 *
 * @author                 Nicolas SOUFFLEUR, Akeneo Expert <contact@nicolas-souffleur.com>
 * @license                http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ArchiverStep extends AbstractStep
{

    /**
     * @param StepExecution $stepExecution
     *
     * @return bool
     */
    public function doExecute(StepExecution $stepExecution)
    {
        $jobParameters = $stepExecution->getJobParameters();

        $filePath = $jobParameters->get('filePath');

        $fileSystem = new Filesystem();

        $archiveDir = $this->formatArchiveDirectory($jobParameters->get('dirArchive'));

        if (!$fileSystem->exists($archiveDir)) {
            try {
                $fileSystem->mkdir($archiveDir, 0755);
            } catch (IOExceptionInterface $exception) {
                echo "An error occurred while creating your directory at " . $exception->getPath();
            }
        }

        $newDir = $archiveDir . '/' . date('hms') . '-' . substr($filePath, strrpos($filePath, '/') + 1);

        try {
            $fileSystem->copy($filePath, $newDir);
        } catch (IOExceptionInterface $exception) {
            echo "An error occurred while moving your file at " . $exception->getPath();
        }

        return true;
    }

    /**
     * @param $archiveDir
     *
     * @return string
     */
    public function formatArchiveDirectory($archiveDir)
    {
        $archiveDir = rtrim($archiveDir, '/') . '/';
        $archiveDir = $archiveDir . date('dmY');

        return $archiveDir;
    }
}
