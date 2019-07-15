
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>REST API Ver 1.0</title>
		<style type="text/css">
			::selection { background-color: #E13300; color: white; }
			::-moz-selection { background-color: #E13300; color: white; }
			body {
				background-color: #fff;
				margin: 40px;
				font: 13px/20px normal Helvetica, Arial, sans-serif;
				color: #4F5155;
			}
			a {
				color: #003399;
				background-color: transparent;
				font-weight: normal;
			}
			h1 {
				color: #444;
				background-color: transparent;
				border-bottom: 1px solid #D0D0D0;
				font-size: 19px;
				font-weight: normal;
				margin: 0 0 14px 0;
				padding: 14px 15px 10px 15px;
			}

			code {
				font-family: Consolas, Monaco, Courier New, Courier, monospace;
				font-size: 12px;
				background-color: #f9f9f9;
				border: 1px solid #D0D0D0;
				color: #002166;
				display: block;
				margin: 14px 0 14px 0;
				padding: 12px 10px 12px 10px;
			}
			#body {
				margin: 0 15px 0 15px;
			}
			p.footer {
				text-align: right;
				font-size: 11px;
				border-top: 1px solid #D0D0D0;
				line-height: 32px;
				padding: 0 10px 0 10px;
				margin: 20px 0 0 0;
			}
			#container {
				margin: 10px;
				border: 1px solid #D0D0D0;
				box-shadow: 0 0 8px #D0D0D0;
			}

			span {
				color:red;
			}

			h1 
			{
				color: #444;
				background-color: transparent;
				font-size: 19px;
				font-weight: normal;
				margin: 0 0 14px 0;
				padding: 14px 15px 10px 15px;
			}
		</style>
	</head>
	<body>

		<div id="container">
			<h1>WHEEL POWER REST API Ver 1.0</h1><h3 style="margin-left: 12px;">All red fields has mandatory</h3>
		<div id="body">
			<!--Authentication Start-->
			<p>Authentication</p>
			<code>
				<table border="1" width="100%"  style="border-collapse:collapse;border-color:blue;">
				    <tr>
						<td colspan="4"><strong>Login API</strong></td>
					</tr>
					<tr>
						<td width="30%"><strong>End Point</strong></td>
						<td width="30%"><strong>Parameters</strong></td>
						<td width="35%"><strong>Description</strong></td>
						<td width="5%"><strong>Type</strong></td>
					</tr>
					<tr>
						<td>
							<a href="<?php echo base_url();?>login/submit">wheel_power_api/index.php/login/loginUser</a>
						</td>
						<td><span>username<span>,<span>password</span></td>
						<td><div></div></td>
						<td>POST</td>
					</tr>
				</table>

			</code>
			
			<!--Authentication End-->
		</div>
		
		<div id="body">
			<!--Common Start-->
			<p>Common</p>
			<code>
				<table border="1" width="100%"  style="border-collapse:collapse;border-color:blue;">
					<tr>
						<td colspan="4"><strong>Common API</strong></td>
					</tr>
					<tr>
						<td width="30%"><strong>End Point</strong></td>
						<td width="30%"><strong>Parameters</strong></td>
						<td width="35%"><strong>Description</strong></td>
						<td width="5%"><strong>Type</strong></td>
					</tr>
					
					<tr>
						<td>
							<a href="<?php echo base_url();?>manufacturers/get_countries">wheel_power_api/index.php/manufacturers/get_countries</a>
						</td>
						<td></td>
						<td><div>Fetch Countries Lists</div></td>
						<td>GET</td>
					</tr>
					
					<tr>
						<td>
							<a href="<?php echo base_url();?>manufacturers/get_state">wheel_power_api/index.php/manufacturers/get_state</a>
						</td>
						<td><span></span></td>
						<td><div>countries should be india</div></td>
						<td>GET</td>
					</tr>
					
					
					<tr>
						<td>
							<a href="<?php echo base_url();?>manufacturers/get_district">wheel_power_api/index.php/manufacturers/get_district</a>
						</td>
						<td><span>state</span></td>
						<td><div>countries should be india</div></td>
						<td>POST</td>
					</tr>
					<tr>
						<td>
							<a href="<?php echo base_url();?>manufacturers/get_city">wheel_power_api/index.php/manufacturers/get_city</a>
						</td>
						<td><span>state</span>,<span>district</span></td>
						<td><div>countries should be india</div></td>
						<td>POST</td>
					</tr>
					<tr>
						<td>
							<a href="<?php echo base_url();?>manufacturers/get_pincode">wheel_power_api/index.php/manufacturers/get_pincode</a>
						</td>
						<td><span>state</span>,<span>district</span></td>
						<td><div>countries should be india</div></td>
						<td>POST</td>
					</tr>

				</table>

			</code>
			
			<!--Common End-->
		</div>
		
		
		<div id="body">
			<!--Manufacturer Start-->
			<p>Product</p>
			<code>
			    <table  border="1" width="100%"  style="border-collapse:collapse;border-color:blue;">
			        <tr>
						<td colspan="4"><strong>Product's API</strong></td>
					</tr>
			        <tr>
						<td width="30%"><strong>End Point</strong></td>
						<td width="30%"><strong>Parameters</strong></td>
						<td width="35%"><strong>Description</strong></td>
						<td width="5%"><strong>Type</strong></td>
					</tr>
			        <tr>
						<td>
							<a href="<?php echo base_url();?>/products/get">wheel_power_api/index.php/products/get</a>
						</td>
						<td>----</td>
						<td><div></div></td>
						<td>GET</td>
					</tr>
					<tr>
						<td>
							<a href="<?php echo base_url();?>/products/edit">wheel_power_api/index.php/products/edit</a>
						</td>
						<td><span>id</span></td>
						<td><div></div></td>
						<td>POST</td>
					</tr>
					<tr>
						<td>
							<a href="<?php echo base_url();?>/products/store">wheel_power_api/index.php/products/store</a>
						</td>
						<td>----</td>
						<td><div></div></td>
						<td>GET</td>
					</tr>
					<tr>
						<td>
							<a href="<?php echo base_url();?>products/update">wheel_power_api/index.php/products/update</a>
						</td>
						<td>----</td>
						<td><div></div></td>
						<td>GET</td>
					</tr>
					<tr>
						<td>
							<a href="<?php echo base_url();?>products/delete">wheel_power_api/index.php/products/delete</a>
						</td>
						<td><span>id</span></td>
						<td><div>Delete Product</div></td>
						<td>POST</td>
					</tr>
					<tr>
					    <td>
					        <a href="<?php echo base_url();?>products/get_product_category">wheel_power_api/index.php/products/get_product_category</a>					
					   </td>
					   <td>---</td>
					   <td>fetch brand</td>
                       <td>GET</td>	
					</tr>
					<tr>
					    <td>
					        <a href="<?php echo base_url();?>products/get_product_sub_category">wheel_power_api/index.php/products/get_product_sub_category</a>					
					   </td>
					   <td>---</td>
					   <td>fetch sub category</td>
                       <td>GET</td>	
					</tr>
					<tr>
					    <td>
					        <a href="<?php echo base_url();?>products/update_attribute">wheel_power_api/index.php/products/get_product_brand</a>					
					   </td>
					   <td>---</td>
					   <td>fetch brand</td>
                       <td>GET</td>	
					</tr>
					<tr>
					   <td>
					        <a href="<?php echo base_url();?>products/get_product_brand">wheel_power_api/index.php/products/update_attribute</a>					
					   </td>
					   <td><span>product_id</span> ,<span>attribute_name</span>, <span>value</span><br>like {"product_id":"102","attribute_name":"min_qty","value":"25"} </td>
					   <td>It can Update each field of product</td>
                       <td>POST</td>	
					</tr>
					<tr>
					   <td>
					        <a href="<?php echo base_url();?>products/get_product_brand">wheel_power_api/index.php/products/add_feature_value</a>					
					   </td>
					   <td><span>product_id</span> ,<span>type</span>, <span>value</span><br>like {"product_id":"102","type":"color","value":"magenta"} </td>
					   <td>It can add value of existed feature type during product add</td>
                       <td>POST</td>	
					</tr>
					<tr>
					   <td>
					        <a href="<?php echo base_url();?>products/remove_feature_value">wheel_power_api/index.php/products/remove_feature_value</a>					
					   </td>
					   <td><span>product feature table id<br>like "102" </td>
					   <td>It can remove value of existed feature type added during product add</td>
                       <td>POST</td>	
					</tr>
					<tr>
					   <td>
					        <a href="<?php echo base_url();?>products/remove_feature_value">wheel_power_api/index.php/products/delete_image</a>					
					   </td>
					   <td><span>product imagetable id<br>like "102" </td>
					   <td>It can remove image of existed Product's Image added during product add</td>
                       <td>POST</td>	
					</tr>
					</table>
	        </code>
	    </div>
		
		<div id="body">
			<!--Manufacturer Start-->
			<p>Manufacturer</p>
			<code>
			    <table  border="1" width="100%"  style="border-collapse:collapse;border-color:blue;">
			        <tr>
						<td colspan="4"><strong>Manufacturer's API </strong></td>
					</tr>
			        <tr>
						<td width="30%"><strong>End Point</strong></td>
						<td width="30%"><strong>Parameters</strong></td>
						<td width="35%"><strong>Description</strong></td>
						<td width="5%"><strong>Type</strong></td>
					</tr>
					<tr>
						<td>
							<a href="<?php echo base_url();?>/Manufacturer/get">wheel_power_api/index.php/Manufacturer/get</a>
						</td>
						<td>----</td>
						<td><div></div></td>
						<td>GET</td>
					</tr>
					<tr>
						<td>
							<a href="<?php echo base_url();?>Manufacturer/edit">wheel_power_api/index.php/Manufacturer/edit</a>
						</td>
						<td>----</td>
						<td><div></div></td>
						<td>GET</td>
					</tr>
					<tr>
						<td>
							<a href="<?php echo base_url();?>Manufacturer/store">wheel_power_api/index.php/Manufacturer/store</a>
						</td>
						<td>----</td>
						<td><div></div></td>
						<td>GET</td>
					</tr>
					<tr>
						<td>
							<a href="<?php echo base_url();?>Manufacturer/update">wheel_power_api/index.php/Manufacturer/update</a>
						</td>
						<td>----</td>
						<td><div></div></td>
						<td>GET</td>
					</tr>
					<tr>
						<td>
							<a href="<?php echo base_url();?>Manufacturer/delete">wheel_power_api/index.php/Manufacturer/delete</a>
						</td>
						<td>----</td>
						<td><div></div></td>
						<td>GET</td>
					</tr>
				</table> 
		    </code>
	      </div>
	      <div id="body">
			<!--Stock Start-->
			<p>Stock</p>
			<code>
			    <table  border="1" width="100%"  style="border-collapse:collapse;border-color:blue;">
			        <tr>
						<td colspan="4"><strong>Stock's API </strong></td>
					</tr>
			        <tr>
						<td width="30%"><strong>End Point</strong></td>
						<td width="30%"><strong>Parameters</strong></td>
						<td width="35%"><strong>Description</strong></td>
						<td width="5%"><strong>Type</strong></td>
					</tr>
					<tr>
						<td>
							<a href="<?php echo base_url();?>login/submit">wheel_power_api/index.php/stock/get</a>
						</td>
						<td>----</td>
						<td><div></div></td>
						<td>GET</td>
					</tr>
					<tr>
						<td>
							<a href="<?php echo base_url();?>stock/edit">wheel_power_api/index.php/stock/edit</a>
						</td>
						<td>----</td>
						<td><div></div></td>
						<td>GET</td>
					</tr>
					<tr>
						<td>
							<a href="<?php echo base_url();?>stock/store">wheel_power_api/index.php/stock/store</a>
						</td>
						<td>----</td>
						<td><div></div></td>
						<td>GET</td>
					</tr>
					<tr>
						<td>
							<a href="<?php echo base_url();?>stock/update">wheel_power_api/index.php/stock/update</a>
						</td>
						<td>----</td>
						<td><div></div></td>
						<td>GET</td>
					</tr>
					<tr>
						<td>
							<a href="<?php echo base_url();?>stock/delete">wheel_power_api/index.php/stock/delete</a>
						</td>
						<td>----</td>
						<td><div></div></td>
						<td>GET</td>
					</tr>
					<tr>
						<td>
							<a href="<?php echo base_url();?>stock/get_ware_house">wheel_power_api/index.php/stock/get_ware_house</a>
						</td>
						<td>----</td>
						<td><div>fetch all existing warehouses</div></td>
						<td>GET</td>
					</tr>
				</table> 
		    </code>
	      </div>
  	      <div id="body">
			<!--Stock  Shiftt-->
			<p>Stock Shift</p>
			<code>
			    <table  border="1" width="100%"  style="border-collapse:collapse;border-color:blue;">
			        <tr>
						<td colspan="4"><strong>Stock Shift's API </strong></td>
					</tr>
			        <tr>
						<td width="30%"><strong>End Point</strong></td>
						<td width="30%"><strong>Parameters</strong></td>
						<td width="35%"><strong>Description</strong></td>
						<td width="5%"><strong>Type</strong></td>
					</tr>
					<tr>
						<td>
							<a href="<?php echo base_url();?>stock/get_stock_shift">wheel_power_api/index.php/stock/get_stock_shift</a>
						</td>
						<td>----</td>
						<td><div></div></td>
						<td>GET</td>
					</tr>
					<tr>
						<td>
							<a href="<?php echo base_url();?>stock/edit_stock_shift">wheel_power_api/index.php/stock/edit_stock_shift</a>
						</td>
						<td>----</td>
						<td><div></div></td>
						<td>GET</td>
					</tr>
					<tr>
						<td>
							<a href="<?php echo base_url();?>stock/insert_stock_shift">wheel_power_api/index.php/stock/insert_stock_shift</a>
						</td>
						<td>----</td>
						<td><div></div></td>
						<td>GET</td>
					</tr>
					<tr>
						<td>
							<a href="<?php echo base_url();?>stock/update_stock_shift">wheel_power_api/index.php/stock/update_stock_shift</a>
						</td>
						<td>----</td>
						<td><div></div></td>
						<td>GET</td>
					</tr>
					<tr>
						<td>
							<a href="<?php echo base_url();?>stock/delete_stock_shift">wheel_power_api/index.php/stock/delete_stock_shift</a>
						</td>
						<td>----</td>
						<td><div></div></td>
						<td>GET</td>
					</tr>
					
				</table> 
		    </code>
		    <p class="footer">REST API Ver 1.0. Page rendered in <strong>0.0081</strong> seconds.</p>
	      </div>
	    </div>
	</body>
</html>