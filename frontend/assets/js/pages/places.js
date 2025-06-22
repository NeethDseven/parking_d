/* Script de correction pour les formulaires de réservation sur la page des places */

document.addEventListener('DOMContentLoaded', function() {
    console.log('🔧 Script de correction formulaires chargé');

    /* Récupère l'URL de base depuis la meta tag */
    const baseUrl = document.querySelector('meta[name="base-url"]')?.getAttribute('content') || '';

    /* Vérifie si l'utilisateur est connecté via un attribut data */
    const isUserLoggedIn = document.body.dataset.userLoggedIn === 'true';
    console.log('👤 Utilisateur connecté:', isUserLoggedIn);

    /* Attendre que la modal soit disponible et que le JavaScript principal soit chargé */
    setTimeout(function() {
        if (isUserLoggedIn) {
            /* Utilisateur connecté - vérifie le formulaire utilisateur */
            const userForm = document.getElementById('reservation-form');
            console.log('🔍 Formulaire utilisateur trouvé:', userForm);

            if (userForm) {
                console.log('✅ Formulaire utilisateur disponible - pas de correction nécessaire');
            } else {
                console.warn('⚠️ Formulaire utilisateur non trouvé');
            }
        } else {
            /* Utilisateur non connecté - gère le formulaire invité */
            handleGuestForm();
        }
    }, 1500);

    /* Gère le formulaire invité */
    function handleGuestForm() {
        const guestForm = document.getElementById('guest-reservation-form');
        console.log('🔍 Formulaire invité trouvé:', guestForm);

        if (guestForm) {
            /* Vérifie si le gestionnaire est déjà attaché */
            if (!guestForm.dataset.handlerAttached) {
                console.log('🔧 Ajout du gestionnaire AJAX au formulaire invité');

                /* Marque comme traité */
                guestForm.dataset.handlerAttached = 'true';

                /* Ajoute un gestionnaire AJAX */
                guestForm.addEventListener('submit', function(event) {
                    event.preventDefault();
                    event.stopPropagation();
                    console.log('🚀 Formulaire invité soumis via AJAX (script de correction)');

                    /* Vérifie le place_id */
                    const placeIdField = document.getElementById('guest_place_id');
                    console.log('🔍 Champ place_id:', placeIdField, 'Valeur:', placeIdField?.value);

                    if (!placeIdField || !placeIdField.value) {
                        alert('Erreur: Place non sélectionnée. Veuillez fermer cette fenêtre et cliquer à nouveau sur "Réserver" pour une place.');
                        return;
                    }

                    /* Désactive le bouton de soumission */
                    const submitButton = guestForm.querySelector('button[type="submit"]');
                    if (submitButton) {
                        submitButton.disabled = true;
                        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Traitement...';
                    }

                    /* Crée FormData */
                    const formData = new FormData(guestForm);
                    console.log('📋 Données du formulaire:');
                    for (let [key, value] of formData.entries()) {
                        console.log(`  ${key}: ${value}`);
                    }

                    /* Envoie la requête AJAX */
                    fetch(baseUrl + 'reservation/guestReserve', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('📡 Réponse du serveur:', data);
                        if (data.success) {
                            console.log('✅ Redirection vers:', data.redirect_url);
                            window.location.href = data.redirect_url;
                        } else {
                            alert('Erreur: ' + (data.error || 'Une erreur est survenue'));
                            resetSubmitButton(submitButton);
                        }
                    })
                    .catch(error => {
                        console.error('❌ Erreur:', error);
                        alert('Erreur de communication avec le serveur');
                        resetSubmitButton(submitButton);
                    });
                });

                console.log('✅ Gestionnaire AJAX ajouté au formulaire invité');
            } else {
                console.log('ℹ️ Gestionnaire déjà attaché au formulaire invité');
            }
        } else {
            console.log('ℹ️ Formulaire invité non trouvé (normal pour utilisateur non connecté)');
        }
    }

    /* Réactive le bouton de soumission */
    function resetSubmitButton(submitButton) {
        if (submitButton) {
            submitButton.disabled = false;
            submitButton.innerHTML = '<i class="fas fa-check me-2"></i> Réserver en tant qu\'invité';
        }
    }
});
