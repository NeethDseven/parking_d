<?php
/**
 * Contrôleur d'authentification consolidé et optimisé
 * Gère l'authentification, l'inscription, le profil et les notifications utilisateur
 */
class AuthController extends BaseController
{
    private $userModel;
    private $logModel;
    private $reservationModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new UserModel();
        $this->logModel = new LogModel();
        $this->reservationModel = new ReservationModel();
    }

    /**
     * Connexion utilisateur optimisée
     */
    public function login()
    {
        $data = [
            'title' => 'Connexion - ' . APP_NAME,
            'description' => 'Connectez-vous à votre compte ' . APP_NAME,
            'active_page' => 'login'
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $validated = $this->validateRequiredFields(['email', 'password']);
            
            if (!$validated) {
                $data['error'] = 'Tous les champs sont obligatoires.';
            } else {
                $email = $this->sanitizeInput($validated['email']);
                $password = $validated['password'];
                $remember = isset($_POST['remember_me']) ? true : false;
                
                $user = $this->userModel->authenticate($email, $password);
                
                if ($user) {
                    // Si l'utilisateur est désactivé
                    if ($user['status'] === 'inactive') {
                        $data['error'] = 'Votre compte a été désactivé. Veuillez contacter l\'administrateur.';
                    } else {
                        // Connexion réussie
                        $_SESSION['user'] = [
                            'id' => $user['id'],
                            'nom' => $user['nom'],
                            'prenom' => $user['prenom'],
                            'email' => $user['email'],
                            'role' => $user['role'],
                            'is_subscribed' => $user['is_subscribed'] ?? false
                        ];
                        
                        // Journaliser la connexion
                        $this->logModel->addLog($user['id'], 'connexion', 'Connexion réussie');

                        // Vérifier si l'utilisateur a des réservations invité à convertir
                        $this->handleGuestReservationConversion($user['id'], $user['email']);
                        
                        // Rediriger selon le rôle
                        $redirectUrl = $user['role'] === 'admin' ? 'admin/dashboard' : 'auth/profile';
                        $this->redirect($redirectUrl);
                    }
                } else {
                    $data['error'] = 'Email ou mot de passe incorrect.';
                }
            }
        }

        $this->renderView('auth/login', $data);
    }

    /**
     * Inscription utilisateur optimisée
     */
    public function register()
    {
        $data = [
            'title' => 'Inscription - ' . APP_NAME,
            'description' => 'Créez un compte sur ' . APP_NAME,
            'active_page' => 'register'
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $validated = $this->validateRequiredFields([
                'nom', 'prenom', 'email', 'password', 'confirm_password'
            ]);

            if (!$validated) {
                $data['error'] = 'Tous les champs sont obligatoires';
            } elseif (!$this->isValidEmail($validated['email'])) {
                $data['error'] = 'L\'adresse email est invalide';
            } elseif ($validated['password'] !== $validated['confirm_password']) {
                $data['error'] = 'Les mots de passe ne correspondent pas';
            } elseif (strlen($validated['password']) < 6) {
                $data['error'] = 'Le mot de passe doit contenir au moins 6 caractères';
            } elseif ($this->userModel->getUserByEmail($validated['email'])) {
                $data['error'] = 'Un compte existe déjà avec cette adresse email';
            } else {
                // Création de l'utilisateur
                $userId = $this->userModel->createUser(
                    $validated['nom'],
                    $validated['prenom'],
                    $validated['email'],
                    $_POST['telephone'] ?? null,
                    $validated['password']
                );

                if ($userId) {
                    // Connexion automatique
                    $user = $this->userModel->getUserById($userId);
                    $_SESSION['user'] = [
                        'id' => $user['id'],
                        'nom' => $user['nom'],
                        'prenom' => $user['prenom'],
                        'email' => $user['email'],
                        'role' => $user['role'],
                        'is_subscribed' => false
                    ];

                    // Journaliser l'inscription
                    $this->logModel->addLog($userId, 'inscription', 'Création de compte réussie');
                    
                    // Vérifier si l'utilisateur a des réservations invité à convertir
                    $this->handleGuestReservationConversion($userId, $validated['email']);
                    
                    $this->redirectWithSuccess('auth/profile', 'Votre compte a été créé avec succès!');
                } else {
                    $data['error'] = 'Une erreur est survenue lors de la création de votre compte.';
                }
            }
        }

        $this->renderView('auth/register', $data);
    }

    /**
     * Déconnexion optimisée
     */
    public function logout()
    {
        if (isset($_SESSION['user'])) {
            $this->logModel->addLog($_SESSION['user']['id'], 'déconnexion', 'Déconnexion réussie');
        }

        session_unset();
        session_destroy();
        $this->redirect(BASE_URL);
    }

    /**
     * Profil utilisateur consolidé
     */
    public function profile()
    {
        $this->requireAuth();

        $user = $this->userModel->getUserById($_SESSION['user']['id']);
        $reservations = $this->userModel->getUserReservations($_SESSION['user']['id']);
        $notifications = $this->userModel->getUserNotifications($_SESSION['user']['id']);
        
        $data = [
            'title' => 'Mon profil - ' . APP_NAME,
            'description' => 'Gérez votre profil et vos réservations',
            'active_page' => 'profile',
            'user' => $user,
            'reservations' => $reservations,
            'notifications' => $notifications,
            'unread_notifications' => $this->userModel->countUnreadNotifications($_SESSION['user']['id'])
        ];

        $this->renderView('auth/profile', $data);
    }

    /**
     * Mise à jour du profil optimisée
     */
    public function updateProfile()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $validated = $this->validateRequiredFields(['nom', 'prenom', 'email']);
            
            if (!$validated) {
                $this->redirectWithError('auth/profile', 'Tous les champs obligatoires doivent être remplis.');
                return;
            }

            if (!$this->isValidEmail($validated['email'])) {
                $this->redirectWithError('auth/profile', 'L\'adresse email est invalide.');
                return;
            }

            // Vérifier si l'email est déjà utilisé par un autre utilisateur
            $existingUser = $this->userModel->getUserByEmail($validated['email']);
            if ($existingUser && $existingUser['id'] != $_SESSION['user']['id']) {
                $this->redirectWithError('auth/profile', 'Cette adresse email est déjà utilisée par un autre compte.');
                return;
            }

            // Préparation des données à mettre à jour
            $userData = [
                'nom' => $validated['nom'],
                'prenom' => $validated['prenom'],
                'email' => $validated['email'],
                'telephone' => $_POST['telephone'] ?? null
            ];

            // Mise à jour du mot de passe si fourni
            if (!empty($_POST['password']) && !empty($_POST['confirm_password'])) {
                if ($_POST['password'] !== $_POST['confirm_password']) {
                    $this->redirectWithError('auth/profile', 'Les mots de passe ne correspondent pas.');
                    return;
                }

                if (strlen($_POST['password']) < 6) {
                    $this->redirectWithError('auth/profile', 'Le mot de passe doit contenir au moins 6 caractères.');
                    return;
                }

                $userData['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }

            // Mise à jour des données
            if ($this->userModel->updateUser($_SESSION['user']['id'], $userData)) {
                // Mettre à jour les données de session
                $_SESSION['user']['nom'] = $validated['nom'];
                $_SESSION['user']['prenom'] = $validated['prenom'];
                $_SESSION['user']['email'] = $validated['email'];

                $this->redirectWithSuccess('auth/profile', 'Votre profil a été mis à jour avec succès.');
            } else {
                $this->redirectWithError('auth/profile', 'Une erreur est survenue lors de la mise à jour de votre profil.');
            }
        }
    }

    /**
     * Conversion des réservations invité
     */
    public function convertReservations()
    {
        if (!isset($_SESSION['user_id_for_conversion']) || !isset($_SESSION['guest_reservations_to_convert'])) {
            $this->redirect(BASE_URL);
            return;
        }

        $userId = $_SESSION['user_id_for_conversion'];
        $guestReservations = $_SESSION['guest_reservations_to_convert'];

        $data = [
            'title' => 'Associer vos réservations - ' . APP_NAME,
            'description' => 'Associez vos réservations invités à votre compte',
            'reservations' => $guestReservations
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['convert']) && $_POST['convert'] === 'yes') {
                $convertCount = 0;
                foreach ($guestReservations as $reservation) {
                    if ($this->reservationModel->convertGuestReservationToUser($reservation['id'], $userId)) {
                        $convertCount++;
                    }
                }

                if ($convertCount > 0) {
                    $this->userModel->createNotification(
                        $userId,
                        'Réservations associées',
                        $convertCount . ' réservation(s) ont été associées à votre compte.',
                        'info'
                    );
                }

                // Nettoyer les sessions temporaires
                unset($_SESSION['user_id_for_conversion']);
                unset($_SESSION['guest_reservations_to_convert']);

                $this->redirectWithSuccess('auth/profile', $convertCount . ' réservation(s) ont été associées à votre compte.');
            } else {
                // L'utilisateur a choisi de ne pas convertir les réservations
                unset($_SESSION['user_id_for_conversion']);
                unset($_SESSION['guest_reservations_to_convert']);
                $this->redirect('auth/profile');
            }
        }

        $this->renderView('auth/convert_reservations', $data);
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllNotificationsRead()
    {
        $this->requireAuth();
        
        $this->userModel->markAllNotificationsAsRead($_SESSION['user']['id']);
        
        $referer = $_SERVER['HTTP_REFERER'] ?? 'auth/profile';
        $this->redirect($referer);
    }

    /**
     * Gestion de la conversion des réservations invité lors de l'inscription
     * @param int $userId ID de l'utilisateur
     * @param string $email Email de l'utilisateur
     */    private function handleGuestReservationConversion($userId, $email)
    {
        if (!$userId || !$email) {
            return;
        }
        
        $guestReservations = $this->reservationModel->getReservationsByGuestEmail($email);
        
        if (!empty($guestReservations)) {
            // Conserver les informations de conversion pour la page suivante
            $_SESSION['user_id_for_conversion'] = $userId;
            $_SESSION['guest_reservations_to_convert'] = $guestReservations;
            
            $this->redirect(BASE_URL . 'auth/convertReservations');
        }
    }
}
