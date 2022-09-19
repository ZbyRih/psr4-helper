# PSR4 Helper [![PHP version](https://img.shields.io/badge/PHP-8.0-blue)](https://img.shields.io/badge/PHP-8.0-blue) [![MIT License](https://img.shields.io/badge/license-MIT-green)](https://img.shields.io/badge/license-MIT-green)

Introduction
------------
This tool should help with the transition of php code, especially class names, namespaces, files and flders of a chaotic unorganized project to psr-4 and enable the use of composer autoloading via psr-4

Installation
------------

The recommended way to install this helper is through Composer:

`composer global require zbyrih/ps4-helper --dev`

Usage
------------

first you have to create config file psr4helper.neon in you current working directory:
```yml
parameters:
	path: App # folder name from current working directory
	namespace: App\ # namespace root
	caseUpdateOmmit: # names of folders that will be omitted from checking validation and changes
		- templates
		- translations
	psr4CheckClassEndsWithOmmit: # end names of classes that will be omitted from the psr4 check
		- Presenter
```

now, you can use console command options:
  - --help           : Display this help message
  - --list           : List multiple classes in one file
  - --psr4           : List of classes with wrong folders by PSR-4 `info|case|missing`
	- info: show mismatch and missing files for classes
	- case: mismatch case
	- missing: missing files for classes
  - --find           : List of classes with a fully quantified name starting with a given value
  - --case-update    : Rename folders with mismatch case `info|rename`
	- info: just printing the information doesn't change anything
	- rename: change names of folders
  - --create-dirs    : Create missong folders by classes namspaces
  - --create-files   : Create missing files by multiple classes
  - --git-clear      : With `clear` value will remove all cached duplicate folders with mismatch case from index
