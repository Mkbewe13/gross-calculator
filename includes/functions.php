<?php

add_action('post_updated', 'updateGrossCalculationContent', 10, 3);
/**
 * Update gross_calculation post type content, when post is edited based on meta values.
 *
 * @param $post_id
 * @param $post
 * @param $update
 * @return void
 */
function updateGrossCalculationContent($post_id, $post_after, $post_before): void
{

    if ($post_after->post_type !== \GrossCalculator\Wordpress\PostTypes\GrossCalculation::getGrossCalculationCPTName()) {
        return;
    }

    $productName = get_post_meta($post_id, 'product_name', true);
    $net = get_post_meta($post_id, 'net_value', true);
    $vat = get_post_meta($post_id, 'vat_rate', true);

    if (!$productName || !$net || !$vat) {
        $postData = array(
            'ID' => $post_id,
            'post_content' => "Dane produktu są niekompletne",
        );
        if ($postData['post_content'] != "Dane produktu są niekompletne") {
            wp_update_post($postData);
            return;
        }
    }

    $tax = 0;
    if ($vat != 0) {
        $tax = ($vat / 100) * $net;
    }

    $gross = $net + $tax;

    $postData = array(
        'ID' => $post_id,
        'post_content' => sprintf('Cena produktu %s, wynosi: %.2f zł brutto, kwota podatku to %.2f zł.'
            , $productName
            , $gross,
            $tax),
        'meta_input' => [
            'gross_value' => $gross,
            'tax' => $tax,
        ]
    );

    if ($postData['post_content'] != $post_after->post_content) {
        wp_update_post($postData);
    }


}

add_action('wp_enqueue_scripts', 'registerGrossCalculatorCss');
/**
 * Enqueue plugin style-file
 */
function registerGrossCalculatorCss()
{
    wp_register_style('gross-calc-style', plugins_url('resources/css/style.css', __FILE__));
    wp_enqueue_style('gross-calc-style');
}

add_action('admin_menu', 'gross_calc_readme_submenu_page');
/**
 * Adds a submenu page under a custom post type parent.
 */
function gross_calc_readme_submenu_page() {
    add_submenu_page(
        'edit.php?post_type=' . \GrossCalculator\Wordpress\PostTypes\GrossCalculation::getGrossCalculationCPTName(),
        'Readme',
        'Readme',
        'manage_options',
        'gross-calc-readme',
        'gross_calc_readme_page_callback'
    );
}

/**
 * Display callback for the submenu page.
 */
function gross_calc_readme_page_callback() {
    printf('<div class="wrap">
        <h1>Informacje</h1>
        <p>Aby dodać formularz z kalkulatorem dodaj go za pomocą shortcodu.</p>
        <p>Umieść <b>[gross_calculator]</b> na wybranej stronie lub w wybranym poście i gotowe!</p>
    </div>');
}

