# Detect coupling problems

The _detect_ command detects coupling problems for a given file or directory depending on the
 coupling rules that have been defined:

```bash
    php bin/php-coupling-detector detect /path/to/directory
    php bin/php-coupling-detector detect /path/to/file
```

 The exit status of the _detect_ command can be: ``0`` if no violations have been raised, ``10`` in case of
 warnings and ``99`` in case of errors.
 
 You can save the configuration in a ``.php_cd`` file in the root directory of
 your project. The file must return an instance of ``Akeneo\CouplingDetector\Configuration\Configuration``,
 which lets you configure the rules and the directories that need to be analyzed.
 Here is an example below:
 
 ```php
    <?php

    $finder = new \Symfony\Component\Finder\Finder();
    $finder
        ->files()
        ->name('*.php')
        ->notPath('foo/bar/');
 
    $builder = new \Akeneo\CouplingDetector\RuleBuilder();
    
    $rules = [
        $builder->forbids(['bar', 'baz'])->in('foo'),
        $builder->discourages(['too'])->in('zoo'),
        $builder->only(['bla', 'ble', 'blu'])->in('bli'),
    ];

    return new \Akeneo\CouplingDetector\Configuration\Configuration($rules, $finder);
    ?>
```
 
 You can also use the default finder implementation if you want to analyse all the PHP files
 of your directory:
 
 ```php
    <?php
    
    $builder = new \Akeneo\CouplingDetector\RuleBuilder();
    
    $rules = [
        $builder->forbids(['bar', 'baz'])->in('foo'),
        $builder->discourages(['too'])->in('zoo'),
        $builder->only(['bla', 'ble', 'blu'])->in('bli'),
    ];
 
    return new \Akeneo\CouplingDetector\Configuration\Configuration(
        $rules,
        new \Akeneo\CouplingDetector\Configuration\DefaultFinder()
    );
    ?>

 ```
 
 With the ``--config-file`` option you can specify the path to the ``.php_cd`` file:
 
```bash
    php bin/php-coupling-detector detect /path/to/dir --config-file=/path/to/my/own_configuration_file.php
```

With the ``--format`` option you can specify the output format:

```bash
    php bin/php-coupling-detector /path/to/dir --format=dot
```
