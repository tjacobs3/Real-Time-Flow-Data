
<html>
	<head>
		<title>USGS: Create Plot</title>
	</head>
	<body>

		<form class="" action="<?php 
					include("Mobile_Detect.php");
					$detect = new Mobile_Detect();	
					if($detect->isMobile())
					{
						echo "detailChart.php";
					}
					else
					{
						echo "StreamPlot.php";
					} 
			?>" method="get" name="options" id="options">
			<select class="form-dropdown" style="width:150px" id="location" name="location">
				<option value="U22"> U22 </option>
				<option value="U25 XS 2501"> U25 XS 2501 </option>
				<option value="U35"> U35 </option>
				<option value="D37"> D37 </option>
				<option value="U39"> U39 </option>
				<option value="D41"> D41 </option>
				<option value="D45"> D45 </option>
				<option value="D52"> D52 </option>
				<option value="D57"> D57 </option>
				<option value="D60"> D60 </option>
				<option value="U69"> U69 </option>
				<option value="D75"> D75 </option>
				<option value="D80"> D80 </option>
				<option value="D83"> D83 </option>
				<option value="U84"> U84 </option>
				<option value="U96"> U96 </option>
				<option value="D97"> D97 </option>
				<option value="U98"> U98 </option>
				<option value="101"> 101 </option>
				<option value="104"> 104 </option>
				<option value="108"> 108 </option>
			</select><br />
			
			<input type="checkbox" name="realTimeData" value="true" checked /> Realtime Data<br />
			<input type="checkbox" name="simulatedData" value="true" /> Simulated Data<br />
			<input type="checkbox" name="elevation" value="true" checked /> Show Elevation<br />
			<input type="checkbox" name="discharge" value="true" /> Show Discharge<br />
			
			<input type="submit" value="Create Plot" />			
		</form>
	</body>
</html>