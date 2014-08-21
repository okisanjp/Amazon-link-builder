<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>AmazonLINK</title>
</head>
<body>

<?php
require_once('./config.inc');
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
    $price = '<div style="font-weight:bold;color:#c00;">' . $data['ItemAttributes']['ListPrice']['FormattedPrice'] . '</div>';
    $release_date = "";
    if(isset($data['ItemAttributes']['ReleaseDate'])){
        $release_date .= '<div style="font-size:11px;">発売日：' . $data['ItemAttributes']['ReleaseDate'] . '</div>';
    }

    $release_extend = "";
    if (isset($data['ItemAttributes']['PublicationDate'])){
        $release_extend  .= '<div style="font-size:11px;">発行日：'.$data['ItemAttributes']['PublicationDate'];
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
            $release_extend  .= '<div style="font-size:11px;">販売元：' . $data['ItemAttributes']['Manufacturer'] . '</div>';
        }
    }

    $now = '<div style="font-size:11px;color:#999;">UPDATE:' . date("Y/m/d H:i:s") . '</div>';
    $asin = $data['ASIN'];
    $afflink = $data['DetailPageURL'];

    $html = '';
    $html .= '<div style="width:100%;border:1px solid #999;background:#fefefe;margin:16px 0;padding:4px;border-radius:3px;">';
    $html .= '<div style="">';
    $html .= '<a href="' . $afflink . '" target="_blank"><img src="' . $data['MediumImage'] ['URL'] . '" alt="" style="border:1px solid #eee;" /></a>';
    $html .= '</div>';
    $html .= '<div style="">';
    $html .= '<a href="' . $afflink . '" target="_blank">' . $data['ItemAttributes']['Title'] . '</a>';
    $html .= $price;
    $html .= $release_date;
    $html .= $release_extend;
    $html .= $now;
    $html .= '<br /><a href="' . $afflink . '" target="_blank">→ Amazonで見る</a>';
    $html .= '</div>';
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
