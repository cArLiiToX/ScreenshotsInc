<?php 
	//echo apache_get_version(); // Apache/2.4.18 (Ubuntu)
	//php 7.0.8-0ubuntu0.16.04.3
	//echo php_ini_loaded_file();
/*
	Magneto 2 requirement : 
	Magento requires Apache 2.2.x or 2.4.x.
	PHP 5.5, 5.6, or 7.0—Ubuntu

*/

//var_dump(get_ini('always_populate_raw_post_data'));


var_dump(extension_loaded('php_openssl'));