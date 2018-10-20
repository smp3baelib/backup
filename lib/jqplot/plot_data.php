<?php

// key to authentication

if (!defined('SB')) {
    // main system configuration
    require '../../../sysconfig.inc.php';
    // start the session
    require SB.'admin/default/session.inc.php';
}

// IP based access limitation
require LIB.'ip_based_access.inc.php';
do_checkIP('smc');
do_checkIP('smc-reporting');


// privileges checking
$can_read = utility::havePrivilege('reporting', 'r');
$can_write = utility::havePrivilege('reporting', 'w');

if (!$can_read) {
    die('<div class="errorBox">'.__('You don\'t have enough privileges to access this area!').'</div>');
}

$x_axis ='';
$title  ='';
$series ='';
$_sr    ='';
$plot1  ='';
(!isset($_GET['year'])?$year = '%':$year= $_GET['year']);

switch ($_GET['chart']) {
    //=========================================================================================
    case 'visitor_statistic':
    //title
        $title .= '\''.__('Visitor Statistic').' '.__('Year').' '.$year.'\'';       
    //x axis        
        $months['01'] = __('Jan');
        $months['02'] = __('Feb');
        $months['03'] = __('Mar');
        $months['04'] = __('Apr');
        $months['05'] = __('May');
        $months['06'] = __('Jun');
        $months['07'] = __('Jul');
        $months['08'] = __('Aug');
        $months['09'] = __('Sep');
        $months['10'] = __('Oct');
        $months['11'] = __('Nov');
        $months['12'] = __('Dec');
        foreach ($months as $month_num => $month) {
        $x_axis .= '\''.$month.'\',';    } 
    //series value
        $_q = $dbs->query("SELECT member_type_id, member_type_name FROM mst_member_type LIMIT 100");
        while ($_d = $_q->fetch_row()) {            
        $series .=  '{label:"'.$_d[1].'"},';
        $_sr .= preg_replace("@[\/ \s]@i","_",$_d[1]).',';    }
        $series .= '{label:"'.__('NON-Member Visitor').'"}';
        $_sr .= 'non_member';  
        $_q = $dbs->query("SELECT member_type_id, member_type_name FROM mst_member_type LIMIT 100");
        while ($_d = $_q->fetch_row()) {            
        $plot1 .=  'var '.preg_replace("@[\/ \s]@i","_",$_d[1]).' = [';
                foreach ($months as $month_num => $month) {
                $sql_str = "SELECT COUNT(visitor_id) FROM visitor_count AS vc
                INNER JOIN (member AS m LEFT JOIN mst_member_type AS mt ON m.member_type_id=mt.member_type_id) ON m.member_id=vc.member_id
                WHERE m.member_type_id=$_d[0] AND vc.checkin_date LIKE '$year-$month_num-%'";
                $visitor_q = $dbs->query($sql_str);
                $visitor_d = $visitor_q->fetch_row();
        $plot1 .=  $visitor_d[0].','; }  
        $plot1 .= '];';  }
        $plot1 .= 'var non_member = [';
                foreach ($months as $month_num => $month) {
                $sql_str = "SELECT COUNT(visitor_id) FROM visitor_count AS vc
                WHERE (vc.member_id IS NULL OR vc.member_id='') AND vc.checkin_date LIKE '$year-$month_num-%'";
                $visitor_q = $dbs->query($sql_str);
                $visitor_d = $visitor_q->fetch_row();
        $plot1 .=  $visitor_d[0].','; }
        $plot1 .= '];';  
    break;

    //=========================================================================================
    case 'loan_by_class':
    //title
        $title .= '\''.__('Loans by Classification').' '.__('Year').' '.$year.'<br/>'.'Type Koleksi : '.__($_GET['coll_type']);
        $title .=' - Klasifikasi : '.$_GET['class'].'\'';  
    //x axis  
        $months['01'] = __('Jan');
        $months['02'] = __('Feb');
        $months['03'] = __('Mar');
        $months['04'] = __('Apr');
        $months['05'] = __('May');
        $months['06'] = __('Jun');
        $months['07'] = __('Jul');
        $months['08'] = __('Aug');
        $months['09'] = __('Sep');
        $months['10'] = __('Oct');
        $months['11'] = __('Nov');
        $months['12'] = __('Dec');
        foreach ($months as $month_num => $month) {
        $x_axis .= '\''.$month.'\',';    }   
    //series value      
        $class_num = $_GET['class'];
        $selected_year = $_GET['year'];
        $coll_type = $_GET['coll_type'];
        $class_num2 = 0;
        if ($class_num == 'NONDECIMAL') {
            $series .= '{label:"'.__('NON DECIMAL Classification').'"},';
            $_sr    .= 'non_decimal';  
            $plot1  .=  'var non_decimal = [';
            foreach ($months as $month_num => $month) {
            $loan_q = $dbs->query("SELECT COUNT(*) FROM biblio AS b
                LEFT JOIN item AS i ON b.biblio_id=i.biblio_id
                LEFT JOIN loan AS l ON l.item_code=i.item_code
                WHERE classification REGEXP '^[^0-9]' AND l.loan_date LIKE '$selected_year-$month_num-%'".( $coll_type='all'?'':"AND i.coll_type_id=$coll_type" ));
            $loan_d = $loan_q->fetch_row();   
            $plot1 .=  $loan_d[0].','; }
            $plot1 .= '];';                 
        }
        else{
            while ($class_num2 < 10) {
            $series .= '{label:"'.$class_num.$class_num2.'0"},';
            $_sr    .= 'c'.$class_num.$class_num2.'0,';
            $plot1 .=  'var c'.$class_num.$class_num2.'0 = [';
            foreach ($months as $month_num => $month) {
                    $loan_q = $dbs->query("SELECT COUNT(*) FROM biblio AS b
                                LEFT JOIN item AS i ON b.biblio_id=i.biblio_id
                                LEFT JOIN loan AS l ON l.item_code=i.item_code
                                WHERE TRIM(classification) LIKE '$class_num"."$class_num2%' AND l.loan_date LIKE '$selected_year-$month_num-%'".( $coll_type='all'?'':"AND i.coll_type_id=$coll_type" ));
                    $loan_d = $loan_q->fetch_row();
            $plot1 .=  $loan_d[0].','; }
            $plot1 .= '];';
            $class_num2++;
            } 
        }
    break;

//=========================================================================================
    case 'reccap':
    //title
        $title .= '\''.__('Title and Collection Recap by').' '.__($_GET['reccap_by']).'\''; 
        $_title ='';
        $_items ='';
        $series .= '{label:"'.__('Title').'"},{label:"'.__('Items').'"}';
        $_sr    .= 'title,items';
        switch ($_GET['reccap_by']){

            case __('Collection Type'):
                $ctype_q = $dbs->query("SELECT DISTINCT coll_type_id, coll_type_name FROM mst_coll_type");
                while ($ctype_d = $ctype_q->fetch_row()) {
                $x_axis .= '\''.__($ctype_d[1]).'\',';
                    // count by title
                    $bytitle_q = $dbs->query("SELECT DISTINCT biblio_id FROM item AS i
                        WHERE i.coll_type_id=".$ctype_d[0]."");
                    $bytitle_d[0] = $bytitle_q->num_rows;
                    $_title .= $bytitle_q->num_rows.',';              
                    // count by item
                    $byitem_q = $dbs->query("SELECT COUNT(item_id) FROM item AS i
                        WHERE i.coll_type_id=".$ctype_d[0]);
                    $byitem_d = $byitem_q->fetch_row();
                    $_items .= $byitem_d[0].',';
                $ctype_q++;
                }
            break;

            case __('Language'):
                $lang_q = $dbs->query("SELECT DISTINCT language_id, language_name FROM mst_language");
                while ($lang_d = $lang_q->fetch_row()) {
                $x_axis .= '\''.$lang_d[1].'\',';
                    // count by title
                    $bytitle_q = $dbs->query("SELECT COUNT(biblio_id) FROM biblio WHERE language_id='".$lang_d[0]."'");
                    $bytitle_d = $bytitle_q->fetch_row();
                    $_title .= $bytitle_d[0].',';
                    // count by item
                    $byitem_q = $dbs->query("SELECT COUNT(item_id) FROM item AS i INNER JOIN biblio AS b
                        ON i.biblio_id=b.biblio_id
                        WHERE b.language_id='".$lang_d[0]."'");
                    $byitem_d = $byitem_q->fetch_row();
                    $_items .= $byitem_d[0].',';
                $lang_q++;
                }
            break;

            case __('GMD'):
                $width = 1300;
                $height = 400;
                $gmd_q = $dbs->query("SELECT DISTINCT gmd_id, gmd_name FROM mst_gmd");
                while ($gmd_d = $gmd_q->fetch_row()) {
                $x_axis .= '\''.$gmd_d[1].'\',';
                    // count by title
                    $bytitle_q = $dbs->query("SELECT COUNT(biblio_id) FROM biblio WHERE gmd_id=".$gmd_d[0]);
                    $bytitle_d = $bytitle_q->fetch_row();
                    $_title   .= $bytitle_d[0].',';
                    // count by item
                    $byitem_q = $dbs->query("SELECT COUNT(item_id) FROM item AS i INNER JOIN biblio AS b
                        ON i.biblio_id=b.biblio_id
                        WHERE b.gmd_id=".$gmd_d[0]);
                    $byitem_d  = $byitem_q->fetch_row();
                    $_items   .= $byitem_d[0].',';
                $gmd_q++;
                }             
            break;

            case __('Classification'):
            //decimal class
                $class_num = 0;
                while ($class_num < 10) {
                    $x_axis .= '\''.$class_num.'00\',';
                    // count by title
                    $bytitle_q = $dbs->query("SELECT COUNT(biblio_id) FROM biblio WHERE TRIM(classification) LIKE '$class_num%'");
                    $bytitle_d = $bytitle_q->fetch_row();
                    $_title   .= $bytitle_d[0].',';
                    // count by item
                    $byitem_q = $dbs->query("SELECT COUNT(item_id) FROM item AS i LEFT JOIN biblio AS b
                        ON i.biblio_id=b.biblio_id
                        WHERE TRIM(b.classification) LIKE '$class_num%'");
                    $byitem_d = $byitem_q->fetch_row();
                    $_items   .= $byitem_d[0].',';
                    $class_num++;
                }
            //2x class
                $x_axis .= '\'2X0\'';
                    // count by title
                    $bytitle_q = $dbs->query("SELECT COUNT(biblio_id) FROM biblio WHERE TRIM(classification) LIKE '2X%'");
                    $bytitle_d = $bytitle_q->fetch_row();
                    $_title   .= $bytitle_d[0].',';
                    // count by item
                    $byitem_q = $dbs->query("SELECT COUNT(item_id) FROM item AS i INNER JOIN biblio AS b
                        ON i.biblio_id=b.biblio_id
                        WHERE TRIM(b.classification) LIKE '2X%'");
                        $byitem_d = $byitem_q->fetch_row();
                        $_items   .= $byitem_d[0].',';

            //non decimal class
                $_non_decimal_q = $dbs->query("SELECT DISTINCT classification FROM biblio WHERE classification REGEXP '^[^0-9]'");
                if ($_non_decimal_q->num_rows > 0) {
                $x_axis .='\'NON DECIMAL\'';    
                    while ($_non_decimal = $_non_decimal_q->fetch_row()) {
                        // count by title
                        $bytitle_q = $dbs->query("SELECT COUNT(biblio_id) FROM biblio WHERE classification LIKE '".$_non_decimal[0]."'");
                        $bytitle_d = $bytitle_q->fetch_row();
                        $_title   .= $bytitle_d[0].',';
                        // count by item
                        $byitem_q = $dbs->query("SELECT COUNT(item_id) FROM item AS i INNER JOIN biblio AS b
                            ON i.biblio_id=b.biblio_id
                            WHERE classification LIKE '".$_non_decimal[0]."'");
                        $byitem_d = $byitem_q->fetch_row();
                        $_items   .= $byitem_d[0].',';
                        $_non_decimal_q++;
                    }
                }
            break;
        }    
        $plot1  =  'var title = ['.$_title.'];var items = ['.$_items.'];';

    break;
//===========================================================================

}
//echo $series.'<br/>'.$_sr.'<br/>';
//echo $_items;
?>
