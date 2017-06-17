<?php

namespace Vidal\MainBundle\Market;

class MarketParser2{
    protected $url = 'http://vidal:3L29y4@smacs.ru/exchange/price';
    protected $drugUrl = 'http://www.piluli.ru/product';
    protected $cachetime = 6000;
    protected $cachefile = 'cached2.xml';
    protected $xml;
    protected $arUrl;

    /** ���������� XML */
    public function __construct($masUrl){

        $this->cachefile = dirname(dirname(__FILE__)).'/../../../../upload_vidal/'.$this->cachefile;

        $this->arUrl = $masUrl;
        if (file_exists($this->cachefile) && time() - $this->cachetime < filemtime($this->cachefile)) {
            $this->getCache();
        }else{
            $this->loadXml();
            $this->setCache();
        }
    }

    /** ��������� XML �� ��� */
    public function loadXml(){
        $this->xml = simplexml_load_file($this->url);
    }

    /** ���� �������� � $xml */
    public function findDrug($name){
        $elems = $this->xml->xpath("product[contains(concat(' ', name, ' '), ' $name ')]");
        $arr = array();
        $url = '';
        foreach ($elems as $elem){
            if ( isset($this->arUrl["$elem->code"]) ){
                $url =  $this->arUrl["$elem->code"] ;
                $arr[] = array(
                    'id' => $elem->code,
                    'manufacturer' => $elem->manufacturer,
                    'name' => $elem->name,
                    'price' => $elem->price,
                    'quantity' => $elem->quantity,
                    'url'   => $url,
                );
            }
        }
        return $arr;
    }

    /** �������� �� ���� */
    public function getCache(){
        $this->xml = simplexml_load_file($this->cachefile);
        #$this->xml = new SimpleXMLElement($file);
    }

    /** ��������� � ��� */
    public function setCache(){
        $this->xml->asXML($this->cachefile);
        #$cached = fopen($this->cachefile, 'w');
        #fwrite($cached, $xml);
        #fclose($cached);
    }

    public function replace_cyr ($path){
        $search = array ("'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'", "'�'");
        $replace = array ('�', '�', '�', '�', '�', '�', '�', '�;', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�');
        $name = preg_replace ($search,$replace,$path);
        return $this->mb_ucfirst($name);
    }

    public function mb_ucfirst($str, $encoding='UTF-8')
    {
        $str = mb_ereg_replace('^[\ ]+', '', $str);
        $str = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding).
            mb_substr($str, 1, mb_strlen($str), $encoding);
        return $str;
    }

}

include 'piluliCodeUrl.php';

