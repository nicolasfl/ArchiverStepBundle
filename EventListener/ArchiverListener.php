<?php

namespace Extensions\Bundle\ArchiverStepBundle\EventListener;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Akeneo\Tool\Component\Batch\Event\JobExecutionEvent;
use Akeneo\Tool\Component\Batch\Job\ExitStatus;

/**
 * Class ArchiverListener
 *
 * @author                 Nicolas SOUFFLEUR, Akeneo Expert <contact@nicolas-souffleur.com>
 * @license                http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ArchiverListener
{

    /**
     * Convenient constant value representing finished processing with an error.
     */
    const WARNING = "WARNING";

    /**
     * @param JobExecutionEvent $event
     *
     * @return bool
     */
    public function onAfterJobExecution(JobExecutionEvent $event)
    {

        $jobExecution = $event->getJobExecution();

        $jobParameters = $jobExecution->getRawParameters();

        if (array_key_exists('archiverEnabled', $jobParameters) && $jobParameters['archiverEnabled'] === true) {
            $filePath = $jobParameters['filePath'];

            $status = $jobExecution->getExitStatus()->getExitCode();

            if ($status === ExitStatus::COMPLETED) {
                foreach ($jobExecution->getStepExecutions() as $stepExecution) {
                    if ($stepExecution->getWarnings()->count() > 0) {
                        $status = self::WARNING;
                        break;
                    }
                }
            } else {
                $status = ExitStatus::FAILED;
            }

            $archiveDir = $this->getStatusDirectory($jobParameters['dirArchive'], $status, $filePath);

            try {
                $fileSystem = new Filesystem();
                $fileSystem->copy($filePath, $archiveDir);
            } catch (IOExceptionInterface $exception) {
                echo "An error occurred while moving your file at " . $exception->getPath();
            }
        }

        return true;
    }

    /**
     * @param $archiveDir
     * @param $status
     *
     * @return string
     */
    public function getStatusDirectory($archiveDir, $status, $filePath)
    {
        $archiveDir = rtrim($archiveDir, '/') . '/';
        $archiveDir = $archiveDir . $status . '/' . date('dmY');
        $archiveDir = $this->checkDir($archiveDir);
        $archiveDir = $archiveDir . '/' . date('His') . '-' . substr($filePath, strrpos($filePath, '/') + 1);

        return $archiveDir;
    }

    /**
     * @param $dir
     *
     * @return mixed
     */
    public function checkDir($dir)
    {
        $fileSystem = new Filesystem();

        if (!$fileSystem->exists($dir)) {
            try {
                $fileSystem->mkdir($dir, 0755);
            } catch (IOExceptionInterface $exception) {
                echo "An error occurred while creating your directory at " . $exception->getPath();
            }
        }

        return $dir;
    }
}