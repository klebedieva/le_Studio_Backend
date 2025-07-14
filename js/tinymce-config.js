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
    file_picker_types: 'image', // Autorise uniquement la sélection d’images

    /// Options pour l’insertion d’images
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

    // Fonction déclenchée lors du choix d’une image (input file + upload)
    file_picker_callback: function (callback, value, meta) {
      // Vérifie qu’il s’agit bien d’une image
      if (meta.filetype === 'image') {
        const input = document.createElement('input');
        input.setAttribute('type', 'file');
        input.setAttribute('accept', 'image/*');

        // Quand un fichier est choisi :
        input.onchange = function () {
          const file = this.files[0];
          const formData = new FormData();
          formData.append('file', file);

          // Envoie du fichier à upload-image.php via fetch
          fetch('admin/articles/upload-image.php', {
            method: 'POST',
            body: formData
          })
            .then(response => response.json())
            .then(data => {
              // Si upload OK, insère l’image dans l’éditeur
              if (data.location) {
                callback(data.location, { title: file.name });
              } else {
                alert("Erreur : image non enregistrée.");
              }
            })
            .catch(() => alert("Erreur réseau lors de l'envoi de l'image."));
        };

        // Ouvre le sélecteur de fichier
        input.click();
      }
    }
  });
}