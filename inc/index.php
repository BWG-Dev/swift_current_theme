<?php

function swc_enqueue_scripts() {
    wp_enqueue_script('jquery-ui-autocomplete');

    if (is_checkout()) {
        wp_enqueue_style('flatpickr-css', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css');
        wp_enqueue_script('flatpickr-js', 'https://cdn.jsdelivr.net/npm/flatpickr', [], null, true);
    }

    wp_enqueue_script('swc-autocomplete', get_stylesheet_directory_uri() . '/inc/main.js', ['jquery', 'jquery-ui-autocomplete'], null, true);

    wp_enqueue_style('swc-style',  get_stylesheet_directory_uri() . '/inc/style.css',  [],  '1.0.0' );

    wp_localize_script('swc-autocomplete', 'swcAjax', [
        'ajax_url' => admin_url('admin-ajax.php'),
    ]);

    wp_enqueue_style('jquery-ui-style', 'https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css');
}
add_action('wp_enqueue_scripts', 'swc_enqueue_scripts');

$service_products = array(
    '2 Gig Plan' => '1260',
    '1 Gig Plan' => '1478',
    '500 MB Plan' => '1475',
    '250 MB Plan' => '1476',
    'Experience IQ Security' => '1471',
    'Wi-Fi Extender' => '1477',
    'Outdoor Wi-Fi' => '1464',
    'Contact about security services' => '',
    'Residential Voice Service' => '1472',
    'Bark' => '1470 ',
    'Phone Service' => '',
);

//Render the query parameter in case it exists
function render_query_param_shortcode($atts) {
    // Define default attributes
    $atts = shortcode_atts( array(
        'key' => '',         // URL query parameter key
        'default' => '',   // Default value if param is missing
    ), $atts );

    // Get the value from the query string
    $value = isset($_GET[$atts['key']]) ? sanitize_text_field($_GET[$atts['key']]) : $atts['default'];

    return esc_html($value);
}

//Survey output 
add_shortcode('get_param', 'render_query_param_shortcode');
function render_smart_form_shortcode() {
    ob_start();
    ?>
    <style>
        .smart-form-wrapper {
            max-width: 800px;
            margin: 0 auto;
            font-family: sans-serif;
        }
        .user-type-options {
            display: flex;
            gap: 20px;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .user-type-card {
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            flex: 1;
            cursor: pointer;
            transition: border-color 0.3s;
            position: relative;
        }
        .user-type-card input[type="radio"] {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }
        .user-type-card img {
            max-width: 100%;
            height: 120px;
            background: #eee;
            margin-bottom: 10px;
        }
        .user-type-card label {
            display: block;
            cursor: pointer;
        }
        .user-type-card.active {
            border-color: #0073aa;
        }
        .smart-form-group {
            display: none;
            margin-bottom: 20px;
        }
        .smart-form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .smart-form-group select {
            width: 100%;
            padding: 8px;
        }
        .smart-form-button {
            margin-top: 20px;
            display: none;
        }
    </style>

    <div class="smart-form-wrapper">
        <div class="user-type-options">
            <?php
            $user_types = [
                'heavy' => ['name' => 'Heavy User', 'desc' => 'My Home is my office most of the time. Video calls and meetings are common. We spend a lot of time online gaming and have multiple users.', 'label' => 'Home Offices, high-resolution streaming and video games'],
                'medium' => ['name' => 'Medium User', 'desc' => 'My family takes advantage of the latest technology that supports our online lifestyle. Out home is our office and our virtual learning center.', 'label' => 'Families with lots of devices and multiple users'],
                'light' => ['name' => 'Light User', 'desc' => 'My lifestyle requires staying connected with family and friends and streaming my favorite show at night.', 'label' => 'Casual online use streaming tv and movies'],
            ];
            foreach ($user_types as $key => $data): ?>
                <div class="user-type-card" data-value="<?php echo esc_attr($key); ?>">
                    <img src="https://via.placeholder.com/150" alt="<?php echo esc_attr($data['name']); ?>">
                    <label>
                        <input type="radio" name="user_type" value="<?php echo esc_attr($key); ?>">
                        <strong><?php echo esc_html($data['label']); ?></strong><br>
                        <small><?php echo esc_html($data['desc']); ?></small>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>

        <?php
        $fields = [
            "work_internet" => ["How are you using the internet for work?", [
                "I work at home",
                "I work at the office",
                "I have a hybrid schedule"
            ]],
            "family" => ["Tell us about your family", [
                "Just Adults",
                "Adults and Toddlers (0-3 yrs)",
                "Adults and Young Kids (3-10yrs)",
                "Adults and Teenagers under 18"
            ]],
            "learning" => ["How My Family Learns", [
                "At School During the Day",
                "At Home",
                "At Home on Occassion",
                "No Home Learning"
            ]],
            "wifi_coverage" => ["Questions about your home to Ensure Complete Wi-Fi Coverage", [
                "In town, no outbuildings",
                "In town, with outbuildings",
                "Rural with a few acres",
                "Rural with outbuildings"
            ]],
            "residence_size" => ["", [
                "Smaller Residence, Less than 2000 sq ft",
                "Large Residence, 2,000-2,500 sq ft",
                "Very Large Residence, 2,500 sq ft or more"
            ]],
            "security" => ["How Do You Want Your Home Secured?", [
                "I am interested in security for my home, contact me",
                "I have my own security cameras and sensors",
                "I’m not interested in home security at this time"
            ]],
            "landline" => ["Do You Need a secure land line phone service?", [
                "Yes, bring my existing phone number to Swiftcurrent’s $24.95 voice service",
                "No thank you"
            ]]
        ];

        foreach ($fields as $key => $field) {
            echo '<div class="smart-form-group" id="group_' . esc_attr($key) . '">';
            if (!empty($field[0])) {
                echo '<label for="' . esc_attr($key) . '">' . esc_html($field[0]) . '</label>';
            }
            echo '<select name="' . esc_attr($key) . '" id="' . esc_attr($key) . '">';
            echo '<option value="">-- Please choose an option --</option>';
            foreach ($field[1] as $option) {
                echo '<option value="' . esc_attr($option) . '">' . esc_html($option) . '</option>';
            }
            echo '</select>';
            if($key !== 'landline' && $key !== 'wifi_coverage'){
                echo '<hr>';
            }

            echo '</div>';
        }
        ?>
        <input type="hidden" id="namecr_field" name="namecr" value="<?php echo esc_attr($_GET['namecr']); ?>">
        <input type="hidden" id="addr_field" name="addr_field" value="<?php echo esc_attr($_GET['addr']); ?>">
        <input type="hidden" id="addr_id_field" name="addr_field" value="<?php echo esc_attr($_GET['id']); ?>">
        <input type="hidden" id="email_field" name="addr_field" value="<?php echo esc_attr($_GET['email']); ?>">
        <input type="hidden" id="dist_field" name="addr_field" value="<?php echo esc_attr($_GET['dist']); ?>">
        <div class="smart-from-group button-action">
            <button type="button" class="smart-form-button">Build <?php echo esc_attr($_GET['namecr']); ?> Family Service Plan</button>
        </div>
    </div>

    <?php
    return ob_get_clean();
}
add_shortcode('smart_form', 'render_smart_form_shortcode');

//Survey logic
add_action('wp_ajax_process_smart_form', 'handle_smart_form_logic');
add_action('wp_ajax_nopriv_process_smart_form', 'handle_smart_form_logic');

function handle_smart_form_logic() {
    $service_products = array(
        '2 Gig Plan' => '1260',
        '1 Gig Plan' => '1478',
        '500 MB Plan' => '1475',
        '250 MB Plan' => '1476',
        'Experience IQ Security' => '1471',
        'Wi-Fi Extender' => '1477',
        'Outdoor Wi-Fi' => '1464',
        'Contact about security services' => '',
        'Residential Voice Service' => '1472',
        'Bark' => '1470 ',
        'Phone Service' => '',
    );

    $product_ids = array(); // This will store what to add
    $addons = [];

    $name = sanitize_text_field($_POST['namecr']);
    $address = sanitize_text_field($_POST['addr']);
    $district = sanitize_text_field($_POST['dist']);
    $address_id = sanitize_text_field($_POST['address_id']);
    $email = sanitize_text_field($_POST['email']);

    // Start by clearing the cart
    WC()->cart->empty_cart();

    $user_type = sanitize_text_field($_POST['user_type']);
    $internet_use = sanitize_text_field($_POST['internet_use']);
    $family_type = sanitize_text_field($_POST['family_type']);
    $learning = sanitize_text_field($_POST['learning']);
    $wifi_coverage = sanitize_text_field($_POST['wifi_area']);
    $residence_size = sanitize_text_field($_POST['residence_size']);
    $security = sanitize_text_field($_POST['security_interest']);
    $landline = sanitize_text_field($_POST['landline']);


    // Base Plan based on user_type
    if ($user_type === 'heavy') {
        $product_ids[] = $service_products['2 Gig Plan'];
    } elseif ($user_type === 'medium') {
        $product_ids[] = $service_products['1 Gig Plan'];
    } elseif ($user_type === 'light') {
        $product_ids[] = $service_products['500 MB Plan'];
    }

    // Internet use overrides
    if ($internet_use === 'I work at home') {
        $product_ids = [$service_products['2 Gig Plan']];
        $addons[] = $service_products['Experience IQ Security'];
    } elseif ($internet_use === 'I have a hybrid schedule') {
        if (!in_array($service_products['2 Gig Plan'], $product_ids)) {
            $product_ids = [$service_products['1 Gig Plan']];
        }
        $addons[] = $service_products['Experience IQ Security'];
    }

    // Family Type
    if ($family_type !== 'Just Adults') {
        $addons[] = $service_products['Experience IQ Security'];
        if (in_array($family_type, ['Adults and Young Kids (3-10yrs)', 'Adults and Teenagers under 18'])) {
            $addons[] = $service_products['Bark'];
        }
    }

    // Learning
    if ($learning === 'At Home') {
        if (!in_array($service_products['2 Gig Plan'], $product_ids) && !in_array($service_products['1 Gig Plan'], $product_ids)) {
            $product_ids = [$service_products['1 Gig Plan']];
        }
    }

    // Wi-Fi coverage logic
    if ($wifi_coverage !== 'In town, no outbuildings') {
        $addons[] = $service_products['Outdoor Wi-Fi'];
    }

    $wifi_extender_double =  false;

    // Residence size: Handle the Wi-Fi Extender case
    if (in_array($residence_size, ['Large Residence, 2,000-2,500 sq ft', 'Very Large Residence, 2,500 sq ft or more'])) {
        $addons[] = $service_products['Wi-Fi Extender'];
        $wifi_extender_double = true;
    }

    if ($landline == 'Yes, bring my existing phone number to Swiftcurrent’s $24.95 voice service') {
        $addons[] = '1472';
    }


    // Security Interest
    if ($security === 'I have my own security cameras and sensors') {
        if (!in_array($service_products['2 Gig Plan'], $product_ids)) {
            $product_ids = [$service_products['1 Gig Plan']];
        }
    }

    if ($security === 'I am interested in security for my home, contact me') {
        WC()->session->set('smartform_security_interest', 'User is interested in security services. Contact required.');
    }


    // Final product list: deduplicate and add products to cart
    $final_product_ids = array_unique(array_merge($product_ids, $addons));

    foreach ($final_product_ids as $product_id) {
        $qty = $product_id === $service_products['Wi-Fi Extender'] && $wifi_extender_double ? 2 : 1;
        if (!empty($product_id)) {
            WC()->cart->add_to_cart($product_id, $qty);
        }
    }

    WC()->session->set( 'smartform_namecr', $name );
    WC()->session->set( 'smartform_addr', $address );
    WC()->session->set( 'smartform_addr_id', $address_id );
    WC()->session->set( 'smartform_email', $email );
    WC()->session->set( 'smartform_dist', $district );

    wp_send_json_success(['redirect_url' => wc_get_cart_url() . '?smart_form=true']);
}

add_action('woocommerce_checkout_create_order', function($order, $data) {
    $security_interest = WC()->session->get('smartform_security_interest');
    if ($security_interest) {
        $order->update_meta_data('_smartform_security_interest', $security_interest);
    }
}, 10, 2);

add_action('woocommerce_before_checkout_billing_form', 'add_copy_service_address_link');
function add_copy_service_address_link() {
    echo '<p><a href="#" id="copy_service_address" style="font-weight:bold;">📋 Copy from Service Address</a></p>';
}

add_action('wp_ajax_get_smartform_address', 'get_smartform_address');
add_action('wp_ajax_nopriv_get_smartform_address', 'get_smartform_address');

function get_smartform_address() {
    global $wpdb;


    $name = WC()->session->get( 'smartform_namecr' );
    $addr_id = WC()->session->get( 'smartform_addr_id' );
    $email = WC()->session->get( 'smartform_email' );

    $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM swc_locations WHERE nisc_srv_loc_nbr = %d", $addr_id), ARRAY_A);
    if ($row) {
        wp_send_json_success(array('email' =>$email, 'name' => $name, 'address' => $row['nisc_addr1'], 'city' => $row['nisc_city'], 'state' => $row['nisc_st'], 'zip' => $row['nisc_zip'], 'district' => $row['swc_ftech_district']));
    } else {
        wp_send_json_error(['message' => 'Address not found.']);
    }
}

//Ajjax call - address suggestions
add_action('wp_ajax_nopriv_swc_address_search', 'swc_address_search');
add_action('wp_ajax_swc_address_search', 'swc_address_search');

function swc_address_search() {
    global $wpdb;
    $query = sanitize_text_field($_POST['query']);
    $table = 'swc_locations';

    $sql = $wpdb->prepare("SELECT DISTINCT nisc_srv_loc_nbr, nisc_addr1, nisc_city, nisc_st, nisc_zip, swc_ftech_district 
                           FROM $table 
                           WHERE CONCAT(nisc_addr1, ' ', nisc_city, ' ', nisc_st, ' ', nisc_zip) 
                           LIKE %s 
                           LIMIT 10", '%' . $wpdb->esc_like($query) . '%');

    $results = $wpdb->get_results($sql);
    $data = [];

    foreach ($results as $row) {
        $address = "{$row->nisc_addr1}, {$row->nisc_city}, {$row->nisc_st} {$row->nisc_zip}";
        $data[] = ['label' => $address, 'value' => $address, 'district' => $row->swc_ftech_district, 'address_id' => $row->nisc_srv_loc_nbr];
    }

    if (empty($data)) {
        $data[] = ['label' => 'NO MATCH', 'value' => 'Address not found'];
    }

    wp_send_json($data);
}

//Adding the user data to the interested table
add_action('wp_ajax_nopriv_swc_add_interested', 'swc_add_interested');
add_action('wp_ajax_swc_add_interested', 'swc_add_interested');

function swc_add_interested() {
    global $wpdb;
    $table = 'swc_interested';

    $first  = sanitize_text_field($_POST['first']);
    $last   = sanitize_text_field($_POST['last']);
    $email   = sanitize_text_field($_POST['email']);
    $address = sanitize_text_field($_POST['address']);

    $wpdb->insert($table, [
        'first'  => $first,
        'last'   => $last,
        'email'   => $email,
        'address'=> $address
    ]);


    wp_send_json_success();
}

//Setting up the session to add the adjustment
add_action('template_redirect', function() {
    if ((is_checkout() || is_cart() ) && isset($_GET['smart_form']) && $_GET['smart_form'] === 'true') {
        WC()->session->set('smart_form_flag', true);
    }
});

//Calculate the amount that need to be applied in order to get the $99 fee;
add_action('woocommerce_cart_calculate_fees', function($cart) {
    if (is_admin() || !(is_checkout() || is_cart())) return;

    if (!WC()->session->get('smart_form_flag')) return;

    $subtotal = $cart->get_subtotal();
    $target_total = 99;

    if ($subtotal != $target_total) {
        $adjustment = $target_total - $subtotal;
        $cart->add_fee('Smart Plan Adjustment', $adjustment, false);
    }
});


//Add  the timeslot date *required

add_filter('woocommerce_checkout_fields', 'add_timetap_slot_field_to_checkout');
function add_timetap_slot_field_to_checkout($fields) {
    $slots = get_timetap_slots_for_checkout()['all_slots'];
    //$slots = [];
    if (!empty($slots)) {
        /*$select_options = ['' => 'Choose a time slot'];
        foreach ($slots as $key => $slot) {
            $select_options[$key] = $slot['label'];
        }

        $fields['billing']['timetap_slot'] = [
            'type'     => 'select',
            'label'    => __('Select Appointment Time', 'woocommerce'),
            'required' => true,
            'options'  => $select_options,
        ];*/

        $fields['billing']['appointment_date'] = [
            'type'        => 'text',
            'label'       => __('Select Appointment Date', 'woocommerce'),
            'required'    => true,
            'class'       => ['form-row-first'],
            'input_class' => ['timetap-date']
        ];

        // Time dropdown field (populated by JS after date is picked)
        $fields['billing']['timetap_slot'] = [
            'type'        => 'select',
            'label'       => __('Select Appointment Time', 'woocommerce'),
            'required'    => true,
            'class'       => ['form-row-last'],
            'options'     => ['' => __('Select a time')],
            'input_class' => ['timetap-time']
        ];

        // 3. Store all slot details in session for retrieval after order is placed
        WC()->session->set('timetap_all_slots', $slots);
    }

    return $fields;
}

add_action('wp_footer', 'custom_timetap_script');
function custom_timetap_script() {
    if (!is_checkout()) return;

    $available_slots = get_timetap_slots_for_checkout()['dates'];

    ?>
    <script>
        jQuery(function($) {
            const availableSlots = <?php echo json_encode($available_slots); ?>;
            const $date = $('.timetap-date');
            const $time = $('.timetap-time');

            $date.flatpickr({
                dateFormat: "Y-m-d",
                enable: Object.keys(availableSlots),
                onChange: function(selectedDates, dateStr) {
                    $time.empty().append('<option value="">Select a time</option>');

                    if (availableSlots[dateStr]) {
                        $.each(availableSlots[dateStr], function(_, timeRaw) {
                            let timeStr = timeRaw.toString().padStart(4, '0'); // e.g., 830 -> "0830"
                            let hours = parseInt(timeStr.substring(0, 2), 10);
                            let minutes = timeStr.substring(2);
                            let suffix = hours >= 12 ? 'PM' : 'AM';
                            let displayHours = hours % 12 || 12; // convert to 12-hour format
                            let timeFormatted = `${displayHours}:${minutes} ${suffix}`;
                            let key = `${dateStr}|${timeRaw}`;
                            $time.append(`<option value="${key}">${timeFormatted}</option>`);
                        });
                    }
                }
            });
        });
    </script>
    <?php
}

function get_timetap_slots_for_checkout() {
    $cached = get_transient('timetap_checkout_slots');
    //if ($cached) return $cached;

    $locationId = '498109';
    $serviceId = '725333';

    $district   = WC()->session->get('smartform_dist');


    if(!empty($district) && intval($district) == 2){
        $serviceId = '725510';
    }

    if(!empty($district) && intval($district) == 3){
        $serviceId = '725511';
    }


    $token = sc_timetap_get_token();

    if(empty($token)){
        return [];
    }

    $slots_data = sc_timetap_get_slots($token, $locationId, $serviceId );

    $formatted_slots = [];
    $slots_dates = [];

    foreach ($slots_data as $day) {

        if (!empty($day['timeSlots'])) {
            foreach ($day['timeSlots'] as $slot) {

                $slots_dates[$slot['clientStartDate']][] =  $slot['clientStartTime'];

                $s_time = sc_convert_military_to_std($slot['startTime']);
                $e_time = sc_convert_military_to_std($slot['endTime']);
                $date = DateTime::createFromFormat('Y-m-d', $day['date']);
                $label = $date->format('l, F j') . '  ' . $s_time . ' - ' . $e_time;
                $value = $slot['clientStartDate'] . '|' . $slot['clientStartTime'];

                $unit = $slot['units'][0] ?? [];
                $staff = [
                    'professionalId' => $unit['professionalId'] ?? null,
                ];

                $reason = [
                    'reasonId' => $unit['reasonId'] ?? null,
                ];

                //Create the appoiment object that will be used to create book the slot
                $formatted_slots[$value] = array(
                        'label' => $label,
                        'startDate'               => $slot['staffStartDate'], //ok.
                        'endDate'               => $slot['staffEndDate'], //ok.
                        'startTime'               => $slot['startTime'], //ok.
                        'endTime'    => $slot['endTime'], //ok.
                        'businessId'               => 361484, //ok.
                        'location'              => array('locationId' => $unit['locationId']), //ok.
                        'staff'              => $staff, //ok.
                        'reason'              => $reason, //ok.
                        'clientStartDate'         => $slot['clientStartDate'],//ok.
                        'clientEndDate'           => $slot['clientEndDate'], //ok.
                        'clientStartTime'         => $slot['clientStartTime'], //ok.
                        'clientEndTime'           => $slot['clientEndTime'],//ok.
                        'remindStaffSmsHrs'       => 1, //ok.
                        'remindClientSmsHrs'      => 1, //ok.
                        'staffReminderHours'      => 4, //ok.
                        'clientReminderHours'     => 4, //ok.
                        'sendConfirmationToClient'     => true, //ok.
                        'sendConfirmationToStaff'     => true, //ok.
                       // 'client'                  => array( 'clientId' => $client_id ),
                        'status'                  => 'OPEN', //ok.
                );
            }
        }
    }

    // Cache for 10 minutes
    set_transient('timetap_checkout_slots', $formatted_slots, 10 * MINUTE_IN_SECONDS);

    return array('all_slots' => $formatted_slots, 'dates' => $slots_dates);
}


add_action('woocommerce_checkout_order_processed', 'sc_save_timetap_appointment_data_to_order', 20, 2);
function sc_save_timetap_appointment_data_to_order($order_id, $posted_data) {

    if (empty($_POST['timetap_slot'])) return;

    $selected_key = sanitize_text_field($_POST['timetap_slot']);
    $all_slots = WC()->session->get('timetap_all_slots', []);
    $addr   = WC()->session->get('smartform_addr');


    update_post_meta($order_id, 'timetap_slot', $selected_key);
    update_post_meta($order_id, 'timetap_addr', $addr);


    if (isset($all_slots[$selected_key])) {
        $slot_data = $all_slots[$selected_key];

        // Store full appointment details as meta
        update_post_meta($order_id, '_timetap_appointment_data', $slot_data);

        // Optional: make a readable version for admin viewing
        update_post_meta($order_id, '_timetap_appointment_label', $slot_data['label']);
    }
}


add_action('woocommerce_thankyou', 'sc_handle_timetap_appointment_and_display', 10, 1);

function sc_handle_timetap_appointment_and_display($order_id) {
    if (!$order_id) return;

    WC()->session->__unset('smart_form_flag');

    $order = wc_get_order($order_id);

    if (!$order) return;

    $slot_key = get_post_meta($order_id, 'timetap_slot', true);
    if (empty($slot_key)) return;

    // Get the cached slot data (was saved in transient during checkout)
    $slots  = WC()->session->get('timetap_all_slots', []);

    $slot = $slots[$slot_key] ?? null;

    if (empty($slot)) {
        echo '<p><strong>Error:</strong> Appointment slot data not found.</p>';
        return;
    }

    // Prepare client data
    $client = [
        'first_name' => $order->get_billing_first_name(),
        'last_name'  => $order->get_billing_last_name(),
        'email'      => $order->get_billing_email(),
    ];

    $token = sc_timetap_get_token();
    if (empty($token)) {
        echo '<p><strong>Error:</strong> Could not authenticate with TimeTap.</p>';
        return;
    }

    // Get or create the client
    $client_id = sc_timetap_client_id($token, $client['email']);
    if (empty($client_id)) {
        $client_id = sc_timetap_create_timetap_client($client, $token);
    }

    if (empty($client_id)) {
        echo '<p><strong>Error:</strong> Could not create or find the client in TimeTap.</p>';
        return;
    }

    // Add client to the appointment slot
    $slot['client'] = ['clientId' => $client_id];

    $calendar_id = get_post_meta($order_id, '_timetap_calendar_id', true);
    // Create the appointment
    if(empty($calendar_id)){
        $appointment_response = sc_temetap_create_appointment($slot, $token);
        update_post_meta($order_id, '_timetap_calendar_id', $appointment_response['calendarId']);
    }

    if (!empty($appointment_response['calendarId']) || !empty($calendar_id)) {
        echo '<h2>Your Appointment Has Been Booked</h2>';
        echo '<p><strong>Date:</strong> ' . date('F j, Y', strtotime($slot['clientStartDate'])) . '</p>';
        echo '<p><strong>Time:</strong> ' . sc_convert_military_to_std($slot['clientStartTime']) . ' - ' . sc_convert_military_to_std($slot['clientEndTime']) . '</p>';
    }
}

add_action('woocommerce_admin_order_data_after_order_details', function($order) {

    $note = $order->get_meta('_smartform_security_interest');
    if ($note) {
        echo '<p>.</p><p style="margin-top:20px;"><strong>Security Interest:</strong> ' . esc_html($note) . '</p>';
    }

    $appointment = get_post_meta($order->get_id(),'_timetap_appointment_data', true);
    $appointment_label = get_post_meta($order->get_id(),'_timetap_appointment_label', true);
    $addr = get_post_meta($order->get_id(),'timetap_addr', true);


    if ($appointment) {
        echo '<p><strong>Appointment:</strong> '. $appointment_label .'</p>';
        echo '<p><strong>Service address:</strong> '. $addr .'</p>';
    }
});

add_action( 'woocommerce_proceed_to_checkout', 'custom_button_before_checkout', 5 );
function custom_button_before_checkout() {

    $namecr = WC()->session->get('smartform_namecr');
    $addr   = WC()->session->get('smartform_addr');
    $district   = WC()->session->get('smartform_dist');
    $smart_session   = WC()->session->get('smart_form_flag');
    $email   = WC()->session->get('smartform_email');
    $id   = WC()->session->get('smartform_addr_id');
    $smart_form   = isset($_GET['smart_form']) && $_GET['smart_form'] === 'true';


    // Bail if empty
    if ( empty( $namecr ) || empty( $addr ) ) {
        return;
    }

    // Build URL with query parameters
    $url = add_query_arg( array(
        'namecr' => urlencode( $namecr ),
        'addr'   => urlencode( $addr ),
        'dist'   => urlencode( $district ),
        'email'   => urlencode( $email ),
        'id'   => urlencode( $id ),
    ), site_url( '/service-available/' ) ); // replace with actual page slug

   if($smart_form || $smart_session){
       echo '<a href="' . esc_url( $url ) . '" class="button choose-different-plan" style="margin-bottom:10px; display:inline-block;text-align:center;background:white;">← Choose a different plan</a>';
   }
}

//Hide the smart plan adjustment text and cahnge the Total to Due today
add_filter( 'woocommerce_cart_hide_zero_taxes', '__return_true' );
add_filter( 'woocommerce_cart_totals_fee_html', 'hide_cart_fee_html', 10, 2 );
add_filter( 'woocommerce_cart_totals_before_order_total', 'remove_fee_row_from_cart', 10, 1 );
add_action( 'wp_head', 'hide_fee_row_css_on_cart_checkout', 1 );

function hide_fee_row_css_on_cart_checkout() {

    if(WC()->session->get('smart_form_flag') && is_checkout()){
        ?>
        <style>
            tr.fee, tr.cart-fee, .woocommerce-checkout-review-order-table .fee {
                display: none !important;
            }
        </style>
        <?php
    }
}

function hide_cart_fee_html( $html, $fee ) {
    // Optionally conditionally hide only certain fees by name
    if ( $fee->name === 'Smart Plan Adjustment' ) {
        return '';
    }
    return $html;
}

function remove_fee_row_from_cart( $cart ) {
    if(is_cart()  && isset($_GET['smart_form']))
    ?>
    <style>
        tr.fee {
            display: none !important;
        }
    </style>
<?php
    ob_start();
}

add_filter( 'gettext', 'rename_cart_total_label', 20, 3 );
add_filter( 'woocommerce_cart_totals_order_total_html', 'custom_due_today_total_html', 10, 1 );

function rename_cart_total_label( $translated_text, $text, $domain ) {

    if ( (is_cart() || is_checkout()) && WC()->session->get('smart_form_flag') ) {
        if ( $translated_text === 'Total' && $domain === 'woocommerce' ) {
            $translated_text = 'Amount Due Today';
        }

        if ( $translated_text === 'Subtotal' && $domain === 'woocommerce' ) {
            $translated_text = 'Monthly Total';
        }
    }

    return $translated_text;
}

// Optional: emphasize styling for total line
function custom_due_today_total_html( $value ) {
    return '<strong>' . $value . '</strong>';
}

add_filter('woocommerce_cart_item_remove_link', 'prevent_product_removal_from_cart', 10, 2);
function prevent_product_removal_from_cart($link, $cart_item_key) {
    $cart_item = WC()->cart->get_cart_item($cart_item_key);
    $product_id_to_protect = [1260, 1478, 1479,1476,1475]; // Replace with your product ID

    if (in_array($cart_item['product_id'], $product_id_to_protect)) {
        return ''; // Removes the "Remove" link
    }

    return $link;
}

add_action('woocommerce_after_cart', 'custom_cart_upsell_vertical_display');

function custom_cart_upsell_vertical_display() {
    $excluded_product_ids = array(123, 456); // Product IDs you want to exclude
    $upsell_ids = [];

    foreach (WC()->cart->get_cart() as $cart_item) {
        $product = wc_get_product($cart_item['product_id']);

        if (in_array($product->get_id(), $excluded_product_ids)) {
            continue;
        }

        $product_upsells = $product->get_upsell_ids();
        $upsell_ids = array_merge($upsell_ids, $product_upsells);
    }

    $upsell_ids = array_unique(array_filter($upsell_ids));

    if (empty($upsell_ids)) {
        return;
    }

    echo '<div class="custom-upsell-products" style="margin-top:40px;">';
    echo '<h2 style="margin-bottom:20px;">You may also like</h2>';

    $args = [
        'post_type' => 'product',
        'post__in' => $upsell_ids,
        'posts_per_page' => -1,
        'orderby' => 'post__in',
    ];

    $query = new WP_Query($args);

    $cont = 0;

    while ($query->have_posts()) : $query->the_post();
        global $product;

        if($cont == 3){
            break;
        }

        echo '<div class="upsell-product" style="display:flex; align-items:center; margin-bottom:20px; border:1px solid #ccc; padding:10px; border-radius:8px;">';

        echo '<div class="upsell-image" style="flex:0 0 100px; margin-right:15px;">';
        echo woocommerce_get_product_thumbnail('woocommerce_thumbnail');
        echo '</div>';

        echo '<div class="upsell-info" style="flex:1;">';
        echo '<h3 style="margin:0 0 10px;">' . get_the_title() . '</h3>';
        woocommerce_template_loop_add_to_cart();
        echo '</div>';

        echo '</div>';
        $cont++;
    endwhile;

    wp_reset_postdata();

    echo '</div>';
}