<?php
add_shortcode( 'form', 'form_func' );
function form_func($atts, $content = null)
{
	extract( shortcode_atts( array(
        'action'  => '',
        'width'  => '100%',
        'table'  => 'wp_dispatch_daily_v',
        'class'  => 'form-charts',
    ), $atts ) );
	$output = '';
	$output .= '<div class="row col-md-10 drops_from">';
	$output .= '<form action="" method="get" class="col-md-12 row">';

	$output .= do_shortcode( $content );
	$output .= '<div class="col-md-4 col-xs-6">';
	$output .= '<input type="hidden" name="table_name" value="'.$atts["table"].'">';
	$output .= '<div class="form-group form-button">
		<input type="submit" name="submited" class="btn btn-success" value="Submit"></div></div>
	</form>';
	if(isset($_GET['submited']))
	{
		//$output .= '<h2 class="entry-title">'.$_GET['hub'].' '.$_GET['deriv'].' '.$_GET['Hour Type'].' '.$_GET['metric'].' '.$_GET['period'].'</h2>';
	}
	$output .= '</div>';
	return $output;
}

add_shortcode( 'page', 'page_func' );
function page_func($atts)
{
	global $post;
	extract( shortcode_atts( array(
        'page_id'  => $post->ID,
    ), $atts ) );
    return '<input name="page_id" type="hidden" value="'.$atts["page_id"].'" />';
}
add_shortcode( 'title', 'title_func' );
function title_func($atts)
{
	extract( shortcode_atts( array(
        'show'  => '',
    ), $atts ) );
    if(isset($_GET['submited'])){
	    $title = explode( ',', $atts['show']);
	    $output = '';
	    $output.= '<h2 class="entry-title">';
	    for ($i=0; $i < count($title); $i++) { 
	    	$output.= $_GET[$title[$i]].' ';
	    }
	    $output .= '</h2>';
	    return $output;
    }
}
add_shortcode( 'variable', 'variable_func' );
function variable_func($atts)
{
	extract( shortcode_atts( array(
        'name'  => 'name',
        'label'  => 'Label',
        'table'  => 'Trade_rank_all',
    ), $atts ) );

    $output = '';
    $output .= '<div class="col-md-4 col-xs-6">';
    $output .= '<div class="form-group">';
    $output .= '<label>'.$atts['label'].'</label>';
    $output .= '<select name="'.$atts["name"].'" class="form-control">';
    $conn = open_conn();
    $info = test_conn();
    $query = "SELECT `".$atts['name']."` FROM ".$info->name.".".$atts['table']." group by `".$atts["name"]."` ORDER BY `".$atts["name"]."` ASC";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
    	while($row = $result->fetch_assoc()) {
    		$output .= '<option '.( $_GET[$atts['name']] == $row[$atts["name"]] ? 'selected="selected"' : '').' >'.$row[$atts["name"]].'</option>';
    	}
    	
    }
    $output .= '</select>';
    $output .= '</div>';
    $output .= '</div>';
    
    return $output;
}

function open_conn()
{
	$info = test_conn();
	$servername = $info->host;
	$username = $info->user;
	$password = $info->pass;
	return $conn = new mysqli($servername, $username, $password);
}

function test_conn()
{
	global $wpdb;
	$dao_database = new dao_database($wpdb, 'wp_wpbi_databases');
	$tgt_database = new vo_database(5, NULL, NULL, NULL, NULL);
	$vo_database = $dao_database->select($tgt_database);
	$vo_database = $vo_database[0];
	return $vo_database;
}


add_filter( 'the_content', 'filter_datatable' );
function filter_datatable($content)
{
	if(isset($_GET['submited']))
	{
		add_filter( 'wpdatatables_filter_mysql_query', 'test_func' );
	}

	return $content;
}

function test_func($query)
{
	/*echo '<pre>'.print_r($_GET,true).'</pre>';*/
	$get_array = $_GET;
	array_shift($get_array);
	array_pop($get_array);
	/*echo '<pre>'.print_r($get_array,true).'</pre>';*/
	$mysql_query ='';
	$mysql_query .= "select * from WECC_Reports.".$get_array["table_name"]." WHERE";
	array_pop($get_array);
	foreach ($get_array as $key => $value) {
		$mysql_query .= " $key = '$value' and";
	}
	/*echo '<pre>'.print_r($mysql_query,true).'</pre>';*/
	$last_four = substr($mysql_query, -3);
	if($last_four === "and")
	{
		$mysql_query = substr($mysql_query, 0, -3);
	}
	/*echo '<pre>'.print_r($mysql_query,true).'</pre>';
	echo '<pre>'.print_r($query,true).'</pre>';*/
	return $mysql_query;
}
