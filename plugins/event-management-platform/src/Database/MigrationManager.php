<?php

namespace EventManagementPlatform\Database;

/**
 * Handles database schema migration and updates.
 */
class MigrationManager {
    const DB_VERSION_OPTION = 'emp_db_version';
    const CURRENT_VERSION    = '1.1.0';

    /**
     * Run all migrations
     */
    public function migrate(): void {
        global $wpdb;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charset_collate = $wpdb->get_charset_collate();

        // 1. Venues Table
        $table_venues = $wpdb->prefix . 'emp_venues';
        $sql_venues = "CREATE TABLE $table_venues (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            address text DEFAULT NULL,
            city varchar(100) DEFAULT NULL,
            country varchar(100) DEFAULT NULL,
            latitude decimal(10,8) DEFAULT NULL,
            longitude decimal(11,8) DEFAULT NULL,
            google_map_url text DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY city_idx (city),
            KEY country_idx (country)
        ) $charset_collate;";

        // 2. Categories Table
        $table_categories = $wpdb->prefix . 'emp_categories';
        $sql_categories = "CREATE TABLE $table_categories (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            slug varchar(255) NOT NULL,
            parent_id bigint(20) unsigned DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY slug_unique (slug),
            KEY parent_idx (parent_id)
        ) $charset_collate;";

        // 3. Events Table
        $table_events = $wpdb->prefix . 'emp_events';
        $sql_events = "CREATE TABLE $table_events (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            slug varchar(255) NOT NULL,
            short_description text DEFAULT NULL,
            full_description longtext DEFAULT NULL,
            venue_id bigint(20) unsigned DEFAULT NULL,
            category_id bigint(20) unsigned DEFAULT NULL,
            status varchar(50) NOT NULL DEFAULT 'draft',
            featured_image_id bigint(20) unsigned DEFAULT NULL,
            start_datetime datetime DEFAULT NULL,
            end_datetime datetime DEFAULT NULL,
            timezone varchar(100) NOT NULL DEFAULT 'UTC',
            seo_title varchar(255) DEFAULT NULL,
            seo_description text DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY slug_unique (slug),
            KEY venue_idx (venue_id),
            KEY category_idx (category_id),
            KEY status_dates_idx (status, start_datetime, end_datetime),
            KEY created_idx (created_at)
        ) $charset_collate;";

        // 4. Event Images Table
        $table_event_images = $wpdb->prefix . 'emp_event_images';
        $sql_event_images = "CREATE TABLE $table_event_images (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            event_id bigint(20) unsigned NOT NULL,
            attachment_id bigint(20) unsigned NOT NULL,
            sort_order int(11) NOT NULL DEFAULT 0,
            PRIMARY KEY  (id),
            KEY event_idx (event_id),
            KEY sort_order_idx (sort_order)
        ) $charset_collate;";

        // 5. Event FAQs Table
        $table_event_faqs = $wpdb->prefix . 'emp_event_faqs';
        $sql_event_faqs = "CREATE TABLE $table_event_faqs (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            event_id bigint(20) unsigned NOT NULL,
            question varchar(255) NOT NULL,
            answer text NOT NULL,
            sort_order int(11) NOT NULL DEFAULT 0,
            PRIMARY KEY  (id),
            KEY event_idx (event_id),
            KEY sort_order_idx (sort_order)
        ) $charset_collate;";

        // 6. Event Tags Table
        $table_event_tags = $wpdb->prefix . 'emp_event_tags';
        $sql_event_tags = "CREATE TABLE $table_event_tags (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            slug varchar(255) NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY slug_unique (slug)
        ) $charset_collate;";

        // 7. Event Tag Map Table
        $table_event_tag_map = $wpdb->prefix . 'emp_event_tag_map';
        $sql_event_tag_map = "CREATE TABLE $table_event_tag_map (
            event_id bigint(20) unsigned NOT NULL,
            tag_id bigint(20) unsigned NOT NULL,
            PRIMARY KEY  (event_id, tag_id),
            KEY tag_idx (tag_id)
        ) $charset_collate;";

        // 8. Organizers Table
        $table_organizers = $wpdb->prefix . 'emp_organizers';
        $sql_organizers = "CREATE TABLE $table_organizers (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            email varchar(255) DEFAULT NULL,
            phone varchar(50) DEFAULT NULL,
            website varchar(255) DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // 9. Event Organizer Map Table
        $table_event_organizer_map = $wpdb->prefix . 'emp_event_organizer_map';
        $sql_event_organizer_map = "CREATE TABLE $table_event_organizer_map (
            event_id bigint(20) unsigned NOT NULL,
            organizer_id bigint(20) unsigned NOT NULL,
            PRIMARY KEY  (event_id, organizer_id),
            KEY organizer_idx (organizer_id)
        ) $charset_collate;";

        // 10. Event Sections Table
        $table_event_sections = $wpdb->prefix . 'emp_event_sections';
        $sql_event_sections = "CREATE TABLE $table_event_sections (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            event_id bigint(20) unsigned NOT NULL,
            title varchar(255) NOT NULL DEFAULT '',
            content longtext NOT NULL,
            sort_order int(11) NOT NULL DEFAULT 0,
            PRIMARY KEY  (id),
            KEY event_idx (event_id),
            KEY sort_order_idx (sort_order)
        ) $charset_collate;";

        // Execute migrations with dbDelta
        dbDelta( $sql_venues );
        dbDelta( $sql_categories );
        dbDelta( $sql_events );
        dbDelta( $sql_event_images );
        dbDelta( $sql_event_faqs );
        dbDelta( $sql_event_tags );
        dbDelta( $sql_event_tag_map );
        dbDelta( $sql_organizers );
        dbDelta( $sql_event_organizer_map );
        dbDelta( $sql_event_sections );

        // Update database version option
        update_option( self::DB_VERSION_OPTION, self::CURRENT_VERSION );
    }

    /**
     * Run migration check on load
     */
    public function maybeMigrate(): void {
        $installed_version = get_option( self::DB_VERSION_OPTION );
        if ( $installed_version !== self::CURRENT_VERSION ) {
            $this->migrate();
        }
    }
}
