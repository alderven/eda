<?php
require_once "common.php";
?>

		<div class="footer navbar-fixed-bottom row-fluid">
			<div class="navbar-inner">
				<div class="container">
					<div class="row logo-font" style=" border-color: #ccc">
						<div class="text-center">
							<span class="text-muted">
								<span class="glyphicon glyphicon-cutlery"></span>
								Сервис «Еда» © 2015-<?php echo date("Y"); echo ' ' . get_version(); ?>
							</span>
						<div>
					</div>
				</div>
			</div>
		</div>
		
		<script>
			$.fn.extend({
				animateCss: function (animationName) {
					var animationEnd = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
					this.addClass('animated ' + animationName).one(animationEnd, function() {
						$(this).removeClass('animated ' + animationName);
					});
				}
			});
		</script>
	</body>
</html>