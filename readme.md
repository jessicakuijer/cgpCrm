# Gestion CRM

Ce projet est une application web de gestion des relations clients (CRM) conçue pour une conseillère en gestion de patrimoine. Il a été développé en utilisant le framework Symfony 6 et EasyAdmin 4 pour la partie administrateur.

## Pré-requis

Pour installer et exécuter ce projet, vous aurez besoin de:

- PHP 8.1
- Symfony CLI
- Node.js (version LTS)

## Installation

Suivez ces étapes pour installer et configurer ce projet sur votre machine locale.

1. **Clonez le dépôt**

   Utilisez la commande suivante pour cloner le dépôt sur votre machine locale.  

```
git clone https://github.com/jessicakuijer/cgpCrm.git
```  

2. **Installer les dépendances PHP**

Après avoir cloné le dépôt, déplacez-vous dans le dossier du projet et utilisez Composer pour installer les dépendances PHP.  

```
cd my-project/
composer install
```

3. **Configurer l'environnement**

Copiez le fichier `.env` en `.env.local` et ajustez les variables d'environnement si nécessaire. 
Ajoutez la variable d'environnement `APP_ENV=dev` pour activer le mode développement ou `APP_ENV=prod` pour le mode production.  
Ajoutez vos paramètres de base de données dans le fichier `.env.local` et créez la base de données.

4. **Lancer les migrations**
Après avoir configuré la base de données, lancez les migrations pour créer les tables de la base de données.

```
php bin/console doctrine:migrations:migrate
```

5. **Installer les dépendances Node.js**

Ce projet utilise Webpack Encore pour gérer les actifs, qui nécessite Node.js. Utilisez npm (qui est inclus avec Node.js) pour installer les dépendances.
    
```
npm install
```  
Si vous rencontrez des problèmes lors de l'installation des dépendances, vous pouvez essayer de supprimer le fichier `package-lock.json` et d'exécuter la commande `npm install` à nouveau.

6. **Compiler les assets**
Après avoir installé les dépendances Node.js, vous pouvez compiler les assets en utilisant Webpack Encore. 

En mode développement:
```
npm run dev
```
En mode production:
```
npm run build
```  

7. **Lancer le serveur**
Utilisez la CLI Symfony pour lancer le serveur de développement.
    
```
symfony server:start (ou serve -d)
```  
Ouvrez votre navigateur et accédez à l'URL `localhost:8000` pour accéder à l'application.
Vous ne pourrez pas accéder au tableau de bord administrateur tant que vous n'aurez pas créé un utilisateur avec le rôle `ROLE_ADMIN`.

8. **Promouvoir un utilisateur en tant qu'admin**

Pour promouvoir un utilisateur en tant qu'administrateur, vous pouvez utiliser la commande `app:promote-admin`.  Vous devrez spécifier l'adresse e-mail de l'utilisateur dans le fichier `PromoteAdminCommand` et définir un mot de passe dans la variable d'environnement `ADMIN_PLAIN_PASSWORD`.  
Spécifier cette variable dans votre fichier `.env.local` ou au sein de votre serveur de production.  
Ce mot de passe sera automatiquement hashé lors de l'exécution de la commande.

```
php bin/console app:promote-admin
```  
Cette commande ajoutera le rôle `ROLE_ADMIN` à l'utilisateur et lui permettra d'accéder au tableau de bord administrateur.  

## Utilisation de l'application ##
### Ajout de client ###
Vous pouvez ajouter vos clients, leurs informations personnelles dans le crm. Pour ajouter un client, cliquez sur le bouton "Ajouter un client" dans le menu de navigation. Vous serez redirigé vers un formulaire où vous pourrez ajouter les informations du client et par qui il a été recommandé.  

### Exporter les données ###
Vous pouvez exporter les données de vos clients dans un fichier CSV.  
Pour ce faire, cliquez sur le bouton "Export to CSV" dans le menu de navigation du tableau de bord.  
## License ##
Ce projet est sous licence MIT. Me contacter pour toute question.


