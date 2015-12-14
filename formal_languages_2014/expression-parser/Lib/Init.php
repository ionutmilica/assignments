<?php

/**
 * O prescurtare pentru vardump. Formateaza raspunsul primit de la acesta.
 * 
 * @return void
 */
function dd()
{
	echo '<pre>';
	call_user_func_array('var_dump', func_get_args());
	echo '</pre>';
}