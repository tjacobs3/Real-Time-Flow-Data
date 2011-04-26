<html>
<head>
  <title>Setup</title>
  <style type="text/css" media="screen">
    label {
      display: inline-block;
      padding-right: 10px;
      text-align: right;
      width: 140px;
    }
  </style>
</head>

<body>
<p>Please make sure <code>create_table.php</code> has the permission to write to the disk.</p>
<form name="input" action="create_table.php" method="post">
<label>Server</label><input type="text" name="server" /><br>
<label>Username</label><input type="text" name="username" /><br>
<label>Password</label><input type="password" name="password" /><br>
<label>Database Name</label><input type="text" name="db_name" /><br>
<label>Port Number</label><input type="text" name="port" /><br>
<label></label><input type="submit" value="Submit" />
</form>
</body>
</html>

