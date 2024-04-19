<?php
// Connexion à la base de données
include 'db.php'; 

// Récupération des données de la base de données
function getArticles($conn) {
    $sql = "SELECT * FROM article";
    $result = $conn->query($sql);
    return $result;
}

// Ajout d'un nouvel article
if(isset($_POST['ajouter_article'])) {
    $titre = $_POST['titre'];
    $contenu = $_POST['contenu'];

    // Gestion du téléchargement d'image
    $dossierDestination = "images/";

    // Vérifier si le répertoire de destination existe, sinon le créer
    if (!is_dir($dossierDestination)) {
        if (!mkdir($dossierDestination, 0755, true)) {
            die('Erreur : Impossible de créer le répertoire de destination.');
        }
    }

    // Vérifier si le répertoire de destination a les bonnes permissions
    if (!is_writable($dossierDestination)) {
        die('Erreur : Le répertoire de destination n\'a pas les permissions nécessaires.');
    }

    $nomFichier = basename($_FILES['image']['name']);
    $cheminFichier = $dossierDestination . $nomFichier;
    
    // Déplacer le fichier téléchargé vers le répertoire de destination
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $cheminFichier)) {
        die('Erreur : Impossible de déplacer le fichier téléchargé.');
    }

    // Échapper les caractères spéciaux dans le titre et le contenu
    $titre = mysqli_real_escape_string($conn, $titre);
    $contenu = mysqli_real_escape_string($conn, $contenu);
    $cheminFichier = mysqli_real_escape_string($conn, $cheminFichier);

    // Enregistrer le chemin de l'image dans la base de données
    $sql = "INSERT INTO article (titre, contenu, chemin_image) VALUES ('$titre', '$contenu', '$cheminFichier')";

    if ($conn->query($sql) === TRUE) {
        echo "Nouvel article ajouté avec succès";
    } else {
        echo "Erreur : " . $sql . "<br>" . $conn->error;
    }
}




// Suppression d'un article
if(isset($_POST['supprimer_article'])) {
    $id_article = $_POST['id_article'];
    $sql = "DELETE FROM article WHERE id=$id_article";
    
    if ($conn->query($sql) === TRUE) {
        echo "Article supprimé avec succès";
    } else {
        echo "Erreur : " . $sql . "<br>" . $conn->error;
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Blog</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Votre CSS personnalisé -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php
include 'header.html'; // Inclure l'en-tête

// Le contenu de votre page principale
?>
    <div class="container">
        <!-- Affichage des articles existants -->
        <?php
        $result = getArticles($conn);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<section class='article mt-4'>";
                echo "<h2 class='h2art'>" . $row["titre"] . "</h2>";
                echo "<div class='img-container'>";
                echo "<img src='" . $row["chemin_image"] . "' class='img-fluid imgart' alt='Photo de l'article'>";
                echo "</div>";
                echo "<p class='part'>" . $row["contenu"] . "</p>";
                echo "<form method='post' class='formart' action=''>";
                echo "<input type='hidden' name='id_article' class='inpart' value='" . $row["id"] . "'>";
             
                echo "</form>";
                echo "</section>";
            }
        } else {
            echo "<p class='mt-4'>0 résultats</p>";
        }
        ?>
    </div>
    <?php
     include 'footer.html'; // Inclure le pied de page
    ?>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


<?php
$conn->close();
?>
