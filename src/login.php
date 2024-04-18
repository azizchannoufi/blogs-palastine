<?php
include 'db.php'; // Inclure le fichier de connexion à la base de données

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifier si les données du formulaire sont envoyées en méthode POST

    // Récupérer les données du formulaire
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $motdepasse = $_POST['motdepasse'];

    try {
        // Connexion à la base de données
        $bdd = new PDO($conn);
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Classe pour gérer les opérations CRUD sur les utilisateurs
        class GestionUtilisateurs {
            private $bdd;

            public function __construct($bdd) {
                $this->bdd = $bdd;
            }

            public function creerUtilisateur($nom, $prenom, $email, $motdepasse) {
                $requete = $this->bdd->prepare("INSERT INTO utilisateurs (nom, prenom, email, motdepasse) VALUES (?, ?, ?, ?)");
                $requete->execute([$nom, $prenom, $email, $motdepasse]);
                return $requete->rowCount(); // Retourne le nombre de lignes affectées par l'opération
            }
        }

        $gestionUtilisateurs = new GestionUtilisateurs($bdd);
        $resultat = $gestionUtilisateurs->creerUtilisateur($nom, $prenom, $email, $motdepasse);
        if ($resultat) {
            echo "<p class='success'>Utilisateur créé avec succès.</p>";
        } else {
            echo "<p class='error'>Erreur lors de la création de l'utilisateur.</p>";
        }
    } catch (PDOException $e) {
        echo "Erreur de connexion à la base de données: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Création d'utilisateur</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Votre CSS personnalisé -->
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="container">
        <h1 class="text-center mt-5">Création d'utilisateur</h1>
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <form action="" method="post">
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom :</label>
                        <input type="text" id="nom" name="nom" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="prenom" class="form-label">Prénom :</label>
                        <input type="text" id="prenom" name="prenom" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email :</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="motdepasse" class="form-label">Mot de passe :</label>
                        <input type="password" id="motdepasse" name="motdepasse" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Créer l'utilisateur</button>
                </form>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
