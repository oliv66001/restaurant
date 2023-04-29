# restaurant Le Quai-Antique
## Installation en local 
### Prérequis

Téléchargement du dépôt git

Mise en place du site en local avec wampserver:

cloner le dépot git : git clone git@github.com:oliv66001/restaurant.git

1e commande : composer install

--------------------
mettre en commentaire dans le fichier config/packages/messenger.yaml comme dans l'exemple ligne 19:
 routing:
            # Symfony\Component\Mailer\Messenger\SendEmailMessage: async
            Symfony\Component\Notifier\Message\ChatMessage: async
            Symfony\Component\Notifier\Message\SmsMessage: async

-------

mise en place des fixtures : composer require --dev doctrine/doctrine-fixtures-bundle

2e commande : symfony console d:d:c (création de la base de donée)

3e commande : symfony console make:migration

4e commande : symfony console d:m:m

5e commande : symfony console d:f:l (création des données)

----------------------

Pour la 1er connexion voir mail et mot de passe fourni.

L'ajout d'administrateur se fait dans le back office du site.
