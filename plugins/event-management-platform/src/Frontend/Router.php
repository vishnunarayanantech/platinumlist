<?php

namespace EventManagementPlatform\Frontend;

use EventManagementPlatform\Repositories\EventRepository;
use EventManagementPlatform\Repositories\VenueRepository;
use EventManagementPlatform\Repositories\CategoryRepository;

class Router {
    /**
     * Hook custom routing events
     */
    public function init(): void {
        add_action( 'init', [ $this, 'addRewriteRules' ] );
        add_filter( 'query_vars', [ $this, 'registerQueryVars' ] );
        add_action( 'template_redirect', [ $this, 'templateRedirect' ] );
        
        // SEO hooks
        add_filter( 'document_title_parts', [ $this, 'filterSeoTitle' ], 999 );
        add_action( 'wp_head', [ $this, 'renderSeoMetaDescription' ], 1 );
    }

    /**
     * Register Custom Rewrite Rules
     */
    public function addRewriteRules(): void {
        $settings = get_option( 'emp_settings', [ 'enable_frontend' => 1 ] );
        if ( empty( $settings['enable_frontend'] ) ) {
            return;
        }

        // List route
        add_rewrite_rule(
            '^events/?$',
            'index.php?emp_route_type=events_list',
            'top'
        );

        // Single Event route
        add_rewrite_rule(
            '^events/([^/]+)/?$',
            'index.php?emp_route_type=event_single&emp_slug=$matches[1]',
            'top'
        );

        // Single Venue route
        add_rewrite_rule(
            '^venues/([^/]+)/?$',
            'index.php?emp_route_type=venue_single&emp_slug=$matches[1]',
            'top'
        );

        // Single Category route
        add_rewrite_rule(
            '^categories/([^/]+)/?$',
            'index.php?emp_route_type=category_single&emp_slug=$matches[1]',
            'top'
        );
    }

    /**
     * Register query variables with WordPress
     */
    public function registerQueryVars( array $vars ): array {
        $vars[] = 'emp_route_type';
        $vars[] = 'emp_slug';
        return $vars;
    }

    /**
     * Intercept template rendering for custom routes
     */
    public function templateRedirect(): void {
        $route_type = get_query_var( 'emp_route_type' );
        $slug       = get_query_var( 'emp_slug' );

        if ( empty( $route_type ) ) {
            return;
        }

        // Enqueue frontend assets
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueueFrontendAssets' ] );

        switch ( $route_type ) {
            case 'events_list':
                $this->renderEventsList();
                exit;

            case 'event_single':
                $this->renderSingleEvent( $slug );
                exit;

            case 'venue_single':
                $this->renderSingleVenue( $slug );
                exit;

            case 'category_single':
                $this->renderSingleCategory( $slug );
                exit;
        }
    }

    /**
     * Enqueue CSS/JS for Frontend Virtual Pages
     */
    public function enqueueFrontendAssets(): void {
        wp_enqueue_style( 'emp-google-fonts', 'https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap', [], EMP_VERSION );
        wp_enqueue_style( 'emp-frontend-style', EMP_URL . 'assets/css/frontend.css', [], EMP_VERSION );
        wp_enqueue_script( 'emp-frontend-script', EMP_URL . 'assets/js/frontend.js', [ 'jquery' ], EMP_VERSION, true );
    }

    /**
     * Page Renderers
     */

    private function renderEventsList(): void {
        $eventRepo = new EventRepository();
        $catRepo   = new CategoryRepository();
        $venueRepo = new VenueRepository();

        $page     = max( 1, (int) ($_GET['paged'] ?? 1) );
        $settings = get_option( 'emp_settings', [ 'events_per_page' => 10 ] );
        $per_page = $settings['events_per_page'];

        $filters = [
            'search'      => sanitize_text_field( $_GET['s'] ?? '' ),
            'category_id' => ! empty( $_GET['category'] ) ? (int) $_GET['category'] : '',
            'venue_id'    => ! empty( $_GET['venue'] ) ? (int) $_GET['venue'] : '',
            'date_from'   => sanitize_text_field( $_GET['date_from'] ?? '' ),
            'date_to'     => sanitize_text_field( $_GET['date_to'] ?? '' ),
            'status'      => 'published',
        ];

        $results = $eventRepo->paginate( $page, $per_page, $filters );
        
        $events     = $results['items'];
        $total      = $results['total'];
        $pages      = $results['pages'];
        $categories = $catRepo->all();
        $venues     = $venueRepo->all();

        include EMP_PATH . 'templates/frontend/event-list.php';
    }

    private function renderSingleEvent( string $slug ): void {
        $eventRepo = new EventRepository();
        $event = $eventRepo->findBySlug( $slug );

        if ( ! $event || $event->status !== 'published' ) {
            global $wp_query;
            $wp_query->set_404();
            status_header( 404 );
            get_template_part( '404' );
            return;
        }

        // Fetch venue details
        $venue = null;
        if ( $event->venue_id ) {
            $venueRepo = new VenueRepository();
            $venue = $venueRepo->find( $event->venue_id );
        }

        // Fetch category details
        $category = null;
        if ( $event->category_id ) {
            $catRepo = new CategoryRepository();
            $category = $catRepo->find( $event->category_id );
        }

        include EMP_PATH . 'templates/frontend/event-single.php';
    }

    private function renderSingleVenue( string $slug ): void {
        $venueRepo = new VenueRepository();
        
        // Find venue by matching slug-ified name
        $venues = $venueRepo->all();
        $venue = null;
        foreach ( $venues as $v ) {
            if ( sanitize_title( $v->name ) === $slug ) {
                $venue = $v;
                break;
            }
        }

        if ( ! $venue ) {
            global $wp_query;
            $wp_query->set_404();
            status_header( 404 );
            get_template_part( '404' );
            return;
        }

        // Get events for this venue
        $eventRepo = new EventRepository();
        $results = $eventRepo->paginate( 1, 20, [ 'venue_id' => $venue->id, 'status' => 'published' ] );
        $events = $results['items'];

        include EMP_PATH . 'templates/frontend/venue-single.php';
    }

    private function renderSingleCategory( string $slug ): void {
        $catRepo = new CategoryRepository();
        $category = $catRepo->findBySlug( $slug );

        if ( ! $category ) {
            global $wp_query;
            $wp_query->set_404();
            status_header( 404 );
            get_template_part( '404' );
            return;
        }

        // Get events for this category
        $eventRepo = new EventRepository();
        $results = $eventRepo->paginate( 1, 20, [ 'category_id' => $category->id, 'status' => 'published' ] );
        $events = $results['items'];

        include EMP_PATH . 'templates/frontend/category-single.php';
    }

    /**
     * Filter document title for SEO
     */
    public function filterSeoTitle( array $title_parts ): array {
        $route_type = get_query_var( 'emp_route_type' );
        $slug       = get_query_var( 'emp_slug' );

        if ( empty( $route_type ) ) {
            return $title_parts;
        }

        if ( $route_type === 'events_list' ) {
            $title_parts['title'] = __( 'Events Listing', 'event-management-platform' );
        } elseif ( $route_type === 'event_single' && ! empty( $slug ) ) {
            $event = ( new EventRepository() )->findBySlug( $slug );
            if ( $event ) {
                $title_parts['title'] = ! empty( $event->seo_title ) ? $event->seo_title : $event->title;
            }
        } elseif ( $route_type === 'venue_single' && ! empty( $slug ) ) {
            $venues = ( new VenueRepository() )->all();
            foreach ( $venues as $v ) {
                if ( sanitize_title( $v->name ) === $slug ) {
                    $title_parts['title'] = $v->name . ' ' . __( 'Venue', 'event-management-platform' );
                    break;
                }
            }
        } elseif ( $route_type === 'category_single' && ! empty( $slug ) ) {
            $category = ( new CategoryRepository() )->findBySlug( $slug );
            if ( $category ) {
                $title_parts['title'] = $category->name . ' ' . __( 'Events', 'event-management-platform' );
            }
        }

        return $title_parts;
    }

    /**
     * Inject Meta Description tag in HTML head for SEO
     */
    public function renderSeoMetaDescription(): void {
        $route_type = get_query_var( 'emp_route_type' );
        $slug       = get_query_var( 'emp_slug' );

        if ( empty( $route_type ) ) {
            return;
        }

        $description = '';

        if ( $route_type === 'events_list' ) {
            $description = __( 'Browse all upcoming events, concerts, conferences, and bookings on our platform.', 'event-management-platform' );
        } elseif ( $route_type === 'event_single' && ! empty( $slug ) ) {
            $event = ( new EventRepository() )->findBySlug( $slug );
            if ( $event ) {
                $description = ! empty( $event->seo_description ) ? $event->seo_description : $event->short_description;
            }
        } elseif ( $route_type === 'venue_single' && ! empty( $slug ) ) {
            $venues = ( new VenueRepository() )->all();
            foreach ( $venues as $v ) {
                if ( sanitize_title( $v->name ) === $slug ) {
                    $description = sprintf( __( 'Explore events happening at %s in %s, %s.', 'event-management-platform' ), $v->name, $v->city, $v->country );
                    break;
                }
            }
        } elseif ( $route_type === 'category_single' && ! empty( $slug ) ) {
            $category = ( new CategoryRepository() )->findBySlug( $slug );
            if ( $category ) {
                $description = sprintf( __( 'Browse and book events related to %s category.', 'event-management-platform' ), $category->name );
            }
        }

        if ( ! empty( $description ) ) {
            echo '<meta name="description" content="' . esc_attr( wp_strip_all_tags( $description ) ) . '" />' . "\n";
        }
    }
}
