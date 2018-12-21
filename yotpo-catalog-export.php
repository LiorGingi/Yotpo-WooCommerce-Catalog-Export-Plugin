<?php
/**
* @package YotpoCatalogExportPlugin
*/
/*
Plugin Name: Yotpo Product Catalog Export
Plugin URI: https://github.com/LiorGingi/Yotpo-WooCommerce-Catalog-Export-Plugin
Description: This plugin allows you to export the WooCommerce's product catalog to CSV file in Yotpo's layout.
Version: 1.0.0
Author: Yotpo Support, Lior Gingi
Author URI: https://github.com/LiorGingi
License: GPLv2 or later
Text Domain: yotpo-catalog-export-plguin
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

add_action('admin_menu', 'my_menu');

function my_menu() {
    add_menu_page('Yotpo Catalog', 'Yotpo Product Catalog Export', 'manage_options', 'woocommerce-yotpo-export-page', 'export_admin');
}

function export_admin(){
	include( plugin_dir_path( __FILE__ ) . 'templates/yotpo-export-settings.php');
	if (!defined('ABSPATH')){
		die;
	}

	if(isset($_POST['export'])){
		product_catalog_csv();
	}
	else{
		wp_enqueue_style( 'yotpoExportCSS', plugins_url('assets/css/admin_style.css', __FILE__));
		admin_ui();
	}
}

function product_statuses(){ //include products that their status is not published
	$statuses = array();
	if(isset($_POST['private']) && $_POST['private'] == 'Yes'){
		array_push($statuses, "private");
	}
	if(isset($_POST['draft']) && $_POST['draft'] == 'Yes'){
		array_push($statuses, "draft");
	}
	if(isset($_POST['pending']) && $_POST['pending'] == 'Yes'){
		array_push($statuses, "pending");
	}
	return $statuses;
}

function gtin($product_id, $type, $mode){ //mode = plugin, type = upc/isbn
	$result = null;
	if($mode == 'wc-uei'){
		$result = get_post_meta($product_id, 'hwp_product_gtin', 1);
	}
	else if($mode == 'pgtin-wc'){
		$result = get_post_meta($product_id, '_wpm_gtin_code', 1);
	}
	else if($mode == 'wc-pfpro'){
		$result = get_post_meta($product_id, '_woosea_upc', 1);
	}
	else if($mode == 'wc-upf'){
		$result = get_post_meta($product_id, '_gtin', 1);
	}
	else if (is_null($mode) || empty($mode)){
		$result = get_post_meta($product_id, $_POST['cust_gtin'], 1);
	}

	if(!is_null($result) && !empty($result) && is_string($result)){
		return $result;
	}
	else{
		return '';
	}
}

function mpn($product_id, $mode){
	$result = null;
	if($mode == 'wc-pfpro'){
		$result = get_post_meta($product_id, '_woosea_mpn', 1);
	}
	else if($mode == 'wc-upf'){
		$result = get_post_meta($product_id, '_mpn', 1);
	}
	else if (is_null($mode) || empty($mode)){
		$result = get_post_meta($product_id, $_POST['cust_mpn'], 1);
	}

	if(!is_null($result) && !empty($result) && is_string($result)){
		return $result;
	}
	else{
		return '';
	}
}

function brand($product_id, $mode){
	$result = null;
	if($mode == 'wc-pfpro'){
		$result = get_post_meta($product_id, '_woosea_brand', 1);
	}
	else if($mode == 'wc-upf'){
		$result = get_post_meta($product_id, '_brand', 1);
	}
	else if (is_null($mode) || empty($mode)){
		$result = get_post_meta($product_id, $_POST['cust_brand'], 1);
	}

	if(!is_null($result) && !empty($result) && is_string($result)){
		return $result;
	}
	else{
		return '';
	}
}

function product_catalog_csv(){
	$args = array(
		'post_type' => 'product',
		'posts_per_page' => -1
		);
	$loop = new WP_Query( $args ); //get the store's catalog
	$saveData = array();
	$statuses = product_statuses();
	ob_end_clean();
	header('Content-type: application/utf-8');
	header('Content-disposition: attachment; filename="Yotpo Product Catalog.csv"');
	$fp = fopen('php://output', 'w'); 
	fputcsv($fp, ['Product ID', 'Product Name', 'Product Description', 'Product URL', 'Product Image URL',
            'Product Price', 'Currency', 'Spec UPC', 'Spec SKU', 'Spec ISBN', 'Spec Brand', 'Spec MPN', 'Blacklisted', 'Product Group']); //print headers to the file
	while ( $loop->have_posts() ){
		$loop->the_post();
		if (($loop->post->post_status == 'publish') || (in_array($loop->post->post_status, $statuses))){
			$_product = wc_get_product($loop->post->ID);
			$product_id = $_product->get_id(); 
			$saveData['Product ID'] = $product_id;
			$saveData['Product Name'] = $_product->get_title();
			$saveData['Product Description'] = '';
			$saveData['Product URL'] = get_permalink($product_id);
			$saveData['Product Image URL'] = wc_yotpo_get_product_image_url($product_id);
			$saveData['Product Price'] = $_product->get_price();
			$saveData['Currency'] = get_woocommerce_currency();
			$saveData['Spec SKU'] = $_product->get_sku();
			$saveData['Spec UPC'] = gtin($product_id, 'upc', $_POST['plugin']);
			$saveData['Spec Brand'] = brand($product_id, $_POST['plugin']);
			$saveData['Spec MPN'] = mpn($product_id, $_POST['plugin']);
			$saveData['Spec ISBN'] = gtin($product_id, 'isbn', $_POST['plugin']);
			$saveData['Blacklisted'] = 'false';
			$saveData['Product Group'] = '';

			fputcsv($fp, $saveData);
		}
	}
	fclose($fp);
	exit();
}
