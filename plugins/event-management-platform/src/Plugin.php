<?php

namespace EventManagementPlatform;

use EventManagementPlatform\Database\MigrationManager;
use EventManagementPlatform\Admin\AdminMenu;
use EventManagementPlatform\API\Router as ApiRouter;
use EventManagementPlatform\Frontend\Router as FrontendRouter;

/**
 * Main Plugin Bootstrap Class
 */
class Plugin {
    /**
     * Singleton instance
     */
    private static ?Plugin $instance = null;

    /**
     * Get class instance
     */
    public static function getInstance(): self {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct() {}

    /**
     * Activation Hook Handler
     */
    public static function activate(): void {
        // Run database migrations on activation.
        $migrationManager = new MigrationManager();
        $migrationManager->migrate();

        // Register custom frontend routes before flushing so WordPress stores them.
        $frontendRouter = new FrontendRouter();
        $frontendRouter->addRewriteRules();

        // Flush rewrite rules for custom frontend routing.
        flush_rewrite_rules();
    }

    /**
     * Deactivation Hook Handler
     */
    public static function deactivate(): void {
        flush_rewrite_rules();
    }

    /**
     * Initialize the plugin components
     */
    public function init(): void {
        // Load translations.
        load_plugin_textdomain( 'event-management-platform', false, dirname( plugin_basename( EMP_PATH ) ) . '/languages/' );

        // Check if DB migrations are up to date (for auto-updates).
        $migrationManager = new MigrationManager();
        $migrationManager->maybeMigrate();

        // Initialize Admin components.
        if ( is_admin() ) {
            $adminMenu = new AdminMenu();
            $adminMenu->init();
        }

        // Initialize REST API.
        $apiRouter = new ApiRouter();
        $apiRouter->init();

        // Initialize Frontend Router.
        $frontendRouter = new FrontendRouter();
        $frontendRouter->init();
    }
}
