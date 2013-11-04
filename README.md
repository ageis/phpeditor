phpeditor 0.01
=========
Simple text editing for files on a webserver.

Here is a web-facing script you can use to edit various txt, php, js, css, pl or ASCII/UTF-8 files on a webserver. 
It has simple directory browsing capabilities, as much as your PHP/httpd privileges will permit.
Use . to navigate to the current working directory, and .. to go to the parent.
You can also create, rename, or delete files.
The allowed file extensions are set with $supported_ext within the script.
It comes with an .htaccess and .htpasswd for securing access to the script with Apache basic authentication.
Due to security concerns. I do not recommend working without them. Here are the default credentials:

username: editor
password: 3d1t0r

Use the htpasswd utility in the apache2-utils package to create a replacement login.
