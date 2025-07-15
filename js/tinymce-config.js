// Fonction d'initialisation de l’éditeur TinyMCE pour les zones de texte
function initTinyMCE(selector) {
  // Supprime l’éditeur s’il est déjà initialisé sur ce champ
  if (tinymce.get(selector)) tinymce.get(selector).remove();

  // Configuration de TinyMCE
  tinymce.init({
    selector: `#${selector}`, // Cible le champ avec l’ID passé en paramètre
    plugins: 'lists image link media code table preview',   // Plugins activés (liste à puces, images, lien, aperçu, etc.)
    toolbar: 'undo redo | bold italic underline | bullist numlist | alignleft aligncenter alignright | image link media table preview | code', // Barre d’outils personnalisée
    height: 400, // Hauteur de l’éditeur
    menubar: false, // Masque le menu du haut (Fichier, Édition…)
    readonly: false, // Champ éditable
    content_style: "body { font-family:Helvetica,Arial,sans-serif; font-size:14px }", // Style par défaut du contenu édité
    automatic_uploads: true, // Active le téléchargement automatique d’images

    // Options pour l’insertion d’images
    image_title: true,
    image_dimensions: true,
    image_advtab: true,

    // Liste des classes CSS proposées pour les images
    image_class_list: [
      { title: 'Responsive', value: 'img-fluid' },
      { title: 'Aucun', value: '' }
    ],

    // Autorise seulement certains éléments HTML sécurisés
    valid_elements: 'p,h1,h2,h3,strong/b,em/i,u,ul,ol,li,a[href|target],img[src|alt|width|height|class],table,tr,td,th,thead,tbody,tfoot,br,span[style],div[style|class]',

    // Permet aussi certains éléments complexes (iframe, vidéo, etc.)
    extended_valid_elements: 'iframe[src|frameborder|style|scrolling|class|width|height|name|align],video[src|type|controls|width|height],source[src|type]',

    // Empêche l’insertion de balises dangereuses
    invalid_elements: 'script,style,object,embed,form,input,textarea,button',
    
    // Gestionnaire d'upload d'images
    images_upload_handler: function (blobInfo) {
      // Cette fonction retourne une promesse utilisée par TinyMCE
      return new Promise((resolve, reject) => {
        var formData = new FormData(); // Prépare un objet FormData pour envoyer le fichier
        formData.append('file', blobInfo.blob(), blobInfo.filename()); // Ajoute le fichier reçu depuis TinyMCE et son nom
        // Envoie la requête POST vers le script PHP
        fetch('admin/articles/upload-image.php', {
          method: 'POST',
          body: formData
        })
          // Récupère la réponse sous forme de texte
          .then(response => response.text())
          // Traite la réponse reçue
          .then(text => {
            let data;
            try {
              data = JSON.parse(text); // Convertit la réponse en JSON
            } catch (e) {
              reject('Réponse serveur non valide'); // Si le format JSON est invalide
              return;
            }
            // Si le JSON contient un champ location, on considère que l’image est enregistrée
            if (data && data.location) {
              resolve(data.location);
            // Si le JSON contient un champ error, on renvoie cette erreur
            } else if (data && data.error) {
              reject(data.error);
            // Sinon, on renvoie une erreur générique
            } else {
              reject('Erreur : image non enregistrée.');
            }
          })
          // Si la requête réseau échoue
          .catch(() => {
            reject("Erreur réseau lors de l'envoi de l'image.");
          });
      });
    },

    // Fonction déclenchée lors du choix d’une image (input file + upload)
    file_picker_callback: function (callback, value, meta) {
      // Vérifie si le type de fichier demandé est une image
      if (meta.filetype === 'image') {
        // Crée un champ input de type file
        const input = document.createElement('input');
        input.setAttribute('type', 'file');
        input.setAttribute('accept', 'image/*');
        // Action quand l’utilisateur choisit un fichier
        input.onchange = function () {
          const file = this.files[0]; // Récupère le fichier choisi
          const formData = new FormData();
          formData.append('file', file); // Ajoute le fichier dans FormData
          // Envoie la requête POST vers le script d’upload
          fetch('admin/articles/upload-image.php', {
            method: 'POST',
            body: formData
          })
            // Récupère la réponse en texte
            .then(response => response.text())
            // Analyse la réponse reçue
            .then(text => {
              let data;
              try {
                data = JSON.parse(text); // Convertit la réponse en JSON
              } catch (e) {
                alert('Réponse serveur non valide');
                return;
              }
              // Si le JSON contient un chemin, insère l’image dans l’éditeur
              if (data && data.location) {
                callback(data.location, { title: file.name, style: 'max-width:100%;height:auto;' });
              // Si le JSON contient une erreur, affiche un message
              } else if (data && data.error) {
                alert(data.error);
              // Sinon affiche une erreur générique
              } else {
                alert('Erreur : image non enregistrée.');
              }
            })
            // Gestion d’une erreur réseau
            .catch(() => alert("Erreur réseau lors de l'envoi de l'image."));
        };
        // Ouvre la boîte de dialogue pour choisir un fichier
        input.click();
      }
    }
  });
}