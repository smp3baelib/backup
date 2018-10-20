<?php
/**
 * SENAYAN application printable data configuration
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

/**
 * Function to load and override print settings from database
 */
function loadPrintSettings($dbs, $type) {
  global $sysconf;
  $barcode_settings_q = $dbs->query("SELECT setting_value FROM setting WHERE setting_name='".$type."_print_settings'");
  if ($barcode_settings_q->num_rows) {
    $barcode_settings_d = $barcode_settings_q->fetch_row();
    if ($barcode_settings_d[0]) {
      $barcode_settings = @unserialize($barcode_settings_d[0]);
      if (is_array($barcode_settings) && count($barcode_settings) > 0) {
        foreach ($barcode_settings as $setting_name => $val) {
          $sysconf['print'][$type][$setting_name] = $val;
        }
      }
      return $sysconf['print'][$type];
    }
  }
}

// label print settings
/* measurement in cm */
$sysconf['print']['label']['page_margin'] = 0.2;
$sysconf['print']['label']['items_per_row'] = 3;
$sysconf['print']['label']['items_margin'] = 0.05;
$sysconf['print']['label']['box_width'] = 8;
$sysconf['print']['label']['box_height'] = 3.3;
$sysconf['print']['label']['include_header_text'] = 1; // change to 0 if dont want to use header in each label
$sysconf['print']['label']['header_text'] = ''; // keep empty if you want to use Library Name as a header text
$sysconf['print']['label']['fonts'] = "Arial, Verdana, Helvetica, 'Trebuchet MS'";
$sysconf['print']['label']['font_size'] = 11;
$sysconf['print']['label']['border_size'] = 1; // in pixels

// label barcode karya kak Heru Subekti terbaru print settings
$sysconf['print']['barcode']['barcode_coll_size'] = 1; // in cm
$sysconf['print']['barcode']['barcode_position'] = 'l'; // left or right
$sysconf['print']['barcode']['barcode_rotate'] = 'cc'; // cc or cw

// Book Card print settings
/* This source modified by Muh Tarom (http://irigomi.com) on Friday, 21 December 2012*/
/* Disesuaikan Pada tanggal 28 September , untuk SLiMS 7 Cendana oleh Zaemakhrus /
/* measurement in cm */
$bookcard_page_margin = 0.2;
$bookcard_items_per_row = 2;
$bookcard_width = 9;
$bookcard_height = 10; // tinggi minimal kartu buku untuk kertas A4 / minimals height recommended for A4 paper
$bookcard_include_header_text = 1; // change to 0 if dont want to use header in each book card
$bookcard_address_text = ''; // tuliskan alamat perpustakaan di sini / type your library address here
$bookcard_header_text = 'Perpustakaan SMP 1 Jekulo'; // keep empty if you want to use Library Name as a header text
$bookcard_cut_title = 36; // maximum characters in title to appear in each book card. change to 0 if you dont want the title cutted
$bookcard_cut_authors = 36; // maximum characters in authors to appear in each book card. change to 0 if you dont want the authors cutted
$bookcard_fonts = "Arial, Verdana, Helvetica, 'Trebuchet MS'"; // font to use
$bookcard_number_row = 11; // numbers blank row of book card / jumlah baris kosong pada kartu buku

// Book Slip print settings
/* This source modified by Muh Tarom (http://irigomi.com) on Tuesday, 25 December 2012*/
/* measurement in cm */
$bookslip_page_margin = 0.2;
$bookslip_items_per_row = 2;
$bookslip_width = 9;
$bookslip_height = 20; // tinggi minimal kartu buku untuk kertas A4 / minimals height recommended for A4 paper
$bookslip_include_header_text = 1; // change to 0 if dont want to use header in each book card
$bookslip_address_text = ''; // tuliskan alamat perpustakaan di sini / type your library address here
$bookslip_header_text = 'Perpustakaan SMP 1 Jekulo'; // keep empty if you want to use Library Name as a header text
$bookslip_cut_title = 36; // maximum characters in title to appear in each book card. change to 0 if you dont want the title cutted
$bookslip_cut_authors = 36; // maximum characters in authors to appear in each book card. change to 0 if you dont want the authors cutted
$bookslip_fonts = "Arial, Verdana, Helvetica, 'Trebuchet MS'"; // font to use
$bookslip_number_row = 50; // numbers blank row of return slip / jumlah baris kosong pada lembar pengembalian

// Pocket Book By Drajat Hasan
$sysconf['print']['pocket']['items_per_row'] = 2;
$sysconf['print']['pocket']['libraryname'] = "Perpustakaan SMP 1 Jekulo";
$sysconf['print']['pocket']['schoolname'] = "Jl. Kudus Pati , Kec. Jekulo Kab. Kudus";

// item barcode print settings
/* measurement in cm */
$sysconf['print']['barcode']['barcode_page_margin'] = 0.2;
$sysconf['print']['barcode']['barcode_items_per_row'] = 3;
$sysconf['print']['barcode']['barcode_items_margin'] = 0.1;
$sysconf['print']['barcode']['barcode_box_width'] = 7;
$sysconf['print']['barcode']['barcode_box_height'] = 5;
$sysconf['print']['barcode']['barcode_include_header_text'] = 1; // change to 0 if dont want to use header in each barcode
$sysconf['print']['barcode']['barcode_cut_title'] = 50; // maximum characters in title to appear in each barcode. change to 0 if you dont want the title cutted
$sysconf['print']['barcode']['barcode_header_text'] = ''; // keep empty if you want to use Library Name as a header text
$sysconf['print']['barcode']['barcode_fonts'] = "Arial, Verdana, Helvetica, 'Trebuchet MS'"; // font to use
$sysconf['print']['barcode']['barcode_font_size'] = 11;
$sysconf['print']['barcode']['barcode_scale'] = 70; // barcode scale in percent relative to box width and height
$sysconf['print']['barcode']['barcode_border_size'] = 1; // in pixels

// barcode generator print settings
$sysconf['print']['barcodegen']['box_width'] = 6;
$sysconf['print']['barcodegen']['page_margin'] = 0.2;
$sysconf['print']['barcodegen']['items_margin'] = 0.05;
$sysconf['print']['barcodegen']['include_border'] = 0;
$sysconf['print']['barcodegen']['items_per_row'] = 3;

/* Receipt Printing */
$sysconf['print']['receipt']['receipt_width'] = '7cm';
$sysconf['print']['receipt']['receipt_font'] = 'Courier';
$sysconf['print']['receipt']['receipt_color'] = '#000';
$sysconf['print']['receipt']['receipt_margin'] = '5px';
$sysconf['print']['receipt']['receipt_padding'] = '10px';
$sysconf['print']['receipt']['receipt_border'] = '1px dashed #000';
$sysconf['print']['receipt']['receipt_fontSize'] = '7pt';
$sysconf['print']['receipt']['receipt_header_fontSize'] = '7pt';
$sysconf['print']['receipt']['receipt_titleLength'] = 100;

// member card print settings
/* measurement in cm */
$sysconf['print']['membercard']['page_margin'] = 0.2;
$sysconf['print']['membercard']['items_margin'] = 0.1;
$sysconf['print']['membercard']['items_per_row'] = 1; //

// by Jushadi Arman Saz
/* measurement in cm*/
$sysconf['print']['membercard']['factor'] = "37.795275591"; //cm to px

//Heru Subekti (heroe_soebekti@yahoo.co.id) Modified by Kusairi , Kartu Anggota New Design
//custom print label and barcode
/* measurement in cm*/
$sysconf['print']['membercard1']['barcode_header_text1']= 'KOMUNITAS SLiMS NUSANTARA'; // Nama lembaga induk
$sysconf['print']['membercard1']['barcode_header_text2'] = 'PERPUSTAKAAN';  // Unit lembaga
$sysconf['print']['membercard1']['barcode_header_text3'] = 'Jl. Merdeka Republik Indonesia 17 081945'; // Alamat
$sysconf['print']['membercard1']['barcode_call_number_style']='font-size: 12.3pt; font-weight:bold; text-align:left; padding-left:84px; font:Consolas'; //Style Call Number


// Items Settings
// change to 0 if dont want to use selected items
$sysconf['print']['membercard']['include_id_label'] = 1; // id
$sysconf['print']['membercard']['include_name_label'] = 1; // name
$sysconf['print']['membercard']['include_pin_label'] = 1; // identify
$sysconf['print']['membercard']['include_inst_label'] = 0; // institution
$sysconf['print']['membercard']['include_email_label'] = 0; // mail address
$sysconf['print']['membercard']['include_address_label'] = 1; // home or office address
$sysconf['print']['membercard']['include_barcode_label'] = 1; // barcode
$sysconf['print']['membercard']['include_expired_label'] = 1; // expired date

// Cardbox Settings
$sysconf['print']['membercard']['box_width'] = 8.6;
$sysconf['print']['membercard']['box_height'] = 5.4;
$sysconf['print']['membercard']['front_side_image'] = 'membercard_background.jpg';
$sysconf['print']['membercard']['back_side_image'] = 'membercard_background.jpg';

// Logo Setting
$sysconf['print']['membercard']['logo'] = "logo.png";
$sysconf['print']['membercard']['front_logo_width'] = "";
$sysconf['print']['membercard']['front_logo_height'] = "";
$sysconf['print']['membercard']['front_logo_left'] = "";
$sysconf['print']['membercard']['front_logo_top'] = "";
$sysconf['print']['membercard']['back_logo_width'] = "";
$sysconf['print']['membercard']['back_logo_height'] = "";
$sysconf['print']['membercard']['back_logo_left'] = "";
$sysconf['print']['membercard']['back_logo_top'] = "";

// Photo Settings
$sysconf['print']['membercard']['photo_left'] = "";
$sysconf['print']['membercard']['photo_top'] = "";
$sysconf['print']['membercard']['photo_width'] = 1.5;
$sysconf['print']['membercard']['photo_height'] = 1.8;

// Header Settings
$sysconf['print']['membercard']['front_header1_text'] = 'Library Member Card'; // use <br /> tag to make another line
$sysconf['print']['membercard']['front_header1_font_size'] = '12';
$sysconf['print']['membercard']['front_header2_text'] = 'My Library';
$sysconf['print']['membercard']['front_header2_font_size'] = '12';
$sysconf['print']['membercard']['back_header1_text'] = 'My Library';
$sysconf['print']['membercard']['back_header1_font_size'] = "12";
$sysconf['print']['membercard']['back_header2_text'] = 'My Library Full Address and Website';
$sysconf['print']['membercard']['back_header2_font_size'] = "5";
$sysconf['print']['membercard']['header_color'] = "#0066FF"; //e.g. :#0066FF, green, etc.

//biodata settings
$sysconf['print']['membercard']['bio_font_size'] = "11";
$sysconf['print']['membercard']['bio_font_weight'] = "bold";
$sysconf['print']['membercard']['bio_label_width'] = "100";

// Stamp Settings
$sysconf['print']['membercard']['city'] = "City Name";
$sysconf['print']['membercard']['title'] = "Library Manager";
$sysconf['print']['membercard']['officials'] = "Librarian Name";
$sysconf['print']['membercard']['officials_id'] = "Librarian ID";
$sysconf['print']['membercard']['stamp_file'] = "stamp.png"; // stamp image, use transparent image
$sysconf['print']['membercard']['signature_file'] = "signature.png"; // sign picture, use transparent image
$sysconf['print']['membercard']['stamp_left'] = "";
$sysconf['print']['membercard']['stamp_top'] = "";
$sysconf['print']['membercard']['stamp_width'] = "";
$sysconf['print']['membercard']['stamp_height'] = "";

//expired
$sysconf['print']['membercard']['exp_left'] = "";
$sysconf['print']['membercard']['exp_top'] = "";
$sysconf['print']['membercard']['exp_width'] = "";
$sysconf['print']['membercard']['exp_height'] = "";

// Barcode Setting
$sysconf['print']['membercard']['barcode_scale'] = 100; // barcode scale in percent relative to box width and height
$sysconf['print']['membercard']['barcode_left'] = "";
$sysconf['print']['membercard']['barcode_top'] = "";
$sysconf['print']['membercard']['barcode_width'] = "";
$sysconf['print']['membercard']['barcode_height'] = "";

// Rules
$sysconf['print']['membercard']['rules'] = "<ul>
<li>1. Kartu ini berfungsi sebagai Kartu Pelajar & Kartu Anggota Perpustakaan</li>
<li>2. Kartu anggota perpustakaan ini hanya bisa digunakan oleh pemilik kartu.</li>
<li>3. Kartu ini harap dibawa saat ke perpustakaan.</li>
<li>4. Jumlah maksimal pinjam 1 buku / anggota.</li>
<li>5. Lama peminjaman buku maksimal 7 hari.</li>
<li>6. Terlambat mengembalikan buku dikenai denda Rp. 500,00 /hari.</li>
<li>7. Merusakkan atau menghilangkan buku wajib mengganti dengan buku yang sama.</li>
</ul>";
$sysconf['print']['membercard']['rules_font_size'] = "8";

// address
$sysconf['print']['membercard']['address'] = 'My Library<br />website: http://slims.web.id, email : librarian@slims.web.id';
$sysconf['print']['membercard']['address_font_size'] = "7";
$sysconf['print']['membercard']['address_left'] = "";
$sysconf['print']['membercard']['address_top'] = "";

// member card print settings
/* measurement in cm */
$sysconf['print']['membercard_1']['page_margin'] = 0;
$sysconf['print']['membercard_1']['items_margin'] = 0.1;
$sysconf['print']['membercard_1']['items_per_row'] = 1; //

// by Jushadi Arman Saz
/* measurement in cm*/
$sysconf['print']['membercard_1']['factor'] = "37.795275591"; //cm to px

// Items Settings
// change to 0 if dont want to use selected items
$sysconf['print']['membercard_1']['include_id_label'] = 1; // no anggota
$sysconf['print']['membercard_1']['include_name_label'] = 1; // nama anggota
$sysconf['print']['membercard_1']['include_member_type_name'] = 0; // tipe anggota
$sysconf['print']['membercard_1']['include_inst_label'] = 0; // institusi
$sysconf['print']['membercard_1']['include_email_label'] = 1; // email
$sysconf['print']['membercard_1']['include_address_label'] = 1; // alamat
$sysconf['print']['membercard_1']['include_phone'] = 1; // telpon
$sysconf['print']['membercard_1']['include_barcode_label'] = 1; // barcode
$sysconf['print']['membercard_1']['include_expired_label'] = 1; // expired

// Cardbox Settings
$sysconf['print']['membercard_1']['box_width'] = 8.5;
$sysconf['print']['membercard_1']['box_height'] = 5.5;
$sysconf['print']['membercard_1']['front_side_image'] = 'smart1.jpg';
$sysconf['print']['membercard_1']['back_side_image'] = 'smart3.jpg';

// Photo Settings
$sysconf['print']['membercard_1']['photo_left'] = "";
$sysconf['print']['membercard_1']['photo_top'] = "";
$sysconf['print']['membercard_1']['photo_width'] = 2;
$sysconf['print']['membercard_1']['photo_height'] = 2.2;

// Rules
$sysconf['print']['membercard_1']['rules'] = "<ul><li>1. Kartu ini berfungsi sebagai Kartu Pelajar & Kartu Anggota Perpustakaan</li>
<li>2. Kartu anggota perpustakaan ini hanya bisa digunakan oleh pemilik kartu.</li>
<li>3. Kartu ini harap dibawa saat ke perpustakaan.</li>
<li>4. Jumlah maksimal pinjam 1 buku / anggota.</li>
<li>5. Lama peminjaman buku maksimal 7 hari.</li>
<li>6. Terlambat mengembalikan buku dikenai denda Rp. 500,00 /hari.</li>
<li>7. Merusakkan atau menghilangkan buku wajib mengganti dengan buku yang sama</li></ul>";
$sysconf['print']['membercard_1']['rules_font_size'] = "8";

// Barcode Setting
$sysconf['print']['membercard_1']['width'] = 4; // cm
$sysconf['print']['membercard_1']['height'] = 1; // cm
$sysconf['print']['membercard_1']['barcode_left'] = "";
$sysconf['print']['membercard_1']['barcode_top'] = "";
$sysconf['print']['membercard_1']['barcode_width'] = "";
$sysconf['print']['membercard_1']['barcode_height'] = "";

// Logo Setting
$sysconf['print']['membercard_1']['logo'] = "logo.png";
$sysconf['print']['membercard_1']['front_logo_width'] = "";
$sysconf['print']['membercard_1']['front_logo_height'] = "";
$sysconf['print']['membercard_1']['front_logo_left'] = "";
$sysconf['print']['membercard_1']['front_logo_top'] = "";
$sysconf['print']['membercard_1']['back_logo_width'] = "";
$sysconf['print']['membercard_1']['back_logo_height'] = "";
$sysconf['print']['membercard_1']['back_logo_left'] = "";
$sysconf['print']['membercard_1']['back_logo_top'] = "";

// Header Settings
$sysconf['print']['membercard_1']['front_header1_text'] = 'Library Member Card'; // use <br /> tag to make another line
$sysconf['print']['membercard_1']['front_header1_font_size'] = '12';
$sysconf['print']['membercard_1']['front_header2_text'] = 'My Library';
$sysconf['print']['membercard_1']['front_header2_font_size'] = '12';
$sysconf['print']['membercard_1']['back_header1_text'] = 'My Library';
$sysconf['print']['membercard_1']['back_header1_font_size'] = "12";
$sysconf['print']['membercard_1']['back_header2_text'] = 'My Library Full Address and Website';
$sysconf['print']['membercard_1']['back_header2_font_size'] = "5";
$sysconf['print']['membercard_1']['header_color'] = "#0066FF"; //e.g. :#0066FF, green, etc.

//biodata settings
$sysconf['print']['membercard_1']['bio_font_size'] = "11";
$sysconf['print']['membercard_1']['bio_font_weight'] = "bold";
$sysconf['print']['membercard_1']['bio_label_width'] = "100";

// Stamp Settings
$sysconf['print']['membercard_1']['city'] = "City Name";
$sysconf['print']['membercard_1']['title'] = "Library Manager";
$sysconf['print']['membercard_1']['officials'] = "Librarian Name";
$sysconf['print']['membercard_1']['officials_id'] = "Librarian ID";
$sysconf['print']['membercard_1']['stamp_file'] = "stamp.png"; // stamp image, use transparent image
$sysconf['print']['membercard_1']['signature_file'] = "signature.png"; // sign picture, use transparent image
$sysconf['print']['membercard_1']['stamp_left'] = "";
$sysconf['print']['membercard_1']['stamp_top'] = "";
$sysconf['print']['membercard_1']['stamp_width'] = "";
$sysconf['print']['membercard_1']['stamp_height'] = "";

//expired
$sysconf['print']['membercard_1']['exp_left'] = "";
$sysconf['print']['membercard_1']['exp_top'] = "";
$sysconf['print']['membercard_1']['exp_width'] = "";
$sysconf['print']['membercard_1']['exp_height'] = "";

// address
$sysconf['print']['membercard_1']['address'] = '';
$sysconf['print']['membercard_1']['address_font_size'] = "7";
$sysconf['print']['membercard_1']['address_left'] = "";
$sysconf['print']['membercard_1']['address_top'] = "";

// desain kartu kedua
$sysconf['print']['membercard_2']['front_side_image'] = 'membercard-c.jpg';
$sysconf['print']['membercard_2']['back_side_image'] = 'membercard-c1.jpg';

// --------------------

// member card print settings untuk desain 3
/* measurement in cm */
$sysconf['print']['membercard_3']['page_margin'] = 0;
$sysconf['print']['membercard_3']['items_margin'] = 0.1;
$sysconf['print']['membercard_3']['items_per_row'] = 1; //

// by Jushadi Arman Saz
/* measurement in cm*/
$sysconf['print']['membercard_3']['factor'] = "37.795275591"; //cm to px

// Items Settings
// change to 0 if dont want to use selected items
$sysconf['print']['membercard_3']['include_id_label'] = 1; // no anggota
$sysconf['print']['membercard_3']['include_name_label'] = 1; // nama anggota
$sysconf['print']['membercard_3']['include_member_type_name'] = 0; // tipe anggota
$sysconf['print']['membercard_3']['include_inst_label'] = 0; // institusi
$sysconf['print']['membercard_3']['include_email_label'] = 1; // email
$sysconf['print']['membercard_3']['include_address_label'] = 1; // alamat
$sysconf['print']['membercard_3']['include_phone'] = 1; // telpon
$sysconf['print']['membercard_3']['include_barcode_label'] = 1; // barcode
$sysconf['print']['membercard_3']['include_expired_label'] = 1; // expired

// Cardbox Settings
$sysconf['print']['membercard_3']['box_width'] = 8.5;
$sysconf['print']['membercard_3']['box_height'] = 5.5;
$sysconf['print']['membercard_3']['front_side_image'] = 'smart1.jpg';
$sysconf['print']['membercard_3']['back_side_image'] = 'smart3.jpg';

// Photo Settings
$sysconf['print']['membercard_3']['photo_left'] = "";
$sysconf['print']['membercard_3']['photo_top'] = "";
$sysconf['print']['membercard_3']['photo_width'] = 2;
$sysconf['print']['membercard_3']['photo_height'] = 2.2;

// Rules
$sysconf['print']['membercard_3']['rules'] = "<ul><li>1. Kartu ini berfungsi sebagai Kartu Pelajar & Kartu Anggota Perpustakaan</li>
<li>2. Kartu anggota perpustakaan ini hanya bisa digunakan oleh pemilik kartu.</li>
<li>3. Kartu ini harap dibawa saat ke perpustakaan.</li>
<li>4. Jumlah maksimal pinjam 1 buku / anggota.</li>
<li>5. Lama peminjaman buku maksimal 7 hari.</li>
<li>6. Terlambat mengembalikan buku dikenai denda Rp. 500,00 /hari.</li>
<li>7. Merusakkan atau menghilangkan buku wajib mengganti dengan buku yang sama</li></ul>";
$sysconf['print']['membercard_3']['rules_font_size'] = "8";

// Barcode Setting
$sysconf['print']['membercard_3']['width'] = 4; // cm
$sysconf['print']['membercard_3']['height'] = 1; // cm
$sysconf['print']['membercard_3']['barcode_left'] = "";
$sysconf['print']['membercard_3']['barcode_top'] = "";
$sysconf['print']['membercard_3']['barcode_width'] = "";
$sysconf['print']['membercard_3']['barcode_height'] = "";

// Logo Setting
$sysconf['print']['membercard_3']['logo'] = "logo.png";
$sysconf['print']['membercard_3']['front_logo_width'] = "";
$sysconf['print']['membercard_3']['front_logo_height'] = "";
$sysconf['print']['membercard_3']['front_logo_left'] = "";
$sysconf['print']['membercard_3']['front_logo_top'] = "";
$sysconf['print']['membercard_3']['back_logo_width'] = "";
$sysconf['print']['membercard_3']['back_logo_height'] = "";
$sysconf['print']['membercard_3']['back_logo_left'] = "";
$sysconf['print']['membercard_3']['back_logo_top'] = "";

// Header Settings
$sysconf['print']['membercard_3']['front_header1_text'] = 'Library Member Card'; // use <br /> tag to make another line
$sysconf['print']['membercard_3']['front_header1_font_size'] = '12';
$sysconf['print']['membercard_3']['front_header2_text'] = 'My Library';
$sysconf['print']['membercard_3']['front_header2_font_size'] = '12';
$sysconf['print']['membercard_3']['back_header1_text'] = 'My Library';
$sysconf['print']['membercard_3']['back_header1_font_size'] = "12";
$sysconf['print']['membercard_3']['back_header2_text'] = 'My Library Full Address and Website';
$sysconf['print']['membercard_3']['back_header2_font_size'] = "5";
$sysconf['print']['membercard_3']['header_color'] = "#0066FF"; //e.g. :#0066FF, green, etc.

//biodata settings
$sysconf['print']['membercard_3']['bio_font_size'] = "11";
$sysconf['print']['membercard_3']['bio_font_weight'] = "bold";
$sysconf['print']['membercard_3']['bio_label_width'] = "100";

//expired
$sysconf['print']['membercard_3']['exp_left'] = "";
$sysconf['print']['membercard_3']['exp_top'] = "";
$sysconf['print']['membercard_3']['exp_width'] = "";
$sysconf['print']['membercard_3']['exp_height'] = "";

// address
$sysconf['print']['membercard_3']['address'] = '';
$sysconf['print']['membercard_3']['address_font_size'] = "7";
$sysconf['print']['membercard_3']['address_left'] = "";
$sysconf['print']['membercard_3']['address_top'] = "";

//------------------

// freeloan letter print settings
// by Drajat Hasan
// Logo Setting
$sysconf['print']['freeloan']['logo_surat'] = "logo.png";
$sysconf['print']['freeloan']['items_per_row'] = 1;
// Header Settings
$sysconf['print']['freeloan']['header1_text'] = 'SMP 1 Jekulo'; // Name of your University or School or Your Institution. use <br /> tag to make another line
$sysconf['print']['freeloan']['header2_text'] = 'Faculty'; // Name of your faculty or your division
$sysconf['print']['freeloan']['header3_text'] = 'Your Major'; // E.g. Computer Engginer, Medical, etc. use <br /> tag to make another line
$sysconf['print']['freeloan']['header4_text'] = 'Jl. Kudus Pati , Kec Jekulo , Kab. Kudus '; // Address of your University or School or Your division
$sysconf['print']['freeloan']['header5_text'] = 'Phone Number'; // Phone Number 
// Content
$sysconf['print']['freeloan']['caption_letter'] = 'Surat Bebas Pustaka';
$sysconf['print']['freeloan']['declare_letter'] = '';
$sysconf['print']['freeloan']['result_letter'] = '';
$sysconf['print']['freeloan']['number_format'] = '/Perp/'; // /Perp/NamaInisialPerpustakaan
$sysconf['print']['freeloan']['institute'] = '';
$sysconf['print']['freeloan']['period'] = '';
$sysconf['print']['freeloan']['year'] = date("Y");
// Head Library Signature
//$sysconf['print']['freeloan']['date'] = date("D")."".$sysconf['array_ina_month_format'][$sysconf['month']];
$sysconf['print']['freeloan']['city'] = "Kudus";
$sysconf['print']['freeloan']['division_of_signature'] = 'Kepala Perpustakaan';
$sysconf['print']['freeloan']['name_of_signature'] = 'Joko Truz';
$sysconf['print']['freeloan']['id_of_signature'] = 'NIP. 1988080902188';

