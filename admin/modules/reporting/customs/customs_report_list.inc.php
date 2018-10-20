<?php
/**
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

// be sure that this file not accessed directly
if (INDEX_AUTH != 1) {
    die("can not access this file directly");
}

/* Custom reports list */
$menu[] = array(__('Buku Induk'), MWB.'reporting/customs/buku_indux.php', __('List of bibliographic titles'));
$menu[] = array(__('Laporan Koleksi'), MWB.'reporting/customs/class_recap.php', __('Laporan Koleksi Berdasar Jenjang, Bahasa, GMD'));
$menu[] = array(__('Title List'), MWB.'reporting/customs/titles_list.php', __('List of bibliographic titles'));
$menu[] = array(__('Items Title List'), MWB.'reporting/customs/item_titles_list.php', __('List of collection/items'));
$menu[] = array(__('Items Usage Statistics'), MWB.'reporting/customs/item_usage.php', __('List of Collection/items usage statistic'));
$menu[] = array(__('Perkembangan Koleksi'), MWB.'reporting/customs/procurement_report.php', __('Procurement Report'));
$menu[] = array('Header', __('Anggota & Peminjam'));
$menu[] = array(__('Membership Report'), MWB.'reporting/member_report.php', __('View Membership Report'));
$menu[] = array(__('Member List'), MWB.'reporting/customs/member_list.php', __('List of library member/patron'));
$menu[] = array(__('Anggota Ter'), MWB.'reporting/customs/member_report_ter.php', __('Anggota Ter'));
$menu[] = array('Header', __('Peminjam'));
$menu[] = array(__('Grafik Peminjam'), MWB.'reporting/customs/grafik-peminjam.php', __('Grafik Peminjam'));
$menu[] = array(__('Loan Report'), MWB.'reporting/loan_report.php', __('View Library Loan Report'));
$menu[] = array(__('Loan History'), MWB.'reporting/customs/loan_history.php', __('Loan History Overview'));
$menu[] = array(__('Loan List by Member'), MWB.'reporting/customs/member_loan_list.php', __('List of loan by each member'));
$menu[] = array(__('Loans by Classification'), MWB.'reporting/customs/loan_by_class.php', __('Loan statistic by classification'));
$menu[] = array(__('Peminjaman Kelas Utama'), MWB.'reporting/customs/loan_by_mainclass.php', __('Peminjaman Kelas Utama'));
$menu[] = array(__('Due Date Warning'), MWB.'reporting/customs/due_date_warning.php', __('Loan Due Date Warnings'));
$menu[] = array(__('Overdued List'), MWB.'reporting/customs/overdued_list.php', __('View Members Having Overdues'));
$menu[] = array('Header', __('Pengunjung'));
$menu[] = array(__('Grafik Pengunjung'), MWB.'reporting/customs/visitor_report_1.php', __('Grafik Pengunjung'));
$menu[] = array(__('Total Pengunjung'), MWB.'reporting/customs/grafik_pengunjung.php', __('Total Pengunjung'));
// $menu[] = array(__('Visitor Statistic (by Day)'), MWB.'reporting/customs/visitor_report_day.php', __('Visitor Statistic (by Day)'));
$menu[] = array(__('Laporan Kunjungan Harian Unik'), MWB.'reporting/customs/visitor_report_day_1.php', __('Laporan Kunjungan Harian Unik'));
$menu[] = array(__('Visitor List'), MWB.'reporting/customs/visitor_list.php', __('Visitor List'));
$menu[] = array('Header', __('Denda & Pegawai'));
$menu[] = array(__('Staff Activity'), MWB.'reporting/customs/staff_act.php', __('Staff activity log recapitulation'));
$menu[] = array(__('Fines Report'), MWB.'reporting/customs/fines_report.php', __('Fines Report'));
