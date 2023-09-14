<!DOCTYPE html>
<html>
<head>


<script src="<?php echo base_url('application/third_party/jquery.js')?>" type="text/javascript"></script>
<link href="<?php echo base_url('application/third_party/menu-verticle.css')?>" rel="stylesheet" type="text/css" />
<script src="<?php echo base_url('application/third_party/menu-verticle.js')?>" type="text/javascript"></script>

<!--
<script src="<?php echo base_url(IMGPATH.'jquery.js')?>" type="text/javascript"></script>
<link href="<?php echo base_url(IMGPATH.'menu-verticle.css')?>" rel="stylesheet" type="text/css" />
<script src="<?php echo base_url(IMGPATH.'menu-verticle.js')?>" type="text/javascript"></script>
-->
</head>
<body>

<ul id="menu-v">
<?php
if($_SESSION['loc_name']=='Fort Ashrama'):
    ?>
	<li><a href="<?php echo site_url('party/party')?>">Add/Edit Party</a></li>
	<li><a href="<?php echo site_url('item/item')?>">List/Edit Item</a></li>
	<li><a href="#">Transactions</a>
		<ul class="sub">
			<li><a href="<?php echo site_url('trns_summary/summary')?>">List/Edit Transaction</a></li>
			<li><a href="<?php echo site_url('trns_details/sales_add_details')?>">Add Sales</a></li>
			<li><a href="<?php echo site_url('trns_details/purch_add_details')?>">Add Purchase</a></li>
			<li><a href="<?php echo site_url('trns_summary/trns_search')?>">Search Transaction</a></li>
			<li><a href="<?php echo site_url('trns_details/discountreport')?>">Discount Report</a></li>
		</ul>
	</li>
<?php
else:
?>
    <li><a href="#">Transactions</a>
		<ul class="sub">
			<li><a href="<?php echo site_url('trns_summary/summary')?>">List/Edit Transaction</a></li>
			<li><a href="<?php echo site_url('trns_details/sales_add_details')?>">Add Sales</a></li>
			<!--<li><a href="<?php echo site_url('trns_summary/trns_search')?>">Search Transaction</a></li>-->
		</ul>
    </li>
    
<?php 
endif;
?>   
    
    <li><a href="#">Reports</a>
        <ul class="sub">
            <li><a href="<?php echo site_url('Reports/tran_report')?>">Non GST Reports</a></li>
<?php
if($_SESSION['loc_name']=='Fort Ashrama'):
            ?>
            <li><a href="<?php echo site_url('Reports/gstreports')?>">GST Reports</a></li>
<?php
endif;
?>
            
       </ul>
    </li>

    <li><a href = '#'>Stock Transfer</a>
        <ul class = "sub">
            <li><a href="<?php echo site_url('Trnf_summary/summary')?>">List Summary</a></li>
            <li><a href="<?php echo site_url('Trnf_details/send')?>">Send Stock</a></li>
            <!--<li><a href="<?php echo site_url('Trnf_details/receive')?>">Receive Stock</a></li>-->
        </ul>
    </li>
	 <?php
	if($_SESSION['loc_name']=='Fort Ashrama'):
	?>  
    <li><a href="<?php echo site_url('Po_summary/summary')?>">Purchase Order</a></li>
        <li><a href="<?php echo site_url('Invent_update/inv_upd')?>">Process Inventory</a></li>
    <li><a href = '#'>Party Ledger</a>
        <ul class = "sub">
            <li><a href="<?php echo site_url('Party_trans/ledger')?>">Ledger</a></li>
            <li><a href="<?php echo site_url('Party_trans/transactions')?>">Party Transactions</a></li>
        </ul>
    </li>
<?php
endif;
?>	
	 <li><a href="<?php echo site_url('Item/printstock')?>">Print Stock</a></li>
	 <?php
	 if($_SESSION['loc_name']=='Extension Counter'):
	 ?>
	 <li><a href="<?php echo site_url('Trns_details/ec')?>">Record Balance</a></li>
	 <?php
	 endif;
	 ?>
	<!--<li><a href="#">Proforma</a>
		<ul class="sub">
			<li><a href="<?php echo site_url('Profo_Summary/summary')?>">Add Proforma</a></li>
            <li><a href="<?php echo site_url('Profo_Summary/balance')?>">View Balance/Print</a></li>
	
		</ul>
        <ul class="sub">
            <li><a href="#">Sub Item 5.1</a></li>
            <li><a href="#">Sub Item 5.2</a>
                <ul class="sub">
                    <li><a href="#521">Vertical Menu 5.2.1</a></li>
                    <li><a href="#522">Vertical Menu 5.2.2</a></li>
                    <li><a href="#523">Vertical Menu 5.2.3</a></li>
                    <li><a href="#524">Vertical Menu 5.2.4</a></li>
                    <li><a href="#525">Vertical Menu 5.2.5</a></li>
                </ul>
            </li>
            <li><a href="#">Sub Item 5.3</a>
                <ul class="sub">
                    <li><a href="#">Sub Item 5.3.1</a></li>
                    <li><a href="#">Sub Item 5.3.2</a></li>
                    <li><a href="#">Sub Item 5.3.3</a></li>
                    <li><a href="#">Sub Item 5.3.4</a></li>
                    <li><a href="#">Sub Item 5.3.5</a></li>
                    <li><a href="#">Sub Item 5.3.6</a></li>
                    <li><a href="#537">Vertical Menus 5.3.7</a></li>
                    <li><a href="#538">Vertical Menus 5.3.8</a></li>
                </ul>
            </li>
        </ul>
    </li>
    <li><a href="<?php echo site_url('Po_Summary/summary')?>">Purchase Order</a></li>
    <li><a href="<?php echo site_url('Stock/stock')?>">Stock</a></li>-->
</ul>
</body>
</html>
