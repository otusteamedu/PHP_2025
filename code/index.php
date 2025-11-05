<?php
	$servicesLink = [
		'Redis' => '/redis.php'
	];
?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport"
			  content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
		<meta http-equiv="X-UA-Compatible" content="ie=edge">
		<title>Document</title>
	</head>
	<body>
		<?php if ($servicesLink): ?>
			<ul class="service-list">
				<?php foreach ($servicesLink as $serviceName => $servicesLint ) : ?>
					<li class="service-list__item">
						<a class="service-link"
						   href="<?= $servicesLint ?>">
							<?= $serviceName ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</body>
</html>
