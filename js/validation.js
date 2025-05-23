

            // function RegexName() {
            //     const inputNameValue = inputName.value;
            //     const regexName = /^[a-zA-Z\s-]+$/;
            //   if (regexName.test(inputNameValue)) {
            //     // Le nom est valide color border vert sur le input nom avec une classe css
            //     inputName.classList.remove("is-invalid");
            //     inputName.classList.add("is-valid");
            //   } 
            //     else {
            //     // Le nom est invalide color border rouge sur le input nom avec une classe css
            //     inputName.classList.remove("is-valid");
            //     inputName.classList.add("is-invalid");
            //   }
            // }
            // // Ecouteur d'événement pour le champ input
            // inputName.addEventListener("input", function () {
            //     RegexName();
            // });

            // Même chose pour le prénom

            // function RegexPrenom() {
            //     const inputPrenomValue = inputPrenom.value;
            //     const regexPrenom = /^[a-zA-Z\s-]+$/;
            //     if (regexPrenom.test(inputPrenomValue)) {
            //         // Le prénom est valide color border vert sur le input prenom avec une classe css
            //         inputPrenom.classList.remove("is-invalid");
            //         inputPrenom.classList.add("is-valid");
            //     } 
            //     else {
            //         // Le prénom est invalide color border rouge sur le input prenom avec une classe css
            //         inputPrenom.classList.remove("is-valid");
            //         inputPrenom.classList.add("is-invalid");
            //     }
            // }
            // // Ecouteur d'événement pour le champ input
            // inputPrenom.addEventListener("input", function () {
            //     RegexPrenom();
            // });

            // const form = document.querySelector("form");
            // form.addEventListener("submit", function (event) {
            //     RegexName();
            //     if (inputName.classList.contains("is-valid")) {
            //         alert("Formulaire soumis avec succès !");
            //     } else {
            //         // Empêche l'envoi du formulaire si le nom est invalide
            //         event.preventDefault();
            //         // Affiche un message d'erreur ou une alerte
                  
            //         alert("Veuillez corriger les erreurs avant de soumettre le formulaire.");
            //     }
            // });
            const inputName = document.getElementById("nom");
            const inputEmail = document.getElementById("email");
            const inputTel = document.getElementById("tel");
            const inputPrenom = document.getElementById("prenom");
            const textArea = document.getElementById("msg")

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
                }
            ]
              
            inputs.forEach(input => {
                    input.element.addEventListener("input", function (e) {
                        RegexTest(this, input.regex);
                        // RegexTest(e.target, input.regex);
                        // RegexTest(input.element, input.regex);
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
