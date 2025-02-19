document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form');
    const resultsContainer = document.querySelector('.results');
    const resetButton = document.querySelector('.reset-btn');

    // Animation des résultats
    const resultItems = resultsContainer.querySelectorAll('p');
    resultItems.forEach((item, index) => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(20px)';
        setTimeout(() => {
            item.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            item.style.opacity = '1';
            item.style.transform = 'translateY(0)';
        }, index * 100); // Ajouter un délai entre chaque résultat
    });

    // Validation simple avant soumission
    form.addEventListener('submit', (event) => {
        const nameField = form.querySelector('input[name="nom"]');
        const dateField = form.querySelector('input[name="date_naissance"]');

        // Validation de la date
        if (dateField && dateField.value) {
            const selectedDate = new Date(dateField.value);
            if (selectedDate > new Date()) {
                alert('La date de naissance ne peut pas être dans le futur.');
                event.preventDefault();
            }
        }
    });

    // Écouteur pour le bouton de réinitialisation
    resetButton.addEventListener('click', (event) => {
        // Réinitialiser tous les champs sauf la date de naissance
        form.querySelectorAll('input, select').forEach((field) => {
            if (field.type === 'text') {
                field.value = ''; // Réinitialiser les champs texte
            } else if (field.tagName === 'SELECT') {
                field.selectedIndex = 0; // Réinitialiser les sélecteurs
            } else if (field.type === 'date' && field.name === 'date_naissance') {
                field.value = '1920-01-01'; // Garder la date par défaut
            }
        });

        // Empêcher la soumission automatique du formulaire
        event.preventDefault();
    });
});
