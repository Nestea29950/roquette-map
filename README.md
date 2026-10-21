# Roquette Map

Cartes Leaflet via le **bloc « Roquette Map »** (éditeur de blocs, réglages dans la colonne de droite) ou le shortcode :

```
[roquette_map cpt="evenements,restaurants" fond="positron" evenements="pin-rouge.png"]
```

## Je veux modifier…

| Quoi | Où |
|---|---|
| Le contenu HTML de la popup | `features/popup/popup.twig` |
| Le style de la popup | `features/popup/popup.css` |
| Le comportement de la popup (fermeture, décalage) | `features/popup/popup.js` |
| La hauteur / taille de la carte | `features/carte/carte.css` |
| L'initialisation de la carte (zoom, cadrage) | `features/carte/carte.js` |
| Les paramètres du shortcode | `features/carte/Carte.php` |
| Quels posts deviennent des pins (requête, métas `lat`/`lng`) | `features/pins/Pins.php` |
| Les champs Localisation (ACF) des posts | `features/localisation/Localisation.php` |
| Afficher seulement certains posts | bloc : panneaux « Posts : … » (ou shortcode `posts="12,34"`) |
| Le bloc (réglages dans l'éditeur) | `features/bloc/editor.js` (+ `block.json`, `Bloc.php`) |
| Ajouter une image de pin | déposer le fichier dans `features/pins/images/` |
| Le pin par défaut | `features/pins/images/default.png` |
| Ajouter / retirer un fond de carte | `features/fond/fonds.php` |
| La page admin « Roquette Map » | `features/admin/dashboard.php` (+ `admin.css`, `admin.js`) |

## Images des pins

| Shortcode | Résultat |
|---|---|
| *(rien)* | tous les pins = `default.png` |
| `pin="pin-rouge.png"` | tous les pins de la carte en rouge |
| `evenements="pin-rouge.png" restaurants="pin-bleu.png"` | une image par CPT, les autres = `default.png` |
| `pin="pin-vert.png" evenements="pin-rouge.png"` | evenements en rouge, tout le reste en vert |
| `evenements="pin-rouge.png,concert:pin-bleu.png,expo:pin-or.png"` | événements « concert » en bleu, « expo » en or, les autres en rouge |

Chaque CPT listé dans `cpt` peut avoir **son propre paramètre, du même nom que le CPT**, qui contient ses réglages séparés par des virgules (ex. `evenements="pin-rouge.png,…"`). - le pin du CPT : l'élément qui se termine par `.png`, `.jpg`, `.gif`, `.webp` ou `.svg` ;
- un pin par terme : `slug-du-terme:image.png` (slug d'un terme de n'importe quelle taxonomie du CPT). Si un post a plusieurs de ces termes, le premier listé l'emporte. Dans le bloc : panneau « Pins par terme : … ».

Priorité : terme > CPT > `pin` > `default.png`.

Les images sont cherchées dans `features/pins/images/`. Un nom inconnu retombe sur `pin`, puis sur `default.png`. La pointe du pin = le bas, au centre de l'image ; la taille est celle du fichier.

## Organisation

Un dossier par fonctionnalité dans `features/`. Chaque dossier contient **tout** ce qui la concerne : PHP, vue, CSS, JS.

```
roquette-map.php        Point d'entrée.
core/                   Plomberie (autoloader, moteur de vues, Leaflet). Rarement à toucher.
features/
  carte/                Le shortcode [roquette_map] et le conteneur de la carte.
    Carte.php           Lit les paramètres, assemble fond + pins.
    conteneur.php       HTML du conteneur.
    carte.js / .css     Initialisation Leaflet / taille de la carte.
  pins/                 Paramètres cpt + pin : un pin par publication ayant lat/lng.
    Pins.php
    images/             Images des pins (default.png = pin par défaut).
  popup/                Ce qui s'ouvre au clic sur un pin.
    popup.twig          Structure (variable `post`, un Timber\Post).
    popup.css           Style.
    popup.js            Options Leaflet + fermeture via [data-roquette-popup-close].
    popup-reset.css     Neutralise le style Leaflet par défaut. Ne pas toucher.
    Popup.php           Rendu Twig.
  fond/                 Paramètre fond.
    fonds.php           Liste des fonds disponibles.
    Fond.php
  admin/                Page de documentation dans l'admin WordPress.
lib/leaflet/            Leaflet 1.9.4 en local (pas de CDN).
```

### Ajouter une fonctionnalité

1. Créer `features/ma-fonctionnalite/` avec sa classe `MaFonctionnalite.php` (namespace `RoquetteMap\Features\MaFonctionnalite`).
2. Y mettre ses vues, CSS et JS.
3. L'instancier dans `core/Plugin.php`.

> Windows ne distingue pas `Carte.php` de `carte.php` : ne jamais donner à une vue le même nom que la classe du dossier.

## Publier une mise à jour

1. Monter la version dans `roquette-map.php` (en-tête `Version:` **et** `ROQUETTE_MAP_VERSION`), commit + push.
2. Créer le zip : `git archive --prefix=roquette-map/ -o roquette-map.zip HEAD`
3. GitHub → Releases → Draft a new release : tag `v1.3.0` (= la version), joindre `roquette-map.zip`, Publish.

Les sites voient la mise à jour dans Extensions (sous 12 h, ou Tableau de bord → Mises à jour → « Vérifier à nouveau »).
Une mise à jour remplace tout le dossier du plugin : ne pas modifier les fichiers directement sur un site.
