// On récupère les champs du formulaire par leur ID
const inputName   = document.getElementById("nom");
const inputPrenom = document.getElementById("prenom");
const inputTel    = document.getElementById("tel");
const inputEmail  = document.getElementById("email");
const inputSujet  = document.getElementById("sujet");
const inputMsg    = document.getElementById("msg");
const inputPassword = document.getElementById("mot_de_passe"); 
const form        = document.querySelector("form"); // le formulaire

// Liste des champs à valider avec leurs règles
const inputs = [
  {
    element: inputName,
    regex: /^[a-zA-Z\s-]+$/, // lettres, espaces, tirets
    message: "Le nom est invalide",
    requiredMessage: "Le nom est obligatoire"
  },
  {
    element: inputPrenom,
    regex: /^[a-zA-Z\s-]+$/,
    message: "Le prénom est invalide",
    requiredMessage: "Le prénom est obligatoire"
  },
  {
    element: inputEmail,
    regex: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/, // format email
    message: "L'email est invalide",
    requiredMessage: "L'email est obligatoire"
  },
  {
    element: inputTel,
    regex: /^[0-9]{10}$/, // numéro français à 10 chiffres
    message: "Le téléphone est invalide"
    // champ facultatif
  },
  {
    element: inputSujet,
    regex: /^[a-zA-Zà-úÀ-Ú\s-]*$/, // lettres accentuées + tirets
    message: "Le sujet est invalide"
    // champ facultatif
  },
  {
    element: inputMsg,
    regex: /^(?!.*<.*?>)[\s\S]{10,1000}$/, // min 10 caractères, sans balises HTML
    message: "Le message doit contenir au moins 10 caractères",
    requiredMessage: "Le message est obligatoire"
  }
];

if (window.location.pathname.includes("registration.php")) {
  inputs.push({
    element: inputPassword,
    regex: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/,
    message: "Le mot de passe doit contenir au moins 8 caractères, une majuscule, un chiffre et un symbole spécial.",
    requiredMessage: "Le mot de passe est obligatoire"
  });
}

// Validation en direct quand on tape dans un champ
inputs.forEach(input => {
  if (!input.element) return; // si l'élément n'existe pas, on ne fait rien

  input.element.addEventListener("input", () => {
    const el = input.element;
    const value = el.value.trim();
    let errorDiv = el.nextElementSibling;

    // Supprime l'ancien message d'erreur s’il existe
    if (errorDiv && errorDiv.classList.contains("text-danger-js")) {
      errorDiv.remove();
    }

    // Si le champ est vide
    if (value === "") {
      if (input.requiredMessage) {
        el.classList.remove("is-valid");
        el.classList.add("is-invalid");

        const error = document.createElement("div");
        error.classList.add("text-danger", "text-danger-js");
        error.textContent = input.requiredMessage;
        el.after(error);
      } else {
        el.classList.remove("is-invalid");
        el.classList.remove("is-valid");
      }
    }
    // Si le champ ne correspond pas à la regex
    else if (!input.regex.test(value)) {
      el.classList.remove("is-valid");
      el.classList.add("is-invalid");

      const error = document.createElement("div");
      error.classList.add("text-danger", "text-danger-js");
      error.textContent = input.message;
      el.after(error);
    }
    // Si tout est bon
    else {
      el.classList.remove("is-invalid");
      el.classList.add("is-valid");
    }
  });
});

// Validation quand on envoie le formulaire
if (form) { // vérifie que le formulaire existe
  form.addEventListener("submit", function (e) {
    let allValid = true;

    inputs.forEach(input => {
      if (!input.element) return; // si l'élément n'existe pas, on l'ignore

      const el = input.element;
      const value = el.value.trim();
      let errorDiv = el.nextElementSibling;

      // Supprimer les anciens messages d'erreur
      if (errorDiv && errorDiv.classList.contains("text-danger-js")) {
        errorDiv.remove();
      }

      // Vérifie si le champ est vide
      if (value === "") {
        if (input.requiredMessage) {
          el.classList.remove("is-valid");
          el.classList.add("is-invalid");

          const error = document.createElement("div");
          error.classList.add("text-danger", "text-danger-js");
          error.textContent = input.requiredMessage;
          el.after(error);
          allValid = false;
        }
      }
      // Vérifie si la valeur respecte la règle
      else if (!input.regex.test(value)) {
        el.classList.remove("is-valid");
        el.classList.add("is-invalid");

        const error = document.createElement("div");
        error.classList.add("text-danger", "text-danger-js");
        error.textContent = input.message;
        el.after(error);
        allValid = false;
      }
      // Tout est bon
      else {
        el.classList.remove("is-invalid");
        el.classList.add("is-valid");
      }
    });

    // Si un champ n'est pas valide, on empêche l'envoi
    if (!allValid) {
      e.preventDefault();
      alert("Veuillez corriger les erreurs avant de soumettre le formulaire.");
    }
  });
}
