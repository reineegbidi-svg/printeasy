# Guide d'utilisateur — PrintEasy

**Plateforme web de gestion des services d'impression, photocopie et scan**

---

## Sommaire

1. [Présentation générale](#1-présentation-générale)
2. [Inscription et connexion](#2-inscription-et-connexion)
3. [Guide pour les clients](#3-guide-pour-les-clients)
4. [Guide pour les imprimeurs](#4-guide-pour-les-imprimeurs)
5. [Guide pour les administrateurs](#5-guide-pour-les-administrateurs)
6. [FAQ](#6-faq)

---

## 1. Présentation générale

PrintEasy est une plateforme qui met en relation des clients souhaitant imprimer, photocopier ou scanner des documents, et des imprimeurs (boutiques, reprographies, espaces de coworking) pouvant traiter ces demandes.

Le fonctionnement principal est le suivant :
1. Le client crée une commande en téléchargeant ses fichiers et en choisissant les options souhaitées.
2. L'imprimeur consultable la file des commandes disponibles et accepte celle qu'il souhaite traiter.
3. L'imprimeur met à jour le statut de la commande jusqu'à la livraison.
4. Le client peut suivre l'avancement en temps réel et payer en ligne ou à la réception.

---

## 2. Inscription et connexion

### 2.1 Inscription

Pour créer un compte :
1. Ouvrez la page de connexion.
2. Cliquez sur « Créer un compte ».
3. Sélectionnez votre rôle :
   - **Client** : Pour passer des commandes.
   - **Imprimeur** : Pour traiter des commandes (nécessite une validation par un administrateur).
4. Remplissez le formulaire :
   - Nom complet
   - Adresse e-mail (unique)
   - Mot de passe (minimum 8 caractères)
   - (Pour les imprimeurs) Adresse et numéro de téléphone
5. Cliquez sur « S'inscrire ».

### 2.2 Connexion

Pour vous connecter :
1. Ouvrez la page de connexion.
2. Saisissez votre e-mail et votre mot de passe.
3. Cliquez sur « Se connecter ».

Vous serez redirigé vers le tableau de bord correspondant à votre rôle.

---

## 3. Guide pour les clients

### 3.1 Tableau de bord

Le tableau de bord client vous montre :
- Un résumé de vos commandes récentes.
- Un bouton pour créer une nouvelle commande.

### 3.2 Créer une commande

1. Cliquez sur « Nouvelle commande » (depuis le tableau de bord ou la page « Mes commandes »).
2. **Téléchargez vos fichiers** :
   - Cliquez sur la zone de dépôt ou faites glisser vos fichiers.
   - Formats acceptés : PDF, DOC, DOCX, JPG, JPEG, PNG.
   - Taille maximale par fichier : 20 Mo.
   - Vous pouvez ajouter plusieurs fichiers.
   - Pour supprimer un fichier, cliquez sur la croix rouge en haut à droite du fichier.
3. **Aperçu des fichiers** : Les images et les PDF sont affichés en aperçu avant la validation.
4. **Choisissez les options** :
   - **Type de service** : Impression, Photocopie ou Scan.
   - **Format** : A4, A3, A5, Lettre.
   - **Couleur** : Noir et blanc ou Couleur.
   - **Quantité** : Nombre d'exemplaires.
   - **Pages** :
     - « Toutes les pages » : Impression de tout le document.
     - « Plage de pages » : Spécifiez la page de début et la page de fin.
   - **Mode de paiement** : En ligne ou à la réception.
   - **Notes** : Remarques pour l'imprimeur (optionnel).
5. Vérifiez le prix calculé automatiquement.
6. Cliquez sur « Soumettre la commande ».

### 3.3 Mes commandes

La page « Mes commandes » liste toutes vos commandes. Vous pouvez :
- Rechercher une commande par référence ou par nom de fichier.
- Filtrer les commandes par statut (en attente, acceptée, en cours, terminée, livrée).
- Ouvrir le détail d'une commande.

### 3.4 Détail d'une commande

Depuis le détail d'une commande, vous pouvez :
- Voir toutes les informations (type de service, format, couleur, quantité, etc.).
- Voir l'imprimeur assigné (nom, adresse, téléphone).
- Suivre l'historique des statuts (avec dates et auteur du changement).
- Si le paiement est en attente, payer en ligne.
- Télécharger le reçu de paiement (si le paiement a été effectué).

> **Remarque** : Les statuts se mettent à jour automatiquement toutes les 5 secondes, pas besoin d'actualiser la page !

### 3.5 Paiements

La page « Paiements » vous permet de consulter l'historique de vos paiements et de télécharger les reçus associés.

---

## 4. Guide pour les imprimeurs

### 4.1 Validation du compte

Lorsque vous créez un compte imprimeur, vous devez attendre qu'un administrateur valide votre compte avant d'avoir accès aux fonctionnalités d'imprimeur. Vous verrez un message d'attente sur votre tableau de bord tant que la validation n'est pas effectuée.

### 4.2 Tableau de bord

Le tableau de bord imprimeur vous montre :
- Le nombre de commandes disponibles.
- Le nombre de commandes à traiter.
- Le nombre de commandes terminées aujourd'hui.
- Un interrupteur pour activer/désactiver la réception de nouvelles commandes.

### 4.3 Commandes disponibles

Cette page liste toutes les commandes en attente d'acceptation (statut « pending ») et non assignées à un imprimeur.

Pour accepter une commande :
1. Cliquez sur la commande pour voir son détail.
2. Vérifiez les informations (type de service, fichier, montant, notes).
3. Cliquez sur « Accepter la commande ».

> **Important** : Une commande acceptée est exclusivement attribuée à vous. Les autres imprimeurs ne pourront plus la voir.

### 4.4 Mes commandes

Cette page liste toutes les commandes que vous avez acceptées. Vous pouvez :
- Voir le statut de chaque commande.
- Ouvrir le détail d'une commande.

### 4.5 Mettre à jour le statut d'une commande

Depuis le détail d'une commande que vous avez acceptée, vous pouvez mettre à jour le statut :
- **En cours** : Vous avez commencé le traitement.
- **Terminée** : Le traitement est terminé.
- **Livrée** : La commande a été livrée au client.

Chaque changement de statut est enregistré dans l'historique et une notification est envoyée au client.

### 4.6 Paramètres

#### 4.6.1 Tarifs

Vous pouvez définir vos propres tarifs pour chaque combinaison de type de service, format et couleur :
1. Allez dans la page « Tarifs ».
2. Ajoutez une nouvelle règle tarifaire ou modifiez une règle existante.
3. Cliquez sur « Enregistrer ».

#### 4.6.2 Disponibilités

Vous pouvez définir vos heures d'ouverture par jour de la semaine :
1. Allez dans la page « Disponibilités ».
2. Sélectionnez un jour et indiquez l'heure de début et de fin.
3. Cliquez sur « Enregistrer ».

### 4.7 Statistiques

La page « Statistiques » vous montre des graphiques sur vos commandes et vos revenus.

### 4.8 Paiements

La page « Paiements » vous permet de consulter les reçus de paiement des commandes que vous avez traitées et de les télécharger.

---

## 5. Guide pour les administrateurs

### 5.1 Tableau de bord

Le tableau de bord admin vous montre :
- Le nombre d'utilisateurs, d'imprimeurs et de commandes.
- Les revenus totaux.
- Des graphiques sur les statuts des commandes et les revenus mensuels.

### 5.2 Gestion des utilisateurs

La page « Utilisateurs » vous permet de :
- Voir tous les utilisateurs (clients, imprimeurs, admins).
- Rechercher et filtrer par rôle.
- Activer/désactiver un compte.
- Valider un compte imprimeur (cliquer sur « Valider »).
- Révoquer la validation d'un compte imprimeur (cliquer sur « Révoquer »).

### 5.3 Supervision des commandes

La page « Commandes » vous permet de voir toutes les commandes de la plateforme et de les supprimer si nécessaire.

### 5.4 Support

La page « Support » vous permet de voir les tickets de support créés par les clients et d'y répondre.

---

## 6. FAQ

### Q : Quels formats de fichiers sont acceptés ?
R : Les formats acceptés sont PDF, DOC, DOCX, JPG, JPEG et PNG.

### Q : Quelle est la taille maximale par fichier ?
R : La taille maximale par fichier est de 20 Mo.

### Q : Comment suivre l'avancement de ma commande ?
R : Ouvrez le détail de votre commande ; les statuts se mettent à jour automatiquement toutes les 5 secondes.

### Q : Un imprimeur peut-il refuser une commande après l'avoir acceptée ?
R : Non, une commande acceptée ne peut pas être refusée. Si vous rencontrez un problème, contactez le support.

### Q : Comment valider mon compte imprimeur ?
R : Votre compte est validé par un administrateur. Vous recevrez une notification une fois que c'est fait.

---

*PrintEasy © 2026*
