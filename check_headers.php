<?php
// Afficher tous les en-têtes de réponse
echo "<pre>";
print_r(headers_list());
echo "</pre>";

// Afficher les informations de session
echo "<h2>Session:</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Afficher les informations du serveur
echo "<h2>Serveur:</h2>";
echo "<pre>";
echo "SERVER_SOFTWARE: " . $_SERVER['SERVER_SOFTWARE'] . "\n";
echo "PHP_SELF: " . $_SERVER['PHP_SELF'] . "\n";
echo "HTTP_REFERER: " . ($_SERVER['HTTP_REFERER'] ?? 'Non défini') . "\n";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "</pre>";
?>
