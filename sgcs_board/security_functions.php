<?php
require_once('./htmlpurifier-4.6.0/library/HTMLPurifier.auto.php'); 

function esc_url($url) {

	if ('' == $url) {
		return $url;
	}

	$url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);

	$strip = array('%0d', '%0a', '%0D', '%0A');
	$url = (string) $url;

	$count = 1;
	while ($count) {
		$url = str_replace($strip, '', $url, $count);
	}

	$url = str_replace(';//', '://', $url);

	$url = htmlentities($url);

	$url = str_replace('&amp;', '&#038;', $url);
	$url = str_replace("'", '&#039;', $url);

	if ($url[0] !== '/') {
		// We're only interested in relative links from $_SERVER['PHP_SELF']
		return '';
	} else {
		return $url;
	}
}

function purify_html ($html)
{
	// �⺻ ������ �ҷ��� �� ������ Ŀ���͸���¡�� ���ݴϴ�.
	$config = HTMLPurifier_Config::createDefault();
	$config->set('Attr.EnableID', false);
	$config->set('Attr.DefaultImageAlt', '');
	
	// ���ͳ� �ּҸ� �ڵ����� ��ũ�� �ٲ��ִ� ���
	$config->set('AutoFormat.Linkify', true);
	
	// �̹��� ũ�� ���� ���� (�ѱ����� ���� ���� �����̳� ©��� ȣȯ�� ������ ����)
	$config->set('HTML.MaxImgLength', null);
	$config->set('CSS.MaxImgLength', null);
	
	// �ٸ� ���ڵ� ���� ���δ� Ȯ������ �ʾҽ��ϴ�. EUC-KR�� ��� iconv�� UTF-8 ��ȯ�� ����Ͻô� �� �����ϴ�.
	$config->set('Core.Encoding', 'UTF-8');
	
	// �ʿ信 ���� DOCTYPE �ٲ㾲����.
	$config->set('HTML.Doctype', 'XHTML 1.0 Transitional');
	
	// �÷��� ���� ���
	$config->set('HTML.FlashAllowFullScreen', true);
	$config->set('HTML.SafeEmbed', true);
	$config->set('HTML.SafeIframe', true);
	$config->set('HTML.SafeObject', true);
	$config->set('Output.FlashCompat', true);
	
	// �ֱ� ���� ����ϴ� iframe ������ ���� ���
	$config->set('URI.SafeIframeRegexp', '#^(?:https?:)?//(?:'.implode('|', array(
			'www\\.youtube(?:-nocookie)?\\.com/',
			'maps\\.google\\.com/',
			'player\\.vimeo\\.com/video/',
			'www\\.microsoft\\.com/showcase/video\\.aspx',
			'(?:serviceapi\\.nmv|player\\.music)\\.naver\\.com/',
			'(?:api\\.v|flvs|tvpot|videofarm)\\.daum\\.net/',
			'v\\.nate\\.com/',
			'play\\.mgoon\\.com/',
			'channel\\.pandora\\.tv/',
			'www\\.tagstory\\.com/',
			'play\\.pullbbang\\.com/',
			'tv\\.seoul\\.go\\.kr/',
			'ucc\\.tlatlago\\.com/',
			'vodmall\\.imbc\\.com/',
			'www\\.musicshake\\.com/',
			'www\\.afreeca\\.com/player/Player\\.swf',
			'static\\.plaync\\.co\\.kr/',
			'video\\.interest\\.me/',
			'player\\.mnet\\.com/',
			'sbsplayer\\.sbs\\.co\\.kr/',
			'img\\.lifestyler\\.co\\.kr/',
			'c\\.brightcove\\.com/',
			'www\\.slideshare\\.net/',
	)).')#');
	
	// ������ �����ϰ� ���͸� ���̺귯�� �ʱ�ȭ
	$purifier = new HTMLPurifier($config);
	
	// HTML ���͸� ����
	$html = $purifier->purify($html);
}


## it didn't work, so i don't use it :(
function xss_clean($data)
{
	// Fix &entity\n;
	$data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
	$data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
	$data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
	$data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

	// Remove any attribute starting with "on" or xmlns
	$data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

	// Remove javascript: and vbscript: protocols
	$data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
	$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
	$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

	// Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
	$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
	$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
	$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

	// Remove namespaced elements (we do not need them)
	$data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

	do
	{
		// Remove really unwanted tags
		$old_data = $data;
		$data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
	}
	while ($old_data !== $data);

	// we are done...
	return $data;
}





?>