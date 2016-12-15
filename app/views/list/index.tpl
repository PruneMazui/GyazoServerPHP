<script type="text/javascript">
var client_id = {$client_id|json_encode nofilter};
var path = {$config.path|json_encode nofilter};
</script>

<div class="row image-container">
	<h2>画像一覧</h2>
	
	<div id="images">
		<ul>
			{foreach $images as $image}
			<li data-contenttype="inline" data-href="#{$image.access_key}">
				<img src="{$config.path}image/{$image.access_key}" />
			</li>
			{/foreach}
		</ul>
	</div>
	
	{foreach $images as $image}
	<div id="{$image.access_key}" style="display : none;">
		<div class="txt">
			<h2 class="text-right">{$image.insert_date|date_format:"%Y/%m/%d %H:%M:%S"}</h2>
			<p class="text-right">
				{$image.size|number_format}(Byte)<br>
				{$image.width}px × {$image.height}px
			</p>
			<p class="text-right">
				<a href="#" class="btn btn-danger btn-lg image-delete" data-target="{$image.access_key}">
					<span class="glyphicon glyphicon-trash"></span> 削除
				</a>
			</p>
		</div>
		<div class="img">
			<a href="{$config.path}image/{$image.access_key}" target="_blank">
				<img src="{$config.path}image/{$image.access_key}" />
			</a>
		</div>
	</div>
	{/foreach}
</div>