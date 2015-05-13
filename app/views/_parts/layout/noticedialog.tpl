{* ダイアログ *}

<div id="az_notice_dialog" style="display:none;">
	<ul>
{if isset($dialogMessages) && count($dialogMessages)}
{foreach $dialogMessages as $v}
		<li>{$v}</li>
{/foreach}
{/if}
	</ul>
</div>

{literal}
<script type="text/javascript">
var NoticeDialog = null;

$(function(){
	//初期化
	NoticeDialog = $("#az_notice_dialog");

	NoticeDialog.dialog({
		autoOpen: false,
		title: 'メッセージ',
		modal: true,
		width:600,
		minWidth:600,
		height: 'auto',
		resizable:false,
		buttons: {
			"閉じる": function(){
				$(this).dialog('close');
			}
		}
	});

	//表示メソッド
	NoticeDialog.appear = function(message){
		if(message !== undefined)
		{
			if(typeof(message) == 'string')
				message = message.split("\n");
			if($.isArray(message))
			{
				NoticeDialog.find('li').remove();
				for(var i=0; i<message.length; i++)
					NoticeDialog.find('ul').append($('<li />').text(message[i]));
			}
		}

		NoticeDialog.dialog('open');
	};

	//IEやchromeでスクロールした状態でリロードするとスクロール前に表示されてしまうので画面外に飛んでしまう。
	//ので一度だけイベントを発火して位置を合わせる
	$(window).one('scroll', function(){
		NoticeDialog.dialog('option', 'position', ['center','center']);
	});

	//初期動作
	if(NoticeDialog.find('li').length > 0)
		NoticeDialog.appear();
});
</script>
{/literal}
