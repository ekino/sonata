CHANGELOG
=========

master
------

* Switch to the new security checker
* Migrate from Travis to GitHub Actions

v0.2.0
------

* Update friendsofphp/php-cs-fixer 
* Remove namespaces MockObject in tests files
* CI: remove php version 7.1
* CI: add php version 7.4
* CI: change symfony version from 4.2 to 4.4
* CI: add php version 7.4 at nightly
* Upgrade symfony/framework-bundle version to fix security issue
* Adapted ImmutableTabsType and its test class to new Sonata form component (fixed namespace and inheritance)
* Upgraded ekino/phpstan-banned-code to 0.3 and upgraded its dependencies
* Dropped support for Symfony 3.x in favor of 4.x+

v0.0.1
------

* Add new form type: ImmutableTabsType
* Enable strict typing
* Add Coveralls Tool for TravisCI
* Add badges in the README
* Remove deprecation about configuration tree builder without a root node
* Minimum supported version of PHP is 7.1
* Minimum supported version of Symfony is 3.4
* Minimum supported version of Sonata is 3.X
* feature #6 [Phpunit] Implements tests helpers
* Added (optional) replacement for "Add block" button, showing blocks in a categorized popup
* Fixed PHP-CS-Fixer config
* Added integration of a liipMonitor dashboard
