<?php

/* ----- create cookie to remember user name and email for gravity form collecting resources ----- */ 
add_action("gform_pre_submission_2", "cookify_gf_form");
 
 
function cookify_gf_form($form_meta) {
	$saveVars = array("name", "email");
	
    foreach($form_meta["fields"] as $field) {
		if (in_array($field["inputName"], $saveVars)) {
			setcookie("gf_".$field["inputName"], $_POST["input_" . $field["id"]], time() + 31536000, COOKIEPATH, COOKIE_DOMAIN, false, true);
		}
	}
}

add_filter("gform_field_value_name", "populate_name");

function populate_name() {
	if (isset($_COOKIE["gf_name"]))  return $_COOKIE["gf_name"];
}

add_filter("gform_field_value_email", "populate_email");

function populate_email() {
	if (isset($_COOKIE["gf_email"])) return $_COOKIE["gf_email"];
}
?>