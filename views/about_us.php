<?php
require_once './includes/functions.php';
require_once './includes/database.php';

redirectIfNotLoggedIn();

$teamMembers = [
    [
        "name" => "Jaime Young",
        "role" => "Frontend Developer",
        "description" => "Especialista en HTML, CSS y JavaScript",
        "image" => "./src/images/Jaime_Young.jpg",
    ],
    [
        "name" => "Jonathan Reyes",
        "role" => "Backend Developer",
        "description" => "Experto en PHP y bases de datos MySQL",
        "image" => "./src/images/Jonathan_Reyes.jpg"
    ],
    [
        "name" => "Antonio Carmona",
        "role" => "Full Stack Engineer",
        "description" => "Desarrollador versátil con experiencia en múltiples tecnologías",
        "image" => "./src/images/Antonio_Carmona.jpg",
    ],
    [
        "name" => "Jacke Sánchez",
        "role" => "Mobile Developer",
        "description" => "Especializado en desarrollo de apps para iOS y Android",
        "image" => "./src/images/Jake_Sanchez.jpg"
    ],
    [
        "name" => "David González",
        "role" => "DevOps Engineer",
        "description" => "Experto en automatización y despliegue continuo",
        "image" => "./src/images/FitLife_Logo.png"
    ]
];
?>

<div class="about-container">
    <h1 class="about-title">Sobre Nosotros</h1>
    
    <p class="about-description">
        Somos un equipo apasionado de desarrolladores e ingenieros dedicados a crear la mejor 
        experiencia de fitness para nuestros usuarios. Nuestra diversidad de habilidades y 
        experiencia nos permite ofrecer una aplicación robusta, intuitiva y efectiva.
    </p>

    <div class="team-grid">
        <?php foreach ($teamMembers as $member): ?>
            <div class="team-card">
                <div class="member-image-container">
                    <img 
                        src="<?php echo htmlspecialchars($member['image']); ?>" 
                        alt="<?php echo htmlspecialchars($member['name']); ?>"
                        class="member-image"
                    >
                </div>
                <h2 class="member-name"><?php echo htmlspecialchars($member['name']); ?></h2>
                <h3 class="member-role"><?php echo htmlspecialchars($member['role']); ?></h3>
                <p class="member-description"><?php echo htmlspecialchars($member['description']); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</div>