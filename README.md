# 🛠️ DevFix

DevFix est une application complète pour les réparateurs d'appareils électroniques, développée avec Symfony et Twig pour le frontend et le backend. Elle permet aux utilisateurs de créer des devis de réparation et aux administrateurs de gérer les devis et le stock des pièces.

<br>

## 🌟 Description

L'application DevFix permet à un utilisateur de :

1. Choisir un type d'appareil, sa marque, son modèle et le type de réparations à effectuer.
2. Obtenir un récapitulatif du devis.
3. S'inscrire ou se connecter pour enregistrer le devis.

L'application dispose également d'un côté administrateur qui permet de :

1. Gérer les devis (accepter, mettre à jour, etc.).
2. Gérer le stock des pièces.
3. Ajouter des types d'appareils, marques, modèles et types de réparations pour l'application côté utilisateur.

**Note : L'application n'est pas terminée. Il reste à finaliser le côté admin.**

<br>

## 🎥 Démonstration

Une vidéo de démonstration du parcours client pour la création d'un devis.

https://github.com/4d3n4n/DevFix/assets/140979426/ba70d038-6d0c-4cf4-a5fd-94950d08d069

une capture d'écran de la gestion des erreurs et de la sécurité pendant l'inscription.
![Capture d’écran 2024-02-25 à 18 11 36](https://github.com/4d3n4n/DevFix/assets/140979426/fdd51149-04bc-4866-ac1d-4ab0fa869a88)


<br>

## 📘 Rapport et présentation

<a href="https://github.com/user-attachments/files/15779872/rapport_de_stage_Adenan_KHACHNANE.pdf" target="_blank">Rapport</a>

<a href="https://github.com/user-attachments/files/15779871/DevFix_presetation.pdf" target="_blank">Présentation</a>

<br>

## 🛠️ Stack Technique

- **PHP Symfony 7**
- **MySQL**
- **Doctrine ORM**
- **Twig Template**
- **JavaScript**
- **CSS**
- **HTML**

<br>

## 🚀 Installation

1. Clonez le dépôt :
   ```bash
   git clone https://github.com/4d3n4n/DevFix.git
   cd DevFix
   ```
   
2. Installez les dépendances :
   ```bash
   composer install
   npm install
   ```

<br>

3. Configurez la base de données dans le fichier .env :
   ```env
   DATABASE_URL="mysql://user:password@127.0.0.1:3306/devfix"
   ```

<br>

4. Créez la base de données et exécutez les migrations :
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```

<br>

5. Démarrez le serveur Symfony :
   ```bash
   symfony serve
   ```

<br>

L'application devrait maintenant être accessible localement dans votre navigateur web à l'adresse http://localhost:8000.


Merci d'utiliser cette app ! 😊🔧
