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

// Modification d'un article
if(isset($_POST['modifier_article'])) {
    $id_article = $_POST['id_article'];
    $titre_edit = $_POST['titre_edit'];
    $contenu_edit = $_POST['contenu_edit'];

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

    $nomFichier = basename($_FILES['image_edit']['name']);
    $cheminFichier = $dossierDestination . $nomFichier;

    // Déplacer le fichier téléchargé vers le répertoire de destination
    if (!move_uploaded_file($_FILES['image_edit']['tmp_name'], $cheminFichier)) {
        die('Erreur : Impossible de déplacer le fichier téléchargé.');
    }

    // Échapper les caractères spéciaux dans le titre et le contenu
    $titre_edit = mysqli_real_escape_string($conn, $titre_edit);
    $contenu_edit = mysqli_real_escape_string($conn, $contenu_edit);
    $cheminFichier = mysqli_real_escape_string($conn, $cheminFichier);

    // Mettre à jour l'article dans la base de données
    $sql = "UPDATE article SET titre='$titre_edit', contenu='$contenu_edit', chemin_image='$cheminFichier' WHERE id=$id_article";

    if ($conn->query($sql) === TRUE) {
        echo "Article modifié avec succès";
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
    <header>
        <h1 class="text-center">Palstine's Blogs</h1>
    </header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Accueil</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">Profile</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
        <!-- Formulaire pour ajouter un nouvel article -->
        <form method="post" class="addart mt-3" action="" enctype="multipart/form-data">
            <input type="text" name="titre" class="form-control mb-2" placeholder="Titre">
            <textarea name="contenu" class="form-control mb-2" placeholder="Contenu de l'article"></textarea>
            <input type="file" name="image" class="form-control mb-2" accept="image/*">
            <input type="submit" class="btn btn-primary" name="ajouter_article" value="Ajouter">
        </form>
        
        <!-- Affichage des articles existants -->
        <?php
        $result = getArticles($conn);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<section class='article mt-3'>";
                echo "<h2 class='h2art'>" . $row["titre"] . "</h2>";
                echo "<div class='img-container'>";
                echo "<img src='" . $row["chemin_image"] . "' class='img-fluid imgart' alt='Photo de l'article'>";
                echo "</div>";
                echo "<p class='part'>" . $row["contenu"] . "</p>";
                echo "<form method='post' class='formart' action=''>";
                echo "<input type='hidden' name='id_article' class='inpart' value='" . $row["id"] . "'>";
                echo "<input type='submit' name='supprimer_article' class='btn btn-danger' value='Supprimer'>";
                echo "</form>";

                // Formulaire pour éditer un article
                echo "<form method='post' class='editart mt-3' action='' enctype='multipart/form-data'>";
                echo "<input type='hidden' name='id_article' value='" . $row["id"] . "'>";
                echo "<input type='text' name='titre_edit' class='form-control mb-2' value='" . $row["titre"] . "'>";
                echo "<textarea name='contenu_edit' class='form-control mb-2'>" . $row["contenu"] . "</textarea>";
                echo "<input type='file' name='image_edit' class='form-control mb-2' accept='image/*'>";
                echo "<input type='submit' class='btn btn-primary' name='modifier_article' value='Modifier'>";
                echo "</form>";

                echo "</section>";
            }
        } else {
            echo "<p class='mt-3'>0 résultats</p>";
        }
        ?>
    </div>
    <footer class="text-center mt-5">
        <p>&copy; 2024 Mon Blog. Tous droits réservés.</p>
    </footer>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


<?php
$conn->close();
?>
