# Fast Web Application for Databases (FWAD)

The Spanish version of this documentation can be accessed [here](README.es.md)

FWAD was developed between 2004-2006 in response to the development of an application that had to be adapted to plant collections from different botanic gardens, with different needs, meaning that a generic application meeting the needs of all botanic gardens was not possible and that the application would have to be adapted to different users (botanic collections) with different fields and sometimes different tables (database model).

In order to meet these requirements, this program was created to read an easily modified _xml configuration file_ that would allow the program to quickly adapt to the conditions of each desired data model without having to make changes in the main program code, and that is where the idea for this program came from, which is nothing more than an interpreter of configuration files (xml) and which generates forms and queries (similar to a modernORM) depending on this configuration file.

I have an ambiguous relationship with this application because on the one hand I am proud of the functionality achieved and the most intelligent solution thought to meet the necessary requirements (based of a app that read a general xml configuration file and change it behaivor...), but on the other hand due to the lack of experience in programming at this level of such a complex program, it was done without taking into account the use of php frameworks (only jQuery was used) and using a simple text editor like [notepad++](https://notepad-plus-plus.org), so the main app code (`run.php`) does not use _clean coding_ principles and is _spaghetti code_ with more than 6000 lines which can make it difficult to understand and maintain. _In addition, the application was made entirely in Cuba without internet access and using documentation contained in books or pdfs._

# Objectives

The ultimate goal of the application, in addition to achieving rapid development, was also to generate biological collection exchange files (xml files) that allow the exchange of information from _living collections_ (ITF2) and _preserved collections_ (HISPID3), to make accessible and encourage global exchange between collections of information as part of the GBIF network.

The programme is not only limited to managing plant collections and has subsequently been used for a cocktail database and for language learning (these examples will be added as the application is migrated to more modern versions of p

# Tests carried out

The application was tested for several years in the botanic gardens and several workshops were held with the users/operators (lasting one week each workshop) in which continuous improvements were made to the core application (FWAD) to meet the various needs requested.

# Develop

## Install php and nodeJS (MacOs)

```
brew tap shivammathur/php
brew install shivammathur/php/php@5.6
brew install node

# point php to the 5.6 install
brew link --overwrite --force shivammathur/php/php@5.6
php --version
```

### Install nodeJS Server (light and fast server)

_References: https://github.com/paragi/sphp_

```
npm install sphp express express-session ws body-parser
# 1 Test the server
node node_modules/sphp/example.js
# Connect to the example server in a browser: http://localhost:8080

# 2 Test the server in root directory
node server.js
# Open the browser in url: http://localhost:8080/string_encode.php
```

## Instal php multiplatform (XAMPP)

Instructions, as an alternative for use on windows, linux and also on MacOSx [here](https://www.apachefriends.org/download.html)

# Pending

- Migrate the entire application to a more modern version of php such as version 7 or 8.
- Refactor all the code and separate it into files (libraries) to allow better code reuse and maintenance.
- Migrate the application for use with a modern php framework such as `Symfony` or `Laravel`, or separate the application into a REST API on the back end using these frameworks or other faster frameworks such as `Phalcon` or `Slim` and a more modern web interface design such as `React` or `VueJS` for the View.
- Change the use of database connection drivers from AdoDB to a more modern one.
- Use a modern ORM (and replace the one programmed in the application).

# Credits

- How to install php 5.6 with homebrew if from this year it is EOL?: https://stackoverflow.com/questions/54143760/how-to-install-php-5-6-with-homebrew-if-from-this-year-it-is-eol
- XAMPP: https://www.apachefriends.org/download.html
- International transfer format for botanical garden plant records. Vers. 2: http://www.bgci.org/files/Databases/itf2.pdf
- A Mapping of HISPID3 to ABCD 1.49d: https://www.bgbm.org/tdwg/codata/Schema/Mappings/HISPID3.htm
- GBIF | Global Biodiversity Information FAcility: Free and open access to biodiversity data: https://www.gbif.org/
- Snappy PHP for nodeJS: https://github.com/paragi/sphp
- Execute PHP scripts within Node.js web server: https://stackoverflow.com/questions/6542169/execute-php-scripts-within-node-js-web-server
