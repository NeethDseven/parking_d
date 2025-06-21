<?php
/**
 * Contrôleur d'abonnements consolidé et optimisé
 * Gestion des abonnements utilisateurs et interface d'administration des abonnements
 */
class SubscriptionController extends BaseController
{    private $subscriptionModel;
    private $userModel;   
    private $logModel;
    private $notificationService;    public function __construct()
    {
        parent::__construct();
        $this->subscriptionModel = new SubscriptionModel();
        $this->userModel = new UserModel();
        $this->logModel = new LogModel();
        $this->notificationService = new NotificationService();
    }/**
     * Affiche la liste des abonnements disponibles
     */
    public function index()
    {
        $this->requireAuth();

        $subscriptions = $this->subscriptionModel->getAllSubscriptions();
        $userSubscriptions = $this->subscriptionModel->getUserActiveSubscriptions($_SESSION['user']['id']);

        $data = [
            'subscriptions' => $subscriptions,
            'userSubscriptions' => $userSubscriptions
        ];

        $this->renderView('subscription/index', $data);
    }    /**
     * Gère la souscription à un abonnement
     */
    public function subscribe($subscriptionId = null)
    {
        $this->requireAuth();

        // Vérifier que l'ID d'abonnement est fourni
        if (!$subscriptionId) {
            $this->redirectWithError('subscription', "Aucun abonnement sélectionné.");
        }

        // Vérifier si l'utilisateur a déjà un abonnement actif
        $activeSubscriptions = $this->subscriptionModel->getUserActiveSubscriptions($_SESSION['user']['id']);
        if (!empty($activeSubscriptions)) {
            $this->redirectWithError('auth/profile', "Vous avez déjà un abonnement actif. Veuillez d'abord résilier votre abonnement actuel.");
        }

        // Récupérer les informations de l'abonnement
        $subscription = $this->subscriptionModel->getSubscriptionById($subscriptionId);
        if (!$subscription) {
            $this->redirectWithError('subscription', "Cet abonnement n'existe pas ou n'est plus disponible.");
        }

        // Ici, on pourrait intégrer une logique de paiement
        // Pour le moment, on simule un paiement réussi
        $paymentId = null; // À remplacer par l'ID du paiement réel

        // Souscrire l'utilisateur
        $result = $this->subscriptionModel->subscribeUser($_SESSION['user']['id'], $subscriptionId, $paymentId);        if ($result) {
            // Récupérer les détails de l'abonnement créé
            $userSubscriptions = $this->subscriptionModel->getUserActiveSubscriptions($_SESSION['user']['id']);
            $subscription = $this->subscriptionModel->getSubscriptionById($subscriptionId);
            
            // Mise à jour de la session utilisateur pour refléter le nouvel état d'abonnement
            $_SESSION['user']['is_subscribed'] = 1;
            
            // Envoyer une notification de confirmation d'abonnement
            if (!empty($userSubscriptions) && $subscription) {
                $userSubscription = $userSubscriptions[0]; // Prendre le plus récent
                $this->notificationService->sendSubscriptionConfirmationNotification(
                    $_SESSION['user']['id'],
                    $subscription['nom'], // Utiliser 'nom' au lieu de 'name'
                    $userSubscription['date_debut'],
                    $userSubscription['date_fin']
                );
            }
            
            $this->redirectWithSuccess('auth/profile', "Vous avez souscrit avec succès à l'abonnement " . $subscription['nom']);
        } else {
            $this->redirectWithError('subscription', "Une erreur est survenue lors de la souscription à l'abonnement.");
        }
    }    /**
     * Gère la résiliation d'un abonnement
     */
    public function cancel($userSubscriptionId = null)
    {
        $this->requireAuth();

        // Vérifier que l'ID de souscription utilisateur est fourni
        if (!$userSubscriptionId) {
            $this->redirectWithError('auth/profile', "Aucun abonnement sélectionné pour la résiliation.");
        }

        // Résilier l'abonnement
        $result = $this->subscriptionModel->cancelUserSubscription($_SESSION['user']['id'], $userSubscriptionId);

        if ($result) {
            // Mettre à jour la session utilisateur
            $hasActiveSubscription = $this->subscriptionModel->updateUserSubscriptionStatus($_SESSION['user']['id']);
            $_SESSION['user']['is_subscribed'] = $hasActiveSubscription ? 1 : 0;
            
            $this->redirectWithSuccess('auth/profile', "Votre abonnement a été résilié avec succès.");
        } else {
            $this->redirectWithError('auth/profile', "Une erreur est survenue lors de la résiliation de l'abonnement.");
        }
    }    /**
     * Interface d'administration des abonnements
     */    public function admin()
    {
        $this->requireAdmin();
        
        // Récupérer tous les abonnements, y compris les inactifs
        $subscriptions = $this->subscriptionModel->getAllSubscriptions(false);
        $stats = $this->subscriptionModel->getSubscriptionStats();

        // Transformer les stats pour le graphique
        $transformedStats = [];
        foreach ($stats as $stat) {
            if (isset($stat['name']) && isset($stat['count'])) {
                $transformedStats[] = [
                    'name' => $stat['name'],
                    'count' => (int)$stat['count']
                ];
            }
        }

        $activeCount = $this->subscriptionModel->countActiveSubscriptions();
        $revenue = [
            'today' => $this->subscriptionModel->calculateSubscriptionRevenue('today'),
            'month' => $this->subscriptionModel->calculateSubscriptionRevenue('month'),
            'total' => $this->subscriptionModel->calculateSubscriptionRevenue('total')
        ];
        
        // Utiliser setActiveMenu et combiner avec les autres données
        $data = $this->setActiveMenu('subscriptions') + [
            'subscriptions' => $subscriptions,
            'stats' => $transformedStats,
            'activeCount' => $activeCount,
            'revenue' => $revenue
        ];

        $this->renderView('admin/subscriptions/index', $data, 'admin');
    }    /**
     * Gère la création d'un nouvel abonnement
     */
    public function create()
    {
        $this->requireAdmin();
        $this->setActiveMenu('subscriptions');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $requiredFields = ['name', 'description', 'price', 'duration_days'];
            $validatedData = $this->validateRequiredFields($requiredFields);
            
            // Si validateRequiredFields retourne false, la redirection a déjà eu lieu
            if ($validatedData === false) {
                return;
            }

            // Récupérer et valider les données du formulaire
            $data = [
                'name' => $_POST['name'] ?? '',
                'description' => $_POST['description'] ?? '',
                'price' => floatval($_POST['price'] ?? 0),
                'duration_days' => intval($_POST['duration_days'] ?? 0),
                'free_minutes' => intval($_POST['free_minutes'] ?? 0),
                'discount_percent' => floatval($_POST['discount_percent'] ?? 0), // Changé en floatval pour accepter les décimales
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            // Validation basique
            if ($data['price'] <= 0 || $data['duration_days'] <= 0) {
                $this->redirectWithError('subscription/admin', "Veuillez remplir tous les champs obligatoires correctement.");
                return;
            }

            // Créer l'abonnement
            $result = $this->subscriptionModel->createSubscription($data);

            if ($result) {
                $this->redirectWithSuccess('subscription/admin', "L'abonnement a été créé avec succès.");
            } else {
                $this->redirectWithError('subscription/admin', "Une erreur est survenue lors de la création de l'abonnement.");
            }
            return;
        }

        $this->renderView('admin/subscriptions/create', [], 'admin');
    }/**
     * Gère la mise à jour d'un abonnement
     */
    public function update($id = null)
    {
        $this->requireAdmin();
        $this->setActiveMenu('subscriptions');

        if (!$id) {
            $this->redirectWithError('subscription/admin', "Aucun abonnement sélectionné.");
        }

        // Récupérer l'abonnement existant
        $subscription = $this->subscriptionModel->getSubscriptionById($id);
        if (!$subscription) {
            $this->redirectWithError('subscription/admin', "Cet abonnement n'existe pas.");
        }        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $requiredFields = ['name', 'description', 'price', 'duration_days'];
            $validatedData = $this->validateRequiredFields($requiredFields);
            
            // Si validateRequiredFields retourne false, la redirection a déjà eu lieu
            if ($validatedData === false) {
                return;
            }

            // Récupérer et valider les données du formulaire
            $data = [
                'name' => $_POST['name'] ?? '',
                'description' => $_POST['description'] ?? '',
                'price' => floatval($_POST['price'] ?? 0),
                'duration_days' => intval($_POST['duration_days'] ?? 0),
                'free_minutes' => intval($_POST['free_minutes'] ?? 0),
                'discount_percent' => floatval($_POST['discount_percent'] ?? 0), // Changé en floatval pour accepter les décimales
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            // Validation basique
            if ($data['price'] <= 0 || $data['duration_days'] <= 0) {
                $this->redirectWithError("subscription/update/$id", "Veuillez remplir tous les champs obligatoires correctement.");
                return;
            }

            // Mettre à jour l'abonnement
            $result = $this->subscriptionModel->updateSubscription($id, $data);

            if ($result) {
                $this->redirectWithSuccess('subscription/admin', "L'abonnement a été mis à jour avec succès.");
            } else {
                $this->redirectWithError("subscription/update/$id", "Une erreur est survenue lors de la mise à jour de l'abonnement.");
            }
            return;
        }

        $data = ['subscription' => $subscription];
        $this->renderView('admin/subscriptions/edit', $data, 'admin');
    }    /**
     * Gère la suppression d'un abonnement
     */
    public function delete($id = null)
    {
        $this->requireAdmin();

        if (!$id) {
            $this->redirectWithError('subscription/admin', "Aucun abonnement sélectionné pour la suppression.");
        }

        // Supprimer l'abonnement (ou le désactiver s'il est utilisé)
        $result = $this->subscriptionModel->deleteSubscription($id);

        if ($result) {
            $this->redirectWithSuccess('subscription/admin', "L'abonnement a été supprimé ou désactivé avec succès.");
        } else {
            $this->redirectWithError('subscription/admin', "Une erreur est survenue lors de la suppression de l'abonnement.");
        }
    }    /**
     * Traite le paiement d'un abonnement
     */
    public function processPayment()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectWithError('subscription', "Méthode non autorisée.");
            return;
        }

        // Validation des données avec la méthode AJAX pour éviter les redirections automatiques
        $validated = $this->validateRequiredFieldsAjax(['subscription_id', 'mode_paiement']);
        if (!$validated) {
            // Identifier les champs manquants
            $missing = [];
            if (!isset($_POST['subscription_id']) || trim($_POST['subscription_id']) === '') {
                $missing[] = 'ID d\'abonnement';
            }
            if (!isset($_POST['mode_paiement']) || trim($_POST['mode_paiement']) === '') {
                $missing[] = 'Mode de paiement';
            }
            
            $this->redirectWithError('subscription', "Données de paiement incomplètes. Champs manquants : " . implode(', ', $missing));
            return;
        }

        // Vérifier les conditions d'utilisation
        if (!isset($_POST['subscription_conditions'])) {
            $this->redirectWithError('subscription', "Vous devez accepter les conditions d'utilisation.");
            return;
        }

        $subscriptionId = intval($validated['subscription_id']);
        $modePaiement = $validated['mode_paiement'];

        // Vérifier si l'utilisateur a déjà un abonnement actif
        $activeSubscriptions = $this->subscriptionModel->getUserActiveSubscriptions($_SESSION['user']['id']);
        if (!empty($activeSubscriptions)) {
            $this->redirectWithError('subscription', "Vous avez déjà un abonnement actif. Veuillez d'abord résilier votre abonnement actuel.");
            return;
        }

        // Récupérer les informations de l'abonnement
        $subscription = $this->subscriptionModel->getSubscriptionById($subscriptionId);
        if (!$subscription) {
            $this->redirectWithError('subscription', "Cet abonnement n'existe pas ou n'est plus disponible.");
            return;
        }

        // Traitement selon le mode de paiement
        $paymentSuccess = false;
        $paymentId = null;
        $paymentDetails = [
            'mode_paiement' => $modePaiement,
            'montant' => $subscription['price'],
            'user_id' => $_SESSION['user']['id'],
            'subscription_id' => $subscriptionId
        ];

        switch ($modePaiement) {
            case 'carte':
                // Simulation du traitement de paiement par carte
                // En production, intégrer avec un véritable processeur de paiement (Stripe, PayPal, etc.)
                $paymentSuccess = $this->processCardPayment($paymentDetails);
                break;

            case 'paypal':
                // Simulation du traitement PayPal
                $paymentSuccess = $this->processPayPalPayment($paymentDetails);
                break;

            case 'virement':
                // Pour le virement, on crée la souscription en attente
                $paymentSuccess = $this->processTransferPayment($paymentDetails);
                break;

            default:
                $this->redirectWithError('subscription', "Mode de paiement non supporté.");
        }

        if ($paymentSuccess) {
            // Souscrire l'utilisateur
            $result = $this->subscriptionModel->subscribeUser($_SESSION['user']['id'], $subscriptionId, $paymentId);

            if ($result) {
                // Mise à jour de la session utilisateur
                $_SESSION['user']['is_subscribed'] = 1;
                
                // Message de succès adapté au mode de paiement
                $successMessage = "Félicitations ! Vous avez souscrit avec succès à l'abonnement " . $subscription['name'];
                if ($modePaiement === 'virement') {
                    $successMessage .= ". Votre abonnement sera activé dès réception du paiement.";
                } else {
                    $successMessage .= ". Votre abonnement est maintenant actif !";
                }
                
                $this->redirectWithSuccess('auth/profile', $successMessage);
            } else {
                $this->redirectWithError('subscription', "Une erreur est survenue lors de la souscription à l'abonnement.");
            }
        } else {
            $this->redirectWithError('subscription', "Le paiement a échoué. Veuillez réessayer.");
        }
    }

    /**
     * Simule le traitement d'un paiement par carte bancaire
     */
    private function processCardPayment($paymentDetails)
    {
        // Simulation : en production, intégrer avec Stripe, Square, etc.
        // Ici on simule un paiement réussi dans 95% des cas
        return rand(1, 100) <= 95;
    }

    /**
     * Simule le traitement d'un paiement PayPal
     */
    private function processPayPalPayment($paymentDetails)
    {
        // Simulation : en production, intégrer avec l'API PayPal
        // Ici on simule un paiement réussi dans 90% des cas
        return rand(1, 100) <= 90;
    }

    /**
     * Traite un paiement par virement bancaire
     */
    private function processTransferPayment($paymentDetails)
    {
        // Pour le virement, on considère toujours comme "réussi" 
        // car l'activation se fera manuellement après réception du virement
        return true;
    }

    /**
     * Liste les utilisateurs ayant un abonnement actif
     */
    public function users()
    {
        $this->requireAdmin();
        $this->setActiveMenu('subscriptions');

        // Récupérer la liste des utilisateurs avec abonnement
        $sql = "SELECT u.id, u.prenom, u.nom, u.email, a.nom as subscription_name, 
                a.price, ua.date_debut as start_date, ua.date_fin as end_date, ua.status
                FROM users u
                JOIN user_abonnements ua ON u.id = ua.user_id
                JOIN abonnements a ON ua.abonnement_id = a.id
                WHERE ua.status = 'actif' AND ua.date_fin > NOW()
                ORDER BY ua.date_fin ASC";
        
        $db = Database::getInstance();
        $users = $db->findAll($sql);

        $data = ['users' => $users];
        $this->renderView('admin/subscriptions/users', $data, 'admin');
    }
}
