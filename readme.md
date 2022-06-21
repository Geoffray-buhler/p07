[![Codacy Badge](https://app.codacy.com/project/badge/Grade/2b1cfcfe73044d12861a25809c11f852)](https://www.codacy.com/gh/Geoffray-buhler/p07/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=Geoffray-buhler/p07&amp;utm_campaign=Badge_Grade)

Pour utilisé cette API, il faut faire un clone de ce git : 
- https://github.com/Geoffray-buhler/p07

puis faire: 
- composer install
- php bin/console make:migration
- php bin/console doctrine:migrations:migrate

et ensuite lancer le projet avec la commande: 
- symfony server:start

Pour crée un nouveau produit veuillez utilisez cette commande : 
- php bin/console app:create-produit [Name] [Color] [Description] [Prices]

Pour crée un nouvel utilisateur veuillez utilisez cette commande : 
- php bin/console app:create-user [username] [password] [email]