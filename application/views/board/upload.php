	
	<? if (Authly::signed()): ?>
	
	<div id="uploader">
		<?=Form::open_for_files('board/upload', 'POST', array('onsubmit' => 'return miniwini.uploadPhoto(this)'))?>

		<?=Form::token()?>

		<div id="upload-container">
			<input type="file" name="photo">
			<input type="submit" value="업로드">
			
			<div id="upload-waiting">업로드 중입니다. 잠시만 기다려 주세요</div>
		</div>

		<?=Form::close()?>
	</div>
	
	<? endif; ?>