<html>
<head>
	<title>ArtLebedevStudio.RemoteTypograf example</title>
	<style type="text/css">
		nobr
		{
			background-color: #EEF1E5;
		}
	</style>
</head>
<body>
	<?
		$text = stripslashes ($_POST[text]);
		if (!$text) $text = '"Вы все еще кое-как верстаете в "Ворде"? - Тогда мы идем к вам!"';
	?>

	<form method="post">
		<textarea style="width: 600px; height: 300px" name="text"><? echo $text; ?></textarea>
		<p>
			<input type="submit" value="ProcessText" />
		</p>		
		<div>
		<?
			if ($_POST[text])
			{
				include "remotetypograf.php";
				
				$remoteTypograf = new RemoteTypograf();

				$remoteTypograf->htmlEntities();
				$remoteTypograf->br (false);
				$remoteTypograf->p (true);
				$remoteTypograf->nobr (3);
				$remoteTypograf->quotA ('laquo raquo');
				$remoteTypograf->quotB ('bdquo ldquo');

				print $remoteTypograf->processText ($text);
			}
		?>
		</div>
	</form>
</body>
</html>

