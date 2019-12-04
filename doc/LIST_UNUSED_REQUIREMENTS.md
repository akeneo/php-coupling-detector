# List unused requirements

The _list-unused-requirements_ command is a complementary tool to the _detect_ command that allows you to easily clean
your configuration. It lists the requirements that are not necessary anymore for each rule :

```bash
    php bin/php-coupling-detector list-unused-requirements
```

Like the _detect_ command, you can specify the path to the configuration file with the ``--config-file`` option.

The exit status of the _list-unused-requirements_ command can be: ``10`` if some unused requirements have been found,
or ``0`` otherwise.

Please note that for the moment this is relevant only for rules of type ``ONLY`` as the notion of "unused requirement"
in a ``DISCOURAGED`` or ``FORBIDDEN`` rule makes no sense.
