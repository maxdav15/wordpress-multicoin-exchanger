<?php
/**
 * Multi-Coin Exchanger WordPress Theme Functions
 * Enqueue styles and scripts, register template, handle AJAX
 */

// Define plugin/theme constants
define('EXCHANGER_DIR', get_template_directory());
define('EXCHANGER_URL', get_template_directory_uri());

/**
 * Enqueue styles and scripts
 */
function exchanger_enqueue_assets() {
    // Enqueue main stylesheet
    wp_enqueue_style(
        'exchanger-style',
        EXCHANGER_URL . '/assets/css/exchanger-style.css',
        array(),
        '1.0.0'
    );
    
    // Enqueue main JavaScript
    wp_enqueue_script(
        'exchanger-js',
        EXCHANGER_URL . '/assets/js/exchanger.js',
        array(),
        '1.0.0',
        true
    );
    
    // Localize script for AJAX
    wp_localize_script('exchanger-js', 'wordpress_exchanger_vars', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('exchanger_nonce'),
        'site_url' => site_url(),
    ));
}
add_action('wp_enqueue_scripts', 'exchanger_enqueue_assets');

/**
 * Register exchange page template
 */
function exchanger_register_template() {
    register_post_type('exchange_page', array(
        'public' => false,
        'show_ui' => false,
    ));
}
add_action('init', 'exchanger_register_template');

/**
 * AJAX handler for processing exchange
 */
function handle_exchange_ajax() {
    // Verify nonce
    check_ajax_referer('exchanger_nonce', 'nonce');
    
    // Get and sanitize POST data
    $from_coin = sanitize_text_field($_POST['from_coin']);
    $to_coin = sanitize_text_field($_POST['to_coin']);
    $from_amount = floatval($_POST['from_amount']);
    $to_amount = floatval($_POST['to_amount']);
    $wallet_address = sanitize_text_field($_POST['wallet_address']);
    $fee = floatval($_POST['fee']);
    $network_fee = floatval($_POST['network_fee']);
    
    // Validate inputs
    if ($from_amount <= 0 || $to_amount <= 0 || empty($wallet_address)) {
        wp_send_json_error(array(
            'message' => 'Invalid transaction details'
        ));
    }
    
    // Generate transaction ID
    $transaction_id = 'TXN-' . time() . '-' . wp_rand(1000, 9999);
    
    // Store transaction in database
    $transaction_data = array(
        'transaction_id' => $transaction_id,
        'from_coin' => $from_coin,
        'to_coin' => $to_coin,
        'from_amount' => $from_amount,
        'to_amount' => $to_amount,
        'wallet_address' => $wallet_address,
        'fee' => $fee,
        'network_fee' => $network_fee,
        'status' => 'pending',
        'user_id' => get_current_user_id(),
        'created_at' => current_time('mysql'),
        'ip_address' => sanitize_text_field($_SERVER['REMOTE_ADDR']),
    );
    
    // Save to database
    global $wpdb;
    $wpdb->insert(
        $wpdb->prefix . 'exchanger_transactions',
        $transaction_data,
        array('%s', '%s', '%s', '%f', '%f', '%s', '%f', '%f', '%s', '%d', '%s', '%s')
    );
    
    // Log the transaction
    error_log('Exchange Transaction: ' . json_encode($transaction_data));
    
    // Here you would typically:
    // 1. Process payment
    // 2. Validate wallet address
    // 3. Call blockchain API to execute transfer
    // 4. Send confirmation email
    
    // Return success response
    wp_send_json_success(array(
        'message' => 'Exchange initiated successfully',
        'transaction_id' => $transaction_id,
        'status' => 'pending'
    ));
}
add_action('wp_ajax_process_exchange', 'handle_exchange_ajax');
add_action('wp_ajax_nopriv_process_exchange', 'handle_exchange_ajax');

/**
 * Create database table for transactions on plugin activation
 */
function exchanger_create_tables() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'exchanger_transactions';
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        transaction_id varchar(50) NOT NULL UNIQUE,
        from_coin varchar(50) NOT NULL,
        to_coin varchar(50) NOT NULL,
        from_amount decimal(20,8) NOT NULL,
        to_amount decimal(20,8) NOT NULL,
        wallet_address varchar(255) NOT NULL,
        fee decimal(10,2) NOT NULL,
        network_fee decimal(10,2) NOT NULL,
        status varchar(20) DEFAULT 'pending',
        user_id bigint(20) DEFAULT 0,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        ip_address varchar(50),
        PRIMARY KEY (id),
        UNIQUE KEY transaction_id (transaction_id),
        KEY status (status),
        KEY user_id (user_id),
        KEY created_at (created_at)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'exchanger_create_tables');

/**
 * Custom page template for exchange page
 */
function add_exchange_template_to_dropdown($post_templates, $wp_theme, $post) {
    if ('page' === $post->post_type) {
        $post_templates['exchange-main.php'] = 'Exchange Interface';
    }
    return $post_templates;
}
add_filter('theme_page_templates', 'add_exchange_template_to_dropdown', 10, 3);

/**
 * Load custom page template
 */
function exchanger_load_template($template) {
    if (is_page() && get_page_template_slug(get_the_ID()) === 'exchange-main.php') {
        return EXCHANGER_DIR . '/templates/exchange-main.php';
    }
    return $template;
}
add_filter('page_template', 'exchanger_load_template');

/**
 * Admin page for viewing transactions
 */
function exchanger_add_admin_menu() {
    add_menu_page(
        'Multi-Coin Exchanger',
        'Exchanger',
        'manage_options',
        'exchanger_dashboard',
        'exchanger_dashboard_page',
        'dashicons-chart-line',
        25
    );
    
    add_submenu_page(
        'exchanger_dashboard',
        'Transactions',
        'Transactions',
        'manage_options',
        'exchanger_transactions',
        'exchanger_transactions_page'
    );
    
    add_submenu_page(
        'exchanger_dashboard',
        'Settings',
        'Settings',
        'manage_options',
        'exchanger_settings',
        'exchanger_settings_page'
    );
}
add_action('admin_menu', 'exchanger_add_admin_menu');

/**
 * Dashboard page callback
 */
function exchanger_dashboard_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'exchanger_transactions';
    
    $total_transactions = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    $pending_transactions = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'pending'");
    $completed_transactions = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'completed'");
    $total_volume = $wpdb->get_var("SELECT SUM(from_amount) FROM $table_name");
    
    echo '<div class="wrap">';
    echo '<h1>Multi-Coin Exchanger Dashboard</h1>';
    echo '<div class="dashboard-grid">';
    
    echo '<div class="card">';
    echo '<h3>Total Transactions</h3>';
    echo '<p class="big-number">' . $total_transactions . '</p>';
    echo '</div>';
    
    echo '<div class="card">';
    echo '<h3>Pending</h3>';
    echo '<p class="big-number">' . $pending_transactions . '</p>';
    echo '</div>';
    
    echo '<div class="card">';
    echo '<h3>Completed</h3>';
    echo '<p class="big-number">' . $completed_transactions . '</p>';
    echo '</div>';
    
    echo '<div class="card">';
    echo '<h3>Volume (BTC)</h3>';
    echo '<p class="big-number">' . number_format($total_volume, 2) . '</p>';
    echo '</div>';
    
    echo '</div>';
    echo '</div>';
    
    // Add some basic styles
    echo '<style>
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .card h3 {
            margin: 0 0 15px 0;
            color: #333;
        }
        .big-number {
            font-size: 32px;
            font-weight: bold;
            color: #6366f1;
            margin: 0;
        }
    </style>';
}

/**
 * Transactions page callback
 */
function exchanger_transactions_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'exchanger_transactions';
    
    // Get all transactions
    $transactions = $wpdb->get_results("
        SELECT * FROM $table_name 
        ORDER BY created_at DESC 
        LIMIT 50
    ");
    
    echo '<div class="wrap">';
    echo '<h1>Exchange Transactions</h1>';
    
    if (empty($transactions)) {
        echo '<p>No transactions found.</p>';
        echo '</div>';
        return;
    }
    
    echo '<table class="widefat">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Transaction ID</th>';
    echo '<th>From</th>';
    echo '<th>To</th>';
    echo '<th>Amount</th>';
    echo '<th>Status</th>';
    echo '<th>User</th>';
    echo '<th>Date</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    foreach ($transactions as $txn) {
        $user = get_userdata($txn->user_id);
        $user_email = $user ? $user->user_email : 'Guest';
        
        $status_class = 'status-' . $txn->status;
        
        echo '<tr>';
        echo '<td><code>' . esc_html($txn->transaction_id) . '</code></td>';
        echo '<td>' . esc_html(strtoupper($txn->from_coin)) . '</td>';
        echo '<td>' . esc_html(strtoupper($txn->to_coin)) . '</td>';
        echo '<td>' . esc_html($txn->from_amount) . '</td>';
        echo '<td><span class="' . $status_class . '">' . esc_html(ucfirst($txn->status)) . '</span></td>';
        echo '<td>' . esc_html($user_email) . '</td>';
        echo '<td>' . esc_html($txn->created_at) . '</td>';
        echo '</tr>';
    }
    
    echo '</tbody>';
    echo '</table>';
    echo '</div>';
    
    echo '<style>
        .status-pending {
            background: #fef3c7;
            color: #92400e;
            padding: 3px 8px;
            border-radius: 3px;
            font-weight: bold;
        }
        .status-completed {
            background: #dcfce7;
            color: #166534;
            padding: 3px 8px;
            border-radius: 3px;
            font-weight: bold;
        }
        .status-failed {
            background: #fee2e2;
            color: #991b1b;
            padding: 3px 8px;
            border-radius: 3px;
            font-weight: bold;
        }
    </style>';
}

/**
 * Settings page callback
 */
function exchanger_settings_page() {
    echo '<div class="wrap">';
    echo '<h1>Exchanger Settings</h1>';
    echo '<form method="post" action="options.php">';
    
    settings_fields('exchanger_settings');
    do_settings_sections('exchanger_settings');
    
    echo '<table class="form-table">';
    echo '<tr>';
    echo '<th><label>Platform Fee (%)</label></th>';
    echo '<td><input type="number" step="0.1" name="exchanger_fee" value="' . esc_attr(get_option('exchanger_fee', 0.5)) . '" /></td>';
    echo '</tr>';
    echo '<tr>';
    echo '<th><label>Network Fee ($)</label></th>';
    echo '<td><input type="number" step="0.01" name="exchanger_network_fee" value="' . esc_attr(get_option('exchanger_network_fee', 2.50)) . '" /></td>';
    echo '</tr>';
    echo '</table>';
    
    submit_button();
    echo '</form>';
    echo '</div>';
}

// Register settings
add_action('admin_init', function() {
    register_setting('exchanger_settings', 'exchanger_fee');
    register_setting('exchanger_settings', 'exchanger_network_fee');
});
