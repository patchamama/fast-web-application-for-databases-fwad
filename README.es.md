# Fast Web Application for Databases (FWAD)

La versión en inglés (más completa) de esta documentación es accesible desde [aquí](README.md)

**ESTE PROGRAMA NO ES FUNCIONAL CON LAS ACTUALES VERSIONES MÁS MODERNAS DE PHP**. La aplicación funcionaba con php 5.5 y se ejecutó en computadoras con windows usando [XAMPP](https://sourceforge.net/projects/xampp/files/XAMPP%20Windows/5.5.27/)

He subido el contenido del proyecto a Github por sí le es útil a alguien y para actualizar el programa en varias fases que permita ser ejecutado en versiones de php más modernas (con las mejoras de seguridad) y para crear un diseño web más moderno.

---

FWAD fue desarrollada entre el 2004-2006 como respuesta al desarrollo de una aplicación que debía de adaptarse a colecciones de plantas de diferentes jardines botánicos, con diferentes necesidades, lo que significa que una aplicación genérica que cumpliera las necesidades de todos los jardines botánicos no era posible y que habría que adaptar la aplicación a los diferentes usuarios (colecciones botánicas) con diferentes campos y a veces diferentes tablas (modelo de la base de datos).

Para poder cumplir con estos requerimientos, se generó este programa que debía de leer un archivo xml de configuración y de fácil modificación que permitiera al programa de forma rápida adaptarse a las condiciones de cada modelo de datos deseados sin tener que hacer cambios en el código del programa y de ahí salió la idea de este programa, que no es más que un intérprete de archivos de configuración (xml) y que genera los formularios y consultas en dependencia de este archivo.

Tengo una relación ambigua con este programa pues por una parte estoy orgulloso de las funcionalidades logradas y la solución más inteligente pensada para cumplir con los requerimientos necesarios, pero por otro lado por la falta de experiencia en programación a este nivel de un programa tan complejo, se realizó sin tener en cuenta el uso de frameworks de php (solo se usó jQuery) y usando un editor de texto simple como [notepad++](https://notepad-plus-plus.org) y el código del programa principal (`run.php`) no usa principios de código limpio siendo un código espagueti que puede dificultar su entendimiento y mantenimiento. _Adicionalmente la aplicación fue hecho completamente en Cuba sin un acceso a internet y valiéndome de documentación contenida en libros o pdfs._

# Objetivos

El objetivo final de la aplicación (FWAD), además de lograr un desarrollo rápido, también era generar archivos de intercambios de colecciones biológicas (archivos en formato xml) que permitan el intercambio de información de _colecciones vivas_ (ITF2) y de _colecciones conservadas_ (HISPID3), para hacer accesible y fomentar el intercambio mundial entre colecciones de información como parte de la red GBIF.

El programa no solamente se limita a gestionar colecciones de plantas y ha sido usado posteriormente para una base de datos de cocteles y para aprendizaje de idioma (estos ejemplos se agregaran en la medida que se migre la aplicación a versiones más modernas de php).

# Pruebas de la aplicación

La aplicación se probó durante varios años en los jardines botánicos y se hicieron varios talleres con los usuarios/operadores (con una duración de una semana cada taller) en que se hicieron mejoras continuas a la aplicación núcleo (FWAD) para cumplir con las necesidades diversas solicitadas.

# Pendiente

- Migrar toda la aplicación a una versión más moderna de php como la versión 7 u 8
- Refactorizar todo el código y separarlo en archivos (librerías) que permita un mejor reuso del cógido y su mantenimiento
- Migrar la aplicación para su uso con algún framework moderno de php como `Symfony` o `Laravel`, o separar la aplicación en una API REST en el Back End usando estos frameworks u otros más rápicos como `Phalcon` o `Slim` y un diseño de interface web más moderna como `React` o `VueJS` para la Vista.
- Cambiar el uso de las drivers de conexión a la base de datos de AdoDB a otra más moderna.
- Usar un ORM moderno (y sustituir el programado en la aplicación)
