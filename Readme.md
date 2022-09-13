#Api MoBile P7
---
##A propos du projet
---
##Ce projet est le P07 d'openclassroom sur le parcour Symfony/Php.

###Presentation de l'Api.
---
Seulement les utilisateur enregistrer peuvent avoir acces a cette Api et prendre la liste des produits et les details de leur clients.

Cette application a etais crée avec Symfony et Api plateform.

#####Crée sous
Symfony 6.1.3

Php = 8.1.0

#####Pour lancer :
Vous devez clonez ce projet ou vous pouvez utilisé le code et la Base de données envoyé

#####Prérequits
git
git clone https://github.com/Geoffray-buhler/p07.git

composer
composer install
#####Installation
Vous devez mettre le fichier .env dans le repertoire principal. (Delivrable OC).

La base de données est aussi dans les délivrables.

Vous pouvez crées des produits et des utilisateurs avec les commande suivants

php bin/console app:create-user && php bin/console app:create-product

$ php bin/console lexik:jwt:generate-keypair
#####Utilisation

Cette application a etais crée pour apprendre comment crée une Api avec Symfony
Toute la documentation est accessible a https://localhost/api

Contact
Project Link: https://github.com/Geoffray-buhler/p07