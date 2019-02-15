<?php
require_once 'excelParcer.php';
/**
 * Class Itdelight_Shell_ImportPostsFromExcel
 */
class Itdelight_Shell_ImportPostsFromExcel extends Itdelight_Shell_ExcelParser
{
    /**
     * @var array
     */
    protected $_argname = array();

    /**
     * Itdelight_Shell_ImportPostsFromExcel constructor.
     */
    public function __construct()
    {
        parent::__construct();

        // Time limit to infinity
        set_time_limit(0);

        if ($this->getArg('file')) {
            $this->_argname = array_merge(
                $this->_argname,
                array_map(
                    'trim',
                    explode(',', $this->getArg('file'))
                )
            );
        } else {
            throw new Mage_Core_Exception('Argument --file is required.');
        }
    }
    

    // Shell script point of entry
    /**
     * @throws Exception
     * @throws Mage_Core_Exception
     */
    public function run()
    {
        try {
            $excelData = $this->parseXML($this->getArg('file'));
            $excelData = $excelData[0]->toArray();
            $count = count($excelData);
            $newsCatId = Mage::getModel('blog/cat')->getCollection()->addFilter('identifier', 'news')
                ->getFirstItem()->getId();
            $ruStoreId = Mage::getModel('core/store')->loadConfig('ru')->getId();
            $uaStoreId = Mage::getModel('core/store')->loadConfig('ua')->getId();

            for ($i = 1; $i <= $count; $i++) {
                if (!Mage::getModel('blog/post')->getCollection()
                    ->addFilter('identifier', $excelData[$i][3])->getFirstItem()->getId()) {
                    $post = Mage::getModel('blog/post');

                    $doc = new DOMDocument();
                    $doc->loadHTML(mb_convert_encoding($excelData[$i][4], 'HTML-ENTITIES', 'UTF-8'));

                    $selector = new DOMXPath($doc);

                    $imgTags = $selector->query('//img');

                    $imgUrl = '';
                    $content = $content = str_replace('_x000D_','',$excelData[$i][4]);
                    if ($imgTags[0] != null) {
                        foreach($imgTags as $imgTag) {
                            if (strpos($imgTag->getAttribute('src'), 'images/news') !== false) {
                                if (strpos('2015_06_impression/', $imgTag->getAttribute('src'))) {
                                    $extraDir = '2015_06_impression/';
                                } else {
                                    $extraDir = '';
                                }
                                $imgUrl = str_replace('images/news/'.$extraDir, 'magiccart/blog/', $imgTag->getAttribute('src'));
                                $imgTag->parentNode->removeChild($imgTag);
                                $content = preg_replace("/<\\/?" . 'body' . "(.|\\s)*?>/",'',
                                    $doc->saveHTML($selector->query('//body')[0]));
                                $content = str_replace('_x000D_','',$content);


                                break;
                            }
                        }
                    }

                    $post->setData([
                        'title' => $excelData[$i][2],
                        'image' => $imgUrl,
                        'post_content' => $content,
                        'status' => '1',
                        'created_time' => date('Y-m-d H:m:s',strtotime($excelData[$i][5])),
                        'identifier' => $excelData[$i][3],
                        'user' => 'Admin',
                        'meta_keywords' => $excelData[$i][6],
                        'comments' => '1',
                        'stores' =>array($ruStoreId, $uaStoreId),
                        'cats' =>array($newsCatId)
                    ])->save();
                }
                
            }



        } catch (Mage_Core_Exception $e) {
            throw new Mage_Core_Exception($e->getMessage());
        }

    }


    /**
     * Usage instructions
     * @return string
     */
    public function usageHelp()
    {
        return <<<USAGE
        imports posts from xlsx
Usage:  php -f importPostsFromExcel.php -- [options]
 
  --file <filepath>       path to csv file
 
  help                   This help
 
USAGE;
    }
}

// Instantiate
$shell = new Itdelight_Shell_ImportPostsFromExcel();

// Initiate script
$shell->run();