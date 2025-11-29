<?php
// test_video_path.php
$possible_paths = [
    'assets/videos/Unity_Donation_Heartfelt_Giving.mp4',
    '../assets/videos/Unity_Donation_Heartfelt_Giving.mp4',
    '../../assets/videos/Unity_Donation_Heartfelt_Giving.mp4',
    'view/Frontoffice/assets/videos/Unity_Donation_Heartfelt_Giving.mp4'
];

echo "<h1>Test des chemins vidéo</h1>";

foreach ($possible_paths as $path) {
    $full_path = __DIR__ . '/' . $path;
    echo "<p><strong>$path</strong>: ";
    if (file_exists($full_path)) {
        echo "<span style='color: green;'>✅ TROUVÉ</span>";
        echo " - Taille: " . filesize($full_path) . " bytes";
        
        // Test d'affichage
        echo "<br><video width='300' controls><source src='$path' type='video/mp4'></video>";
    } else {
        echo "<span style='color: red;'>❌ NON TROUVÉ</span>";
    }
    echo "</p>";
}
?>