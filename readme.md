# PSR4 Helper [![PHP version](https://img.shields.io/badge/PHP-8.0-blue)](https://img.shields.io/badge/PHP-8.0-blue) [![MIT License](https://img.shields.io/badge/license-MIT-green)](https://img.shields.io/badge/license-MIT-green)

Introduction
------------
This tool should help with the transition of php code, especially class names, namespaces, files and folders of a chaotic unorganized project to psr-4 and enable the use of composer auto-loading via psr-4.

Installation
------------

The recommended way to install this helper is through Composer:

`composer global require zbyrih/ps4-helper --dev`

Usage
------------

first you have to create config file psr4helper.neon in you current working directory:
```yml
path: App # folder name from current working directory
namespace: App\ # namespace root
excludeCaseUpdates: # names of folders that will be excluded from checking validation and changes
    - templates
    - translations
excludePsr4CheckClassEndsWith: # end names of classes that will be excluded from the psr4 check
    - Presenter
```

now, you can use console command options:
  - help           : Display this help message
  - init           : Init default neon config
  - multi          : List multiple classes in one file
  - psr4           : List of classes with wrong folders by PSR-4 `info|case|missing`
    - info           : show mismatch and missing files for classes
    - case           : mismatch case
    - missing        : missing files for classes
  - find           : List of classes with a fully quantified name starting with a given value
  - update-case    : Rename folders with mismatch case `info|rename`
    - info: just printing the information doesn't change anything
    - rename: change names of folders
  - create-dirs    : Create missing folders by classes namespaces
  - create-files   : Create missing files by multiple classes
  - clear-git      : With `clear` value will remove all cached duplicate folders with mismatch case from index
