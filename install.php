<?php
require_once (__DIR__.'/crest.php');

$result = CRest::installApp();

// embedded for placement "placement.php"
$handlerBackUrl = ($_SERVER['HTTPS'] === 'on' || $_SERVER['SERVER_PORT'] === '443' ? 'https' : 'http') . '://'
				. $_SERVER['SERVER_NAME']
				. (in_array($_SERVER['SERVER_PORT'],	['80', '443'], true) ? '' : ':' . $_SERVER['SERVER_PORT'])
				. str_replace($_SERVER['DOCUMENT_ROOT'], '',__DIR__)
				. '/placement.php';

CRest::setLog(['REQUEST' => $_REQUEST], 'installation');

CRest::setLog(['tk-app1' => true], 'installation');

if($result['rest_only'] === false):?>
	<head>
		<script src="//api.bitrix24.com/api/v1/"></script>
		<?php if($result['install'] == true):?>
			<script>
				BX24.init(function(){
					BX24.installFinish();
				});
			</script>
		<?php endif;?>
	</head>
	<body>
	<?php if($result['install'] == true):?>
		installation has been finished
	<?php else:?>
		installation error
	<?php endif;?>
	</body>
<?php endif;