<?php

/**
 * Contrôleur d'erreurs consolidé et optimisé
 * Gère tous les types d'erreurs HTTP et d'application
 */
class ErrorController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Affiche la page d'erreur 404 (Page non trouvée)
     */
    public function notFound()
    {
        http_response_code(404);
        $data = [
            'title' => 'Page non trouvée - ' . APP_NAME,
            'description' => 'La page que vous avez demandée n\'existe pas.'
        ];

        $this->renderView('error/404', $data);
    }

    /**
     * Affiche une page d'erreur d'accès non autorisé (403)
     */
    public function forbidden()
    {
        http_response_code(403);
        $data = [
            'title' => 'Accès refusé - ' . APP_NAME,
            'description' => 'Vous n\'avez pas les droits nécessaires pour accéder à cette page.'
        ];

        $this->renderView('error/403', $data);
    }

    /**
     * Affiche une page d'erreur pour les erreurs internes du serveur (500)
     */
    public function serverError($message = null)
    {
        http_response_code(500);
        $data = [
            'title' => 'Erreur serveur - ' . APP_NAME,
            'description' => 'Une erreur interne est survenue. Veuillez réessayer ultérieurement.',
            'error_message' => $message
        ];

        $this->renderView('error/500', $data);
    }
}
