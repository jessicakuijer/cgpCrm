document.addEventListener('DOMContentLoaded', function() {
    // Fonction pour gérer l'affichage du champ des contrats
    function toggleContractsField() {
        const clientField = document.querySelector('input[name="User[client]"]');
        const contractsField = document.querySelector('.contrats-souscrits-field').closest('.form-group');
        
        if (!clientField || !contractsField) return;
        
        // Afficher ou masquer le champ des contrats en fonction de l'état du champ client
        if (clientField.checked) {
            contractsField.style.display = 'block';
        } else {
            contractsField.style.display = 'none';
        }
    }
    
    // Observer les changements sur le champ client
    const clientField = document.querySelector('input[name="User[client]"]');
    if (clientField) {
        // Exécuter une fois au chargement
        toggleContractsField();
        
        // Ajouter l'écouteur d'événements pour les changements
        clientField.addEventListener('change', toggleContractsField);
    }
});