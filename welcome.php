<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue - Gestion des Stocks Restaurant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --accent-color: #ffc107;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }
        
        .welcome-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .welcome-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 1000px;
            width: 100%;
            transition: all 0.5s ease;
        }
        
        .welcome-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }
        
        .card-inner {
            display: flex;
            flex-direction: column;
            min-height: 500px;
        }
        
        @media (min-width: 768px) {
            .card-inner {
                flex-direction: row;
            }
        }
        
        .welcome-image {
            flex: 1;
            background-image: url('https://images.unsplash.com/photo-1590846406792-0adc7f938f1d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80');
            background-size: cover;
            background-position: center;
            position: relative;
            min-height: 250px;
        }
        
        .welcome-image::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(13, 110, 253, 0.3);
        }
        
        .welcome-content {
            flex: 1;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .welcome-title {
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: var(--primary-color);
            position: relative;
        }
        
        .welcome-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 50px;
            height: 3px;
            background-color: var(--accent-color);
        }
        
        .feature-item {
            margin-bottom: 1.5rem;
            display: flex;
            align-items: flex-start;
        }
        
        .feature-icon {
            font-size: 1.5rem;
            color: var(--primary-color);
            margin-right: 1rem;
            background: rgba(13, 110, 253, 0.1);
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }
        
        .btn-enter {
            padding: 0.75rem 2rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        
        .btn-enter::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.2);
            transition: all 0.4s ease;
            z-index: -1;
        }
        
        .btn-enter:hover::before {
            left: 0;
        }
        
        .loading-spinner {
            display: none;
            margin-left: 10px;
        }
        
        .animated-element {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.5s ease, transform 0.5s ease;
        }
        
        .animated-element.show {
            opacity: 1;
            transform: translateY(0);
        }
        
        .delay-1 { transition-delay: 0.1s; }
        .delay-2 { transition-delay: 0.2s; }
        .delay-3 { transition-delay: 0.3s; }
        .delay-4 { transition-delay: 0.4s; }
        .delay-5 { transition-delay: 0.5s; }
        
        .footer {
            text-align: center;
            padding: 1.5rem;
            background: rgba(255, 255, 255, 0.8);
            margin-top: auto;
        }
    </style>
</head>
<body>
    <div class="welcome-container">
        <div class="welcome-card animate__animated animate__fadeIn">
            <div class="card-inner">
                <div class="welcome-image"></div>
                <div class="welcome-content">
                    <h1 class="welcome-title animated-element">Gestion des Stocks Restaurant</h1>
                    <p class="lead mb-4 animated-element delay-1">Système de gestion efficace pour optimiser vos stocks et améliorer votre rentabilité.</p>
                    
                    <div class="feature-item animated-element delay-2">
                        <div class="feature-icon">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <div>
                            <h5>Suivi en temps réel</h5>
                            <p>Surveillez vos niveaux de stock et recevez des alertes automatiques.</p>
                        </div>
                    </div>
                    
                    <div class="feature-item animated-element delay-3">
                        <div class="feature-icon">
                            <i class="bi bi-people"></i>
                        </div>
                        <div>
                            <h5>Gestion des utilisateurs</h5>
                            <p>Différents niveaux d'accès pour votre équipe.</p>
                        </div>
                    </div>
                    
                    <div class="feature-item animated-element delay-4">
                        <div class="feature-icon">
                            <i class="bi bi-bar-chart"></i>
                        </div>
                        <div>
                            <h5>Rapports détaillés</h5>
                            <p>Analysez vos données pour prendre de meilleures décisions.</p>
                        </div>
                    </div>
                    
                    <div class="mt-4 animated-element delay-5">
                        <button id="enterBtn" class="btn btn-primary btn-enter">
                            <span>Accéder au système</span>
                            <div class="spinner-border spinner-border-sm loading-spinner" role="status">
                                <span class="visually-hidden">Chargement...</span>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <footer class="footer">
        <p class="mb-0">© <?php echo date('Y'); ?> Gestion des Stocks Restaurant. Tous droits réservés.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Afficher les éléments animés après chargement
            setTimeout(function() {
                $('.animated-element').addClass('show');
            }, 100);
            
            // Gestion du bouton d'entrée avec Ajax
            $('#enterBtn').click(function() {
                const $btn = $(this);
                const $spinner = $('.loading-spinner');
                const $btnText = $btn.find('span');
                
                // Afficher le spinner
                $btnText.text('Chargement...');
                $spinner.show();
                
                // Simuler une requête Ajax (vous pouvez remplacer par une vraie requête)
                setTimeout(function() {
                    // Rediriger vers la page de connexion
                    window.location.href = 'connexion.php';
                }, 1000);
            });
        });
    </script>
</body>
</html>