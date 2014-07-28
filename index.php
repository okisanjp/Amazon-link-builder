<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>AmazonLINK</title>
<style type="text/css">
	.alb-price {
		font-weight:bold;
		color:#c00;
	}
	.alb-mini {
		font-size:11px;
	}
	.alb-micro {
		font-size:1px;
		color:#999;
	}
	.alb-wrapper {
		width:500px;
		border:1px solid #999;
		background:#fefefe;
		margin:16px 0;
		padding:4px;
		border-radius:3px;
	}
	.alb-left {
		width:170px;
		float:left;
	}
	.alb-right {
		width:330px;
		float:left;
	}
	.alb-img {
		border:1px solid #eee;
	}
	.alb-clearfix:after {
	  content: ".";  /* 新しい要素を作る */
	  display: block;  /* ブロックレベル要素に */
	  clear: both;
	  height: 0;
	  visibility: hidden;
	}
	.alb-clearfix {
	  min-height: 1px;
	}
	* html .alb-clearfix {
	  height: 1px;
	  /*¥*//*/
	  height: auto;
	  overflow: hidden;
	  /**/
	}
</style>
</head>
<body>

<?php
require_once('./config.php');
require_once('Services/Amazon.php');

define('RESPONSE_GROUP','Large');

if($_GET['asin']) {
    $asin = htmlspecialchars($_GET['asin']);
}else{
    $asin = "B004INGZAE";
}

$amazon = new Services_Amazon(ACCESS_KEY_ID,SECRET_ACCESS_KEY,ASSOC_ID);

$amazon->setLocale('JP');
$options = array();
$options['ResponseGroup'] = RESPONSE_GROUP;

$xml= $amazon->ItemLookup($asin,$options);
if(PEAR::isError($xml)){
    echo "Amazonの個別商品ページでお使いください。";
}else{
    
    $data = $xml['Item'][0];    
    $price = '<div class="alb-price">' . $data['ItemAttributes']['ListPrice']['FormattedPrice'] . '</div>';
    $release_date = "";
    if(isset($data['ItemAttributes']['ReleaseDate'])){
        $release_date .= '<div class="alb-mini">発売日：' . $data['ItemAttributes']['ReleaseDate'] . '</div>';
    }
    
    $release_extend = "";
    if (isset($data['ItemAttributes']['PublicationDate'])){
        $release_extend  .= '<div class="alb-mini">発行日：'.$data['ItemAttributes']['PublicationDate'];
        if ( isset ($data['ItemAttributes']['Label'])){
            $release_extend  .= '<br />発行元：'.$data['ItemAttributes']['Label'];
    }
    if(is_array($data['ItemAttributes']['Author'])){
        $release_extend  .= '<br />著者：';
        foreach($data['ItemAttributes']['Author'] as $author){
            $release_extend  .= $author . '　　';
        }
    }elseif(isset ($data['ItemAttributes']['Author'])){
        $release_extend  .= '<br />著者：' . $data['ItemAttributes']['Author'];
    }
        $release_extend  .= '</div>';
    }else{
        
        if(isset($data['ItemAttributes']['Manufacturer'])){
            $release_extend  .= '<div class="alb-mini">販売元：' . $data['ItemAttributes']['Manufacturer'] . '</div>';
        }
    }
    
    $now = '<div class="alb-micro">UPDATE:' . date("Y/m/d H:i:s") . '</div>';
    $asin = $data['ASIN'];
    $afflink = $data['DetailPageURL'];
    
    $html = '';
    $html .= '<div class="alb-wrapper">';
    $html .= '<div class="alb-left">';
    $html .= '<a href="' . $afflink . '" target="_blank"><img src="' . $data['MediumImage'] ['URL'] . '" alt="" class="alb-img" /></a>';
    $html .= '</div>';
    $html .= '<div class="alb-right">';
    $html .= '<a href="' . $afflink . '" target="_blank">' . $data['ItemAttributes']['Title'] . '</a>';
    $html .= $price;
    $html .= $release_date;
    $html .= $release_extend;
    $html .= $now;
    $html .= '<br /><a href="' . $afflink . '" target="_blank">→ Amazonで見る</a>';
    $html .= '</div>';
    $html .= '<div class="alb-clearfix"></div>';
    $html .= '</div>';
    $html .= '';
 
    echo $html;
}


?>
 
<?php if(!PEAR::isError($xml)){ ?>
<h2>貼りつけ用コード</h2>
<textarea readonly cols=50 rows=5>
<?php echo $html; ?>
</textarea>
<?php } ?>
</body>
</html>
