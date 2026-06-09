# Cahier des charges — PrintEasy

**Plateforme web de gestion des services d'impression, photocopie et scan**

---

## Informations générales

| Champ | Valeur |
|-------|--------|
| **Nom du projet** | PrintEasy — Plateforme web de gestion des services d'impression, photocopie et scan |
| **Type de projet** | Projet académique / professionnel |
| **Personne à contacter** | [À compléter] |
| **Adresse** | [À compléter] |
| **Téléphone** | [À compléter] |
| **E-mail** | [À compléter] |

---

## Sommaire

- I. [Présentation du projet](#i-présentation-du-projet)

- II. [Réalisation du projet](#ii-réalisation-du-projet)
  - A. [Contexte général](#a-contexte-général)
  - B. [Problématique](#b-problématique)
  - C. [Objectifs](#c-objectifs)

- III. [Arborescence](#iii-arborescence)
  - A. [Rôle : Client](#a-arborescence--rôle-client-utilisateur)
  - B. [Rôle : Imprimeur](#b-arborescence--rôle-imprimeur)
  - C. [Rôle : Administrateur](#c-arborescence--rôle-administrateur)
  - D. [Synthèse des rôles](#synthèse-des-rôles-sur-la-plateforme)

- IV. [Fonctionnalités](#iv-fonctionnalités)

- V. [Technologie & outils](#v-technologie--outils-stack-laravel--react)

---

## I. Présentation du projet

**PrintEasy** est une plateforme web destinée à digitaliser et centraliser les services d'impression, de photocopie et de numérisation (scan). Elle met en relation des clients (étudiants, particuliers, professionnels) et des imprimeurs (boutiques, reprographies, espaces de coworking) via une interface moderne, sécurisée et accessible depuis un navigateur.

La plateforme vise à :

- Réduire les files d'attente et les déplacements inutiles
- Offrir un suivi transparent des commandes en temps réel
- Permettre aux imprimeurs de gérer leur activité (tarifs, disponibilités, commandes)
- Garantir une attribution équitable des commandes (premier arrivé, premier servi)

PrintEasy s'adresse à tout écosystème nécessitant des services documentaires rapides : campus universitaires, centres d'affaires, bibliothèques, reprographies urbaines, etc.

---

## II. Réalisation du projet

### A. Contexte général

Dans un environnement de plus en plus numérique, le besoin d'imprimer, photocopier ou scanner des documents reste fréquent (cours, dossiers administratifs, contrats, affiches). Cependant, les processus traditionnels (déplacement sur place, attente, paiement manuel, absence de suivi) génèrent perte de temps, manque de visibilité et difficultés de coordination entre clients et prestataires.

PrintEasy répond à ce besoin en proposant un espace unique où le client dépose ses fichiers en ligne, choisit ses options (format, couleur, quantité, pages), paie si nécessaire, et suit l'état de sa commande ; tandis que l'imprimeur consulte une file de commandes disponibles, accepte celles qu'il peut traiter, et met à jour les statuts jusqu'à la livraison.

### B. Problématique

Aujourd'hui, la gestion des demandes d'impression souffre de plusieurs limites :

- Absence de plateforme centralisée pour passer commande à distance
- Files d'attente physiques et manque de visibilité sur les délais
- Difficulté pour les imprimeurs à prioriser et répartir la charge de travail
- Risque de double attribution si plusieurs opérateurs acceptent la même commande
- Suivi client insuffisant (qui traite ma commande ? à quel stade est-elle ?)
- Tarification peu transparente ou variable selon les prestataires

Il devient essentiel de disposer d'un outil numérique structuré, fiable et évolutif qui centralise les commandes, automatise le calcul des prix, sécurise les paiements et trace chaque étape du traitement.

### C. Objectifs

Les objectifs de la plateforme PrintEasy sont les suivants :

- Permettre aux clients de soumettre des commandes en ligne (upload PDF, DOCX, images)
- Calculer automatiquement le prix selon le service, le format, la couleur et le volume
- Offrir un système d'attribution des commandes aux imprimeurs validés (file d'attente, acceptation atomique, premier arrivé premier servi)
- Garantir qu'une commande acceptée n'est plus disponible pour les autres imprimeurs
- Permettre au client de voir l'imprimeur en charge et l'historique des statuts
- Proposer un espace imprimeur (tarifs, disponibilités, statistiques, historique)
- Proposer un espace administrateur (utilisateurs, validation imprimeurs, supervision)
- Intégrer des paiements en ligne simulés et paiement à la réception
- Notifier les acteurs des changements d'état (notifications in-app et e-mail simulé)
- Créer un environnement évolutif, maintenable et documenté (API REST, rôles, logs)

---

## III. Arborescence

### A. Arborescence – Rôle : Client (Utilisateur)

| Rubrique | Sous-rubrique | Description |
|----------|---------------|-------------|
| Accueil (public) | Page d'accueil | Présentation PrintEasy, services, CTA inscription |
| | Connexion / Inscription | Accès compte client ou imprimeur |
| | Espace imprimeur (lien) | Redirection vers inscription imprimeur |
| Tableau de bord | Vue d'ensemble | Résumé des commandes récentes, accès rapides |
| Commandes | Nouvelle commande | Upload **plusieurs fichiers** (PDF/DOC/DOCX/JPG/PNG, max 20 Mo par fichier), choix service/format/couleur, quantité, **choix plage de pages** (toutes les pages ou page de début/fin), mode de paiement, notes |
| | Mes commandes | Liste paginée, recherche, filtres par statut |
| | Détail commande | Infos commande, imprimeur assigné, historique statuts, paiement en ligne si applicable |
| Paiements | Historique paiements | Liste des transactions et reçus |
| Notifications | Liste notifications | Alertes statut commande, acceptation imprimeur |
| Profil | Informations personnelles | Nom, e-mail, téléphone, mot de passe |
| Support | Tickets support | Création et suivi de demandes d'aide |
| Authentification | Inscription client | Création compte rôle « client » |
| | Connexion / Déconnexion | Authentification Sanctum (token API) |
| | Mot de passe oublié | Réinitialisation par e-mail (simulé) |

### B. Arborescence – Rôle : Imprimeur

| Rubrique | Sous-rubrique | Description |
|----------|---------------|-------------|
| Tableau de bord | Vue d'ensemble | Commandes disponibles (file), à traiter, en cours, terminées du jour, disponibilité on/off |
| | Alerte validation | Message si compte en attente d'approbation admin |
| Commandes | Commandes disponibles | File des commandes pending non assignées (consultation avant acceptation) |
| | Détail + Accepter | Voir détails client/fichier/options, bouton « Accepter la commande » (attribution exclusive) |
| | Mes commandes attribuées | Commandes acceptées par l'imprimeur connecté |
| | Mise à jour statuts | accepted → in_progress → completed → delivered |
| Tarification | Grille tarifaire | Prix par service, format, mode couleur (N&B/couleur) |
| Disponibilités | Horaires hebdomadaires | Jours et plages horaires de disponibilité |
| | Toggle disponibilité | Activer/désactiver la réception de nouvelles commandes |
| Historique | Commandes passées | Consultation des commandes traitées |
| Statistiques | Tableaux & graphiques | Répartition par statut, revenus, évolution mensuelle |
| Notifications | Alertes | Nouvelles commandes, changements de statut |
| Profil | Fiche imprimerie | Nom, adresse, téléphone, compte |

### C. Arborescence – Rôle : Administrateur

| Rubrique | Sous-rubrique | Description |
|----------|---------------|-------------|
| Tableau de bord | Vue globale | Utilisateurs, imprimeurs, commandes, revenus, graphiques par statut et par mois |
| Utilisateurs | Liste utilisateurs | Recherche, filtres par rôle, pagination |
| | Activer / Désactiver | Gestion `is_active` des comptes |
| | Valider imprimeur | Approbation `is_approved` (accès file commandes) |
| Commandes | Supervision commandes | Vue globale, suppression si nécessaire |
| Paiements | Suivi paiements | Historique des transactions plateforme |
| Support | Tickets | Réponse aux demandes utilisateurs |
| Statistiques | Indicateurs clés | Commandes pending, complétées, revenus mensuels |

### Synthèse des rôles sur la plateforme

| Fonctionnalités principales | Client (Utilisateur) | Imprimeur (Prestataire) | Administrateur (Superviseur) |
|----------------------------|----------------------|-------------------------|------------------------------|
| Inscription / Connexion | Créer compte client, se connecter | Créer compte imprimeur (validation admin requise) | Gérer comptes, rôles, activer/désactiver, valider imprimeurs |
| Commandes | Créer commande, suivre statuts, voir imprimeur assigné | Consulter file disponible, accepter (1 seul gagnant), traiter, livrer | Superviser toutes les commandes, supprimer si besoin |
| Tarification | Voir prix calculé auto | Définir ses tarifs par service/format | — |
| Attribution | — | Premier arrivé, premier servi (transaction verrouillée) | — |
| Paiements | Payer en ligne ou à réception | — | Suivi global revenus |
| Notifications | Recevoir alertes statut / accept. | Recevoir alertes commandes | — |
| Statistiques | Historique personnel | Stats personnelles, revenus, graphiques | Stats globales plateforme |
| Support | Ouvrir ticket | — | Répondre aux tickets |
| Sécurité | Profil sécurisé | Compte validé requis | Modération comptes |

---

## IV. Fonctionnalités

### A. Client (Utilisateur)

| Rubrique | Fonctionnalité | Descriptif |
|----------|----------------|------------|
| Compte & Profil | Création de compte / Connexion | Inscription e-mail, mot de passe (min. 8 car.), authentification API Sanctum (Bearer token) |
| | Gestion profil | Modifier nom, téléphone, mot de passe |
| Commandes | Nouvelle commande | Upload **plusieurs fichiers** (PDF/DOC/DOCX/JPG/PNG, max 20 Mo par fichier), aperçu des fichiers avant validation, suppression de fichiers sélectionnés, choix impression/photocopie/scan, format A4/A3/A5/letter, N&B ou couleur, quantité, **choix plage de pages** (toutes les pages ou page de début/fin) |
| | Calcul prix automatique | Tarification dynamique avant soumission |
| | Suivi commande | Statuts : en attente, acceptée, en cours, terminée, livrée, refusée, annulée ; **mise à jour automatique en temps réel** via polling (rafraîchissement toutes les 5 secondes) |
| | Imprimeur en charge | Affichage nom, adresse, téléphone, date d'acceptation après attribution |
| Paiements | Mode de paiement | En ligne (simulé : Mobile Money, Stripe) ou à la réception |
| | Historique & reçu | Liste paiements, **génération automatique du reçu PDF lors du paiement**, téléchargement du reçu depuis la page paiements et depuis le détail de la commande |
| Recherche & Liste | Mes commandes | Recherche par référence/fichier, filtre statut, pagination |
| Notifications | Alertes in-app | Changement de statut, commande acceptée |
| Support | Ticket support | Envoi de demandes d'assistance liées à une commande |
| Sécurité | Session sécurisée | Token stocké localement, déconnexion, 401 géré |

### B. Imprimeur (Gestionnaire de service d'impression)

| Rubrique | Fonctionnalité | Descriptif |
|----------|----------------|------------|
| Compte | Inscription imprimeur | Compte créé avec `is_approved = false` ; message d'attente validation admin |
| | Validation admin | Obligatoire pour accéder aux commandes disponibles |
| Tableau de bord | Vue d'ensemble | Nombre commandes en file, à traiter, en cours, terminées aujourd'hui ; toggle disponibilité |
| Attribution | File commandes disponibles | Liste des commandes pending sans imprimeur |
| | Consultation détail | Client, service, fichier, montant, notes |
| | Accepter commande | `POST /printer/orders/{id}/accept` — attribution exclusive avec verrouillage BDD (`lockForUpdate`). En cas de conflit simultané : erreur 409 |
| | Mes commandes | Uniquement les commandes dont `printer_id` = moi |
| Traitement | Mise à jour statuts | Démarrer (`in_progress`), terminer (`completed`), marquer livrée (`delivered`) |
| Tarification | Grille tarifaire | CRUD tarifs par service/format/couleur |
| Disponibilités | Horaires | Définition par jour de la semaine (0-6) |
| | Disponibilité globale | `is_available` : masque la file si indisponible |
| Statistiques | Indicateurs | Graphiques Recharts : statuts, revenus mensuels |
| Historique | Commandes passées | Consultation des commandes traitées |
| Notifications | Alertes | Commande acceptée, changements de statut ; mise à jour automatique via polling |
| Paiements | Accès aux reçus | Consultation et téléchargement des reçus des commandes traitées |

### C. Administrateur (Superviseur)

| Rubrique | Fonctionnalité | Descriptif |
|----------|----------------|------------|
| Tableau de bord | Dashboard global | Compteurs utilisateurs/imprimeurs/commandes, revenus, graphiques par statut et par mois |
| Gestion utilisateurs | Liste & recherche | Filtrer par rôle (client, imprimeur, admin) |
| | Activer / Désactiver | Champ `is_active` |
| | Valider / Révoquer | Champ `is_approved` pour les imprimeurs |
| | Suppression | Suppression compte (sauf dernier admin) |
| Commandes | Supervision | Vue toutes commandes, suppression possible |
| Support | Réponse tickets | Interface de réponse aux demandes |
| Statistiques | KPI plateforme | Commandes pending/complétées, revenus globaux |

---

## V. Technologie & outils (Stack Laravel + React)

| Rubrique / Couche | Technologie / Outil | Description / Utilisation dans le projet |
|-------------------|---------------------|------------------------------------------|
| **Frontend** | React 19 | Interface SPA, composants, routing (React Router) |
| | Vite 8 | Build tool, dev server, proxy API (`/api` → `:8000`) |
| | Tailwind CSS v4 | Design responsive, thème, composants UI |
| | Axios | Client HTTP, intercepteurs token, FormData upload |
| | React Hot Toast | Notifications utilisateur |
| | Recharts | Graphiques dashboard admin et imprimeur |
| | Lucide React | Iconographie interface |
| **Backend** | PHP 8.2+ / Laravel 11 | API REST, logique métier, ORM Eloquent |
| | Laravel Sanctum | Authentification par token API |
| | Services métier | OrderService, OrderAssignmentService, PricingService, NotificationService, PrinterSetup |
| **Base de données** | MySQL 8 / SQLite (dev) | Users, orders, pricing_rules, payments, notifications, support_tickets, historiques, **order_files** |
| | Migrations & Seeders | Schéma versionné, données de démonstration |
| **API** | REST API v1 | Préfixe `/api/v1`, JSON, validation Laravel, réponses 401/403/409/422 structurées |
| **Fichiers** | Laravel Storage (public) | Stockage documents commandes (`orders/`) |
| **Paiement** | Stripe / Mobile Money | Intégration simulée pour démonstration |
| **Notifications** | E-mail (driver log) | Envoi simulé + notifications in-app (BDD) |
| **Conteneurisation** | Docker Compose | MySQL + API (optionnel développement) |
| **Sécurité** | HTTPS (production) | Chiffrement transit ; CORS configuré |
| | Rôles middleware | Middleware `role:printer`, `role:admin` |
| | Validation entrées | Règles strictes (extensions fichier) |
| **Gestion de projet** | Git | Versioning du code source |
| **Documentation** | README.md | Installation, comptes démo, endpoints API |

---

*PrintEasy © 2025 — Projet académique*
