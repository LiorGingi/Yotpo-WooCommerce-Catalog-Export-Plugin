<?php

function admin_ui(){
	$ui = "<div class='wrapper'>
	<h1>Welcome To Yotpo Product Catalog Export Plugin</h1>
	</br>
	<form  method='post'>

		<div class='step'>
			<div class='step-container'>
				<h3>Step 1: <i>(Optional)</i></h3>
				<input type='checkbox' name='private' value='Yes'>Include products that their <i>visibility</i> is set to <b>Private</b>
				</br></br>
				<input type='checkbox' name='draft' value='Yes'>Include products that their <i>status</i> is set to <b>Draft</b>
				</br></br>
				<input type='checkbox' name='pending' value='Yes'>Include products that their <i>status</i> is set to <b>Pending Review</b>
			</div>
		</div>
		</br></br>
		<div class='step'> 
			<div class='step-container'>
				<h3>Step 2: Unique Identifiers <i>(Optional)</i></h3>
				<p class='instructions'>If you're using one of the following plugins, please choose the plugin in use: </p>
				<input type='radio' name='plugin' value='wc-uei'>WooCommerce UPC, EAN, and ISBN
				</br></br>
				<input type='radio' name='plugin' value='pgtin-wc'>Product GTIN (EAN, UPC, ISBN) for WooCommerce
				</br></br>
				<input type='radio' name='plugin' value='wc-pfpro'>WooCommerce Product Feed PRO - <i>Please note that the <b>UPC</b> field is pulled for UPC or ISBN</i>
				</br></br>
				<input type='radio' name='plugin' value='wc-upf'>Ultimate Products Feed
				</br></br>
				<hr>
				<p style='font-size:20px; margin: auto 50px;'><b>OR</b></p>
				<hr>
				<p class='instructions'>If you're using custom meta-fields, please fill the relevant fields below:</p>
				<div class='customization'>GTIN (UPC/ISBN)</div> <input type='text' name='cust_gtin'><br><br>
				<div class='customization'>MPN</div> <input type='text' name='cust_mpn'><br><br>
				<div class='customization'>Brand</div> <input type='text' name='cust_brand'><br><br>
				<p><i><b>*Please note that Yotpo supports only UPC-A, ISBN and MPN+Brand.</b></i></p>
			</div>
		</div>
		</br></br>
		<input type='submit' name='export' value='Export Product Catalog'/>
	</form>
	</div>";
	echo $ui;
}

?>
