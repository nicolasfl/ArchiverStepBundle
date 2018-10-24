<?php

namespace Extensions\Bundle\ArchiverStepBundle\Job\JobParameters;

use Akeneo\Tool\Component\Batch\Job\JobInterface;
use Akeneo\Tool\Component\Batch\Job\JobParameters\ConstraintCollectionProviderInterface;
use Akeneo\Tool\Component\Batch\Job\JobParameters\DefaultValuesProviderInterface;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotNull;

/**
 * Class ProductCsvImportArchiver
 *
 * @author                 Nicolas SOUFFLEUR, Akeneo Expert <contact@nicolas-souffleur.com>
 * @license                http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ProductCsvImportArchiver implements ConstraintCollectionProviderInterface, DefaultValuesProviderInterface
{

    /** @var DefaultValuesProviderInterface */
    private $baseDefaultValuesProvider;
    /** @var ConstraintCollectionProviderInterface */
    private $baseConstraintCollectionProvider;
    /** @var string[] */
    private $supportedJobNames;

    /**
     * @param DefaultValuesProviderInterface        $baseDefaultValuesProvider
     * @param ConstraintCollectionProviderInterface $baseConstraintCollectionProvider
     * @param string[]                              $supportedJobNames
     */
    public function __construct(DefaultValuesProviderInterface $baseDefaultValuesProvider, ConstraintCollectionProviderInterface $baseConstraintCollectionProvider, array $supportedJobNames)
    {
        $this->baseDefaultValuesProvider        = $baseDefaultValuesProvider;
        $this->baseConstraintCollectionProvider = $baseConstraintCollectionProvider;
        $this->supportedJobNames                = $supportedJobNames;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultValues()
    {
        return array_merge($this->baseDefaultValuesProvider->getDefaultValues(), [
                'dirArchive' => './var/archives/'
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getConstraintCollection()
    {
        $baseConstraints  = $this->baseConstraintCollectionProvider->getConstraintCollection();
        $constraintFields = array_merge($baseConstraints->fields, ['dirArchive' => new NotNull()]);

        return new Collection(['fields' => $constraintFields]);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(JobInterface $job)
    {
        return in_array($job->getName(), $this->supportedJobNames);
    }
}
