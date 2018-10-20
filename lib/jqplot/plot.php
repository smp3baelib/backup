<?php
/**
 * jqPlot
 * Pure JavaScript plotting plugin using jQuery
 *
 * Version: @VERSION
 *
 * Copyright (c) 2009-2011 Chris Leonello
 * jqPlot is currently available for use in all personal or commercial projects 
 * under both the MIT (http://www.opensource.org/licenses/mit-license.php) and GPL 
 * version 2.0 (http://www.gnu.org/licenses/gpl-2.0.html) licenses. This means that you can 
 * choose the license that best suits your project and use it accordingly. 
 *
 * Although not required, the author would appreciate an email letting him 
 * know of any substantial use of jqPlot.  You can reach the author at: 
 * chris at jqplot dot com or see http://www.jqplot.com/info.php .
 *
 * If you are feeling kind and generous, consider supporting the project by
 * making a donation at: http://www.jqplot.com/donate.php .
 *
 * sprintf functions contained in jqplot.sprintf.js by Ash Searle:
 *
 *     version 2007.04.27
 *     author Ash Searle
 *     http://hexmen.com/blog/2007/03/printf-sprintf/
 *     http://hexmen.com/js/sprintf.js
 *     The author (Ash Searle) has placed this code in the public domain:
 *     "This code is unrestricted: you are free to use it however you like."
 *
 * included jsDate library by Chris Leonello:
 *
 * Copyright (c) 2010-2011 Chris Leonello
 *
 * jsDate is currently available for use in all personal or commercial projects 
 * under both the MIT and GPL version 2.0 licenses. This means that you can 
 * choose the license that best suits your project and use it accordingly.
 *
 * jsDate borrows many concepts and ideas from the Date Instance 
 * Methods by Ken Snyder along with some parts of Ken's actual code.
 * 
 * Ken's origianl Date Instance Methods and copyright notice:
 * 
 * Ken Snyder (ken d snyder at gmail dot com)
 * 2008-09-10
 * version 2.0.2 (http://kendsnyder.com/sandbox/date/)     
 * Creative Commons Attribution License 3.0 (http://creativecommons.org/licenses/by/3.0/)
 *
 * jqplotToImage function based on Larry Siden's export-jqplot-to-png.js.
 * Larry has generously given permission to adapt his code for inclusion
 * into jqPlot.
 *
 * Larry's original code can be found here:
 *
 * https://github.com/lsiden/export-jqplot-to-png
 * 
 * 
 */

 
require_once("plot_data.php");
$html  ='<link class="include" rel="stylesheet" type="text/css" href="'.SWB.'/lib/jqplot/css/jquery.jqplot.min.css" />'."\n";
$html .='<link rel="stylesheet" type="text/css" href="'.SWB.'/lib/jqplot/css/examples.min.css" />'."\n";
$html .='<link type="text/css" rel="stylesheet" href="'.SWB.'/lib/jqplot/css/shCoreDefault.min.css" />'."\n";
$html .='<link type="text/css" rel="stylesheet" href="'.SWB.'/lib/jqplot/css/shThemejqPlot.min.css" />'."\n";
$html .='<script class="include" type="text/javascript" src="'.SWB.'/lib/jqplot/js/jquery.min.js"></script>'."\n";
$html .='<script type="text/javascript">self.print();</script>'."\n";  
//preview plot  
$html .='<center><div id="chart1" style="margin-top:10px; margin-left:10px;width:'.(!isset($width)?'900':$width).'px; height:'.(!isset($height)?'550':$height).'px;"></div></center>'."\n";
echo $html;

?>

  <script class="code" type="text/javascript">

  $(document).ready(function(){
        $.jqplot.config.enablePlugins = true;
        <?php echo $plot1; ?>;
        var ticks = [<?php echo $x_axis; ?>];       
        plot1 = $.jqplot('chart1',[<?php echo $_sr; ?>], { 
            // Only animate if we're not using excanvas (not in IE 7 or IE 8)..
            //animate: !$.jqplot.use_excanvas,
            title:<?php echo $title; ?>,
            legend:{show:true, location:'nw',fontSize:'13pt',fontFamily:'Tahoma' },
            series:[<?php echo $series; ?>], //data series 
            seriesDefaults:{ renderer:$.jqplot.BarRenderer, pointLabels: { show: true } },
            axes: {
                xaxis: {
                    renderer: $.jqplot.CategoryAxisRenderer,
                    ticks: ticks
                }
            },
            highlighter: { show: true }
        });

    });</script>


<script type="text/javascript" src="<?php echo SWB; ?>/lib/jqplot/js/shCore.min.js"></script>
<script type="text/javascript" src="<?php echo SWB; ?>/lib/jqplot/js/shBrushJScript.min.js"></script>
<script type="text/javascript" src="<?php echo SWB; ?>/lib/jqplot/js/shBrushXml.min.js"></script>
<script class="include" type="text/javascript" src="<?php echo SWB; ?>/lib/jqplot/js/jquery.jqplot.min.js"></script>
<script class="include" type="text/javascript" src="<?php echo SWB; ?>/lib/jqplot/js/jquery.jqplot.min.js"></script>
<script class="include" type="text/javascript" src="<?php echo SWB; ?>/lib/jqplot/js/jqplot.barRenderer.min.js"></script>
<script class="include" type="text/javascript" src="<?php echo SWB; ?>/lib/jqplot/js/jqplot.pieRenderer.min.js"></script>
<script class="include" type="text/javascript" src="<?php echo SWB; ?>/lib/jqplot/js/jqplot.categoryAxisRenderer.min.js"></script>
<script class="include" type="text/javascript" src="<?php echo SWB; ?>/lib/jqplot/js/jqplot.pointLabels.min.js"></script>

