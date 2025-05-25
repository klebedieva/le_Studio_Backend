            const inputName = document.getElementById("nom");
            const inputPrenom = document.getElementById("prenom");
            const inputTel = document.getElementById("tel");
            const inputEmail = document.getElementById("email");
            const inputSujet = document.getElementById("sujet")
            const inputMsg = document.getElementById("msg")
            const form = document.querySelector("form");

            // Regex expression régulière partielle utilisée pour valider
            const inputs = [
                {
                    element : inputName,
                    regex : /^[a-zA-Z\s-]+$/,
                    message : "Le nom est invalide"
                },
                {
                    element : inputPrenom,
                    regex : /^[a-zA-Z\s-]+$/,
                    message : "Le prénom est invalide"

                },
                {
                    element : inputEmail,
                    regex : /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/,
                    message : "L'email est invalide"
                },
                {
                    element : inputTel,
                    regex : /^[0-9]{10}$/,
                    message : "Le téléphone est invalide"
                },
                {
                    element: inputSujet,
                    regex: /^[a-zA-Zà-úÀ-Ú\s-]+$/, // Permet les accents français
                    message: "Le sujet est requis"
                },
                {
                    element : inputMsg,
                    regex: /^(?!.*<.*?>)[\s\S]{10,1000}$/, /*
                    == (?!.*<.*?>) interdit les balises HTML (par exemple, <p>, <div>, <script>) ==
                    == [\s\S] atorise les caractères, y compris les espaces (\s) et non-espaces (\S) ==
                    */
                    message : "Le message est invalide"
                }
            ]
              
            inputs.forEach(input => {
                    input.element.addEventListener("input", function (e) {
                        RegexTest(this, input.regex, input.message);
                    });
            });
            
             function RegexTest(input, regex, message = "Le champ est invalide") {
                const inputValue = input.value;

                // Vérifie s'il y a déjà un message d'erreur juste après l'input
                
                // Récupère l'élément suivant de l'input
                let errorDiv = input.nextElementSibling;
                // Vérifie si l'élément suivant est n'est pas une div
                if (!errorDiv) {
                    errorDiv = null; // Réinitialise errorDiv si ce n'est pas une div
                }
                
                if (regex.test(inputValue)) {
                    input.classList.remove("is-invalid");
                    input.classList.add("is-valid");

                    // Si un message d’erreur est déjà affiché, on le supprime
                    if (errorDiv) errorDiv.remove();
                } 
                else if (inputValue.length === 0) {
                    input.classList.remove("is-valid");
                    input.classList.remove("is-invalid");
                    // Si un message d’erreur est déjà affiché, on le supprime
                    if (errorDiv) errorDiv.remove();
                }
                else {
                    input.classList.remove("is-valid");
                    input.classList.add("is-invalid");
                    // Si le message d'erreur n'existe pas, on le crée
                    if (!errorDiv) {
                        // Crée un nouvel élément div pour le message d'erreur
                        errorDiv = document.createElement("div");
                        // Ajoute une classe CSS pour le style
                        errorDiv.classList.add("text-danger");
                        // Ajoute le message d'erreur au div
                        input.after(errorDiv);
                    }
                    // Met à jour le contenu du message d'erreur
                    errorDiv.textContent = message;
                }
            }

           // Ecouteur d'événement pour le bouton d'envoi
           form.addEventListener("submit", function (e) {
               // Vérifie si tous les champs sont valides
               let allValid = true;
               inputs.forEach(input => {
                   if (!input.element.classList.contains("is-valid")) {
                       allValid = false;
                   }
               });

               if (allValid) {
                   alert("Formulaire soumis avec succès !");
               } else {
                   // Empêche l'envoi du formulaire si un champ est invalide
                   e.preventDefault();
                   alert("Veuillez corriger les erreurs avant de soumettre le formulaire.");
               }
           });
