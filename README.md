# umlgeneration-bundle
generates UML class diagrams and more based upon your class definitions

## Setup
* copy env.dist and replace USER_ID with own ID (Retrieve: `echo $(id -u)` in a terminal)

## Install

Before you can use the Bundle, you need to add the git repository to your composer.json


```JSON
// composer.json
{
  "require": {
    "mike4git/umlgeneration-bundle": "dev-main"
  },
  ...
  "autoload": {
    "psr-4": {
      ...
      "Neusta\\Pimcore\\UMLGenerationBundle\\": "bundles/Neusta/Pimcore/UMLGenerationBundle/src"
    }
  ...
  "repositories": {
    "uml-generation-bundle": {
      "type": "git",
      "url": "https://github.com/mike4git/umlgeneration-bundle.git"
    }
  },
}
```

Additionally, you'll have to install GraphViz (`dot` executable).
Users of Debian/Ubuntu-based distributions may simply invoke:

```bash
$ sudo apt-get install graphviz
```

Windows users have to [download GraphViZ for Windows](http://www.graphviz.org/Download_windows.php) and remaining
users should install from [GraphViz homepage](http://www.graphviz.org/Download.php).

Use the following URL for the GraphViz usage: 
[GraphViz](https://graphviz.org/doc/info/command.html)

## Usage

#### Generate dotfile:
```bash
$ php bin/console uml:generate -o myDotfileName
```
Note that this will generate a myDotfileName.dot file


#### Generate Graph:
```bash
$ dot -Tsvg myDotfileName.dot -o image.svg
```

#### How to use the "dot" command:
```bash
dot -T${fileType} ${filename}.dot -o ${outputFile}
```

Note that this will generate a svg.