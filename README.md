![ArchiverStepBundle](doc/ArchiverStep.png)

ArchiverStepBundle
==========================

Plug this step to a connector and manage by yourself where to stock the imported files.
To make it easier for you, the connector will create a directory everyday to organize the files. 
You can also use it as a product import job.

## Requirements

| ArchiverStepBundle     | Akeneo PIM Community Edition | Akeneo PIM Enterprise Edition |
|:------------------------------:|:----------------------------:|:-----------------------------:|
| v1.1.*                         | v3.*                         | v3
.*                              |

## Installation

Enter the following command line:
```console
$php composer.phar require "nicolas-souffleur/archiver-step-bundle":"1.1.*"
```

Then enable the bundle in the ```app/AppKernel.php``` file in the registerProjectBundles() method:
```php
$bundles[] = new \Extensions\Bundle\ArchiverStepBundle\ExtensionsArchiverStepBundle()
```

## Usage

Two ways are possible :
* Use it as a product import job directly 
* Plug it to another connector 

### As a product import job 
* It's easy, go to the Import Profiles Management page and click on "Create Import Profile" 
* Then, select the 'CSV Product Import with Archiver Step' profile. 
* Configure it like a classic product import job.
* In the field "Archives directory path" configure the directory where you want to archive your files.

### Plug it to a connector
* In the edit form_extension file of your own connector, add the 
following lines at the end of the file : 
```yml
    pim-job-instance-csv-product-import-archiver-edit-properties-archiver-enabled:
        module: pim/job/common/edit/field/switch
        parent: pim-job-instance-csv-product-import-archiver-edit-global
        position: 125
        targetZone: properties
        config:
            fieldCode: configuration.archiverEnabled
            readOnly: false
            label: archiver.form.job_instance.tab.properties.archiverEnabled.title
            tooltip: archiver.form.job_instance.tab.properties.archiverEnabled.help

    pim-job-instance-csv-product-import-archiver-edit-properties-dir-archive:
        module: pim/job/common/edit/field/text
        parent: pim-job-instance-csv-product-import-archiver-edit-global
        position: 130
        targetZone: properties
        config:
            fieldCode: configuration.dirArchive
            readOnly: false
            label: archiver.form.job_instance.tab.properties.dir_archive.title
            tooltip: archiver.form.job_instance.tab.properties.dir_archive.help
```

* In your JobParameters file, add the two following declarations : 
```php
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Type;
```

Then add those two functions to add the two new fields to your configuration : 
```php
/**
     * {@inheritdoc}
     */
    public function getDefaultValues()
    {
        return array_merge($this->baseDefaultValuesProvider->getDefaultValues(), [
                'archiverEnabled' => true,
                'dirArchive'     => './var/archives/'
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getConstraintCollection()
    {
        $baseConstraints  = $this->baseConstraintCollectionProvider->getConstraintCollection();
        $constraintFields = array_merge($baseConstraints->fields, [
            'archiverEnabled' => new Type('bool'),
            'dirArchive'     => new NotNull()
        ]);

        return new Collection(['fields' => $constraintFields]);
    }
```

## Roadmap
* [DONE] Archive the files depending on the import status

Don't hesitate to send me a message if you would like other features :)

## About me
Specialized in Akeneo since its launch in 2014, I'm helping companies to implement this efficient and essential solution, to integrate it into their workflow and to structure their data. Feel free to contact me through my contact form on my website (http://www.nicolas-souffleur.com) or directly by email (contact@nicolas-souffleur.com).
