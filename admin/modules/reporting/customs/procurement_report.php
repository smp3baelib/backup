<?php
/**
 *
 * Copyright (C) 2007,2008  Arie Nugraha (dicarve@yahoo.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 */

/* Laporan Perkembangan Koleksi */
/* 2015, Heru Subekti (heroe.soebekti@gmail.com) */

// key to authenticate
define('INDEX_AUTH', '1');

// main system configuration
require '../../../../sysconfig.inc.php';
// IP based access limitation
require LIB.'ip_based_access.inc.php';
do_checkIP('smc');
do_checkIP('smc-reporting');
// start the session
require SB.'admin/default/session.inc.php';
require SB.'admin/default/session_check.inc.php';
// privileges checking
$can_read = utility::havePrivilege('reporting', 'r');
$can_write = utility::havePrivilege('reporting', 'w');

if (!$can_read) {
    die('<div class="errorBox">'.__('You don\'t have enough privileges to access this area!').'</div>');
}

require SIMBIO.'simbio_GUI/form_maker/simbio_form_element.inc.php';

$page_title = 'Laporan Perkembangan Koleksi';
$reportView = false;
if (isset($_GET['reportView'])) {
    $reportView = true;
}

if (!$reportView) {
?>
    <!-- HEADER -->
    <fieldset>
    <div class="per_title">
        <h2><?php echo __('Perkembangan Koleksi'); ?></h2>
    </div>
    <div class="infoBox">
    <h4>Menampilkan perkembangan koleksi tiap tahun tiap kelas dengan jumlah judul dan eksemplar</h4>
    <p>Dibutuhkan waktu untuk memroses data base koleksi , harap menunggu sebentar. Anda dapat membaca tutorial ini di <a href="http://slimskudus.blogspot.com" target="blank">slimskudus.blogspot.com</a></p>
    <h5>Plugin ini buat oleh Heru Subekti (<a href="mailto:heroe.soebekti@gmail.com" target="blank">heroe.soebekti@gmail.com</a>)</h5>
    </div>
    </fieldset>
    <!-- HEADER end -->
    <iframe name="reportView" id="reportView" src="<?php echo $_SERVER['PHP_SELF'].'?reportView=true'; ?>" frameborder="0" style="width: 100%; height: 500px;"></iframe>
<?php
} else {
    ob_start();
    // months array
    $class['0'] = __('0');
    $class['1'] = __('1');
    $class['2'] = __('2');
    $class['3'] = __('3');
    $class['4'] = __('4');
    $class['5'] = __('5');
    $class['6'] = __('6');
    $class['7'] = __('7');
    $class['8'] = __('8');
    $class['9'] = __('9');

    // table start
    $row_class = 'alterCellPrinted';
    $output = '<table align="center" class="border" style="width: 100%;" cellpadding="3" cellspacing="0">';

    // header
    $output .= '<tr>';
    $output .= '<td class="dataListHeaderPrinted" style="font-size: 1.5em; font-weight:bolder;">'.__('Year / Classification').'</td>';
    foreach ($class as $class_num => $cls) {
        $total_title[$class_num] = 0;
        $total_items[$class_num] = 0;
        $total_title['2X'] = 0;
        $total_items['2X'] = 0;
        if($cls == '2X'){
        $output .= '<td class="dataListHeaderPrinted">'.$cls.'0</td>';
        }else{
        $output .= '<td class="dataListHeaderPrinted">'.$cls.'00</td>';
        }
    }
    $output .= '<td class="dataListHeaderPrinted">2X (islam)</td>';
    $output .= '</tr>';

    // year
    $selected_year = date('Y');
    if (isset($_GET['year']) AND !empty($_GET['year'])) {
        $selected_year = (integer)$_GET['year'];
    }


    $_q = $dbs->query("SELECT DISTINCT YEAR(input_date) AS input_date FROM biblio UNION SELECT DISTINCT YEAR(input_date) AS input_date FROM item  ORDER BY input_date DESC  LIMIT 100");
    while ($_d = $_q->fetch_row()) {
        $year_options[$_d[0]] = $_d[0];
    }
    $r = 1;

    foreach ($year_options as $id => $input_date) {
        $row_class = ($r%2 == 0)?'alterCellPrinted':'alterCellPrinted2';
        $output .= '<tr>';
        $output .= '<td class="'.$row_class.'">'.$input_date.'</td>'."\n";
        /* DECIMAL CLASSES */
        foreach ($class as $class_num => $cls) {
            $title_q = $dbs->query("SELECT COUNT(biblio_id) FROM biblio WHERE TRIM(classification) LIKE '$cls%' AND TRIM(classification) NOT LIKE '2X%' AND input_date LIKE '$input_date-%'");
            $title_d = $title_q->fetch_row();
            //if ($title_d[0] > 0) {
                $byitem_q = $dbs->query("SELECT COUNT(i.item_id) FROM item AS i LEFT JOIN biblio AS b ON i.biblio_id=b.biblio_id
                WHERE TRIM(b.classification) LIKE '$cls%'  AND TRIM(classification) NOT LIKE '2X%' AND i.input_date LIKE '$input_date-%'");
                $byitem_d = $byitem_q->fetch_row();              
                $output .= '<td class="'.$row_class.'">'.__('Title').': <strong>'.$title_d[0].'</strong><br/>'.__('Items').': <i><span style="color: blue;">'.$byitem_d[0].'</span></i></td>';
            $total_items[$class_num] += $byitem_d[0];
            //} else {
            //    $output .= '<td class="'.$row_class.'"><span style="color: #ff0000;">'.$title_d[0].'</span></td>';
            //}
            $total_title[$class_num] += $title_d[0];
            
        }
        /* DECIMAL CLASSES END */
        /* 2X NUMBER CLASSES */
            $sql_str = "SELECT COUNT(biblio_id) FROM biblio WHERE TRIM(classification) LIKE '2X%' AND input_date LIKE '$input_date-%'";
            $title_q = $dbs->query($sql_str);
            $title_d = $title_q->fetch_row();
            //if ($title_d[0] > 0) {
                $byitem_q = $dbs->query("SELECT COUNT(i.item_id) FROM item AS i LEFT JOIN biblio AS b ON i.biblio_id=b.biblio_id
                WHERE TRIM(b.classification) LIKE '2X%' AND i.input_date LIKE '$input_date-%'");
                $byitem_d = $byitem_q->fetch_row();              
                $output .= '<td class="'.$row_class.'">'.__('Title').': <strong>'.$title_d[0].'</strong><br/>'.__('Items').': <i><span style="color: blue;">'.$byitem_d[0].'</span></i></td>';
            $total_items['2X'] += $byitem_d[0];
            //} else {
            //    $output .= '<td class="'.$row_class.'"><span style="color: #ff0000;">'.$title_d[0].'</span></td>';
            //}
            $total_title['2X'] += $title_d[0];
            
        /* 2X NUMBER CLASSES END */
        $output .= '</tr>';
        $r++;
    }


    // TOTTAL FOR AECH CLASSES
    $output .= '<tr>';
    $output .= '<td class="dataListHeaderPrinted">'.__('Total Title/exemplar').'</td>';
    foreach ($class as $class_num => $cls) {
        $output .= '<td class="dataListHeaderPrinted">'.$total_title[$class_num].'/'.$total_items[$class_num].'</td>';
    }
    $output .= '<td class="dataListHeaderPrinted">'.$total_title['2X'].'/'.$total_items['2X'].'</td>';
    $output .= '</tr>';

    $output .= '</table>';

    // print out
    echo '<div class="printPageInfo">Laporan Perkembangan Koleksi <a class="printReport" onclick="window.print()" href="#">'.__('Print Current Page').'</a></div>'."\n";
    echo $output;

    $content = ob_get_clean();
    // include the page template
    require SB.'/admin/'.$sysconf['admin_template']['dir'].'/printed_page_tpl.php';
}
