<?php


namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Routing\Annotation\Route;
use Goutte\Client;


class ScrapingController extends DefaultController
{

    /**
     * @Route("/get-urls",name="get_urls")
     */
    public function getProductUrls(){

        $categoryArray = array(
            "supplements",
            "Herbs-Homeopathy",
            "sports-nutrition",
            "bath-personal-care",
            "beauty",
            "Grocery",
            "baby-kids",
            "pets"

        );

        foreach ($categoryArray as $category){

            $productRepo = $this->getRepository('Product');
            $em = $this->getDoctrine()->getManager();
            for($i = 1; $i<2;$i++){
                $client = new Client();
                $client->setHeader("x-requested-with","XMLHttpRequest");
                $crawler = $client->request("GET","https://www.iherb.com/c/".$category."?p=".(string)$i."&noi=192");
                $productUrls = $crawler->filter("div.absolute-link-wrapper > a")->extract(array('href'));
                if(count($productUrls) == 0){
                    break;
                }
                foreach ($productUrls as $productUrl) {
                    $strArray = explode("/",$productUrl);
                    $productId = (integer)end($strArray);
                    $existingProduct = $productRepo->findOneBy(array('productId'=> $productId));
                    if($existingProduct == null){
                        $product = new Product();
                        $product->setUrl($productUrl);
                        $product->setProductId($productId);
                        $em->persist($product);
                    }

                }

                $em->flush();
                var_dump($i);
            }

        }






        var_dump(count($productUrls));exit;
    }

    /**
     * @Route("/test",name="test")
     */
    public function test(){

        $products = $this->getRepository('Product')->findBy(array('title'=>null));
//        $products = $this->getRepository('Product')->findBy(array('id'=>22692));
//        var_dump(count($products));exit;
        foreach ($products as $product){
            try{
                $this->scrapeProduct($product);
            }catch (\Exception $e){
                var_dump($e->getMessage());
            }

        }
        exit;
    }

    private function scrapeProduct(Product $product){
        $client = new Client();
        var_dump($product->getUrl());
        $client->setHeader("x-requested-with","XMLHttpRequest");
        $client->setHeader("user-agent","Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36");
        $crawler = $client->request("GET",$product->getUrl());
        $title = $crawler->filter('h1#name')->text();
        $quantities = $crawler->filter('select[name="qty"] > option');

        $description = $crawler->filter("div.product-overview > div > section > div.inner-content > div.item-row > div.row > div > div.row");

        $descriptionText = "";
        $descriptionText.="<div class='row item-row'>".$description->eq(0)->html()."</div>";
        $descriptionText.="<div class='row item-row'>".$description->eq(1)->html()."</div>";
        $descriptionText.="<div class='row item-row'>".$description->eq(2)->html()."</div>";
        $descriptionText.="<div class='row item-row'>".$description->eq(3)->html()."</div>";

        try{
            $table = $crawler->filter("div.supplement-facts-container")->html();
            $descriptionText.="<div class='row item-row'>".$table."</div>";
        }catch (\Exception $e){
            var_dump($e->getMessage());
        }

        $stockQuantity = 0;

        foreach ($quantities as $quantity){
            $stockQuantity = $quantity->nodeValue;
            var_dump($stockQuantity);
        }

        $stockQuantity = (int) $stockQuantity;

        if($stockQuantity < 5){
            $stockQuantity = 0;
        }

        $brand = $crawler->filter('meta[property="og:brand"]')->attr("content");
        $category = $crawler->filter('meta[itemprop="category"]')->attr("content");


        $images = $crawler->filter("div.thumbnail-container > img");
        $imagesList = array();
        foreach ($images as $image){
            $imagesList[] = str_replace("/t/","/l/",$image->attributes->getNamedItem('src')->value);
        }



        $specs = $crawler->filter("input#modelProperties");


        $price = (string)((float) substr($specs->attr("data-list-price"),1)*2);
        $weight = $specs->attr("data-actual-weight-lb");
        $sku = $specs->attr("data-part-number");
        $description = $crawler->filter("div.product-overview > div > section > div.inner-content")->html();
        $upc = $crawler->filter('span[itemprop="gtin12"]')->text();

        $product->setTitle($title);
        $product->setDescription($descriptionText);
        $product->setSku($sku);
        $product->setUpc($upc);
        $product->setWeight($weight);
        $product->setPrice($price);
        $product->setStock($stockQuantity);
        $product->setBrand($brand);
        $product->setCategory($category);
        $product->setImages($imagesList);

        $this->insert($product);

    }

    /**
     * @Route("/sync", name="sync")
     */
    public function syncAction()
    {
        $products = $this->getRepository('Product')->getProductsToSync(1000);
//        var_dump(count($products));exit;
        foreach ($products as $product) {

            if($product->getStock() == 0 || $product->getTitle() == null || $product->getTitle() == ""){
                continue;
            }

            $productObj = array();
            $productObj['title'] = $product->getTitle();
            $productObj['published'] = true;

            $variantObj = array();
            $images = array();

            $productObj['product_type'] = $product->getCategory();

            $variantObj['price'] = $product->getPrice();
            $variantObj['sku'] = $product->getSku();
            $weight = explode(" ",$product->getWeight());
            $variantObj['weight'] = $weight[0];
            $variantObj['weight_unit'] = 'lb';
            $variantObj['barcode'] = $product->getUpc();
            $variantObj['inventory_quantity'] = $product->getStock();
//            $variantObj['inventory_quantity'] = ;
            $variantObj['inventory_management'] = 'shopify';
            foreach ($product->getImages() as $image){
                $images[] =  array('src'=>$image);
            }


            $variantObj['title'] = $product->getTitle();
            $productObj['vendor'] = $product->getBrand();



            $productObj['body_html'] = $product->getDescription();
            $productObj['variants'] = [$variantObj];
            $productObj['images'] = $images;

            try{
                $this->addProductToShopify($productObj,$product);
            }catch (\Exception $e){
                var_dump($e->getMessage());
            }




        }
        var_dump(count($products));
        exit;

    }

    private function addProductToShopify($product,Product $productObj){

        $curl = curl_init();


        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://f8d419916ee88328f8cf284908c5ee85:cc99e1810cf828f55a9175bcbebe94a6@deep-discount-center.myshopify.com/admin/api/2019-10/products.json",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode(array('product' => $product)),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json"
//                "Authorization: Basic NzdmYWY1Y2M2ZDBiZmE0ODM2NDIxODc5NDcwNWJjMGI6NzNlMDFiYjUyYjdmMzhjYzAyOTRmMDRlOGZkZDYxZjI="
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response, true);
        $productId = $response['product']['id'];
        $variantId = $response['product']['variants'][0]['id'];
        $productObj->setShopifyProductId($productId);
        $productObj->setShopifyVariantId($variantId);
        $this->insert($productObj);
        var_dump($productId);
        var_dump($variantId);

    }

    private function updateProductInShopify($product, $productId){

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://f8d419916ee88328f8cf284908c5ee85:cc99e1810cf828f55a9175bcbebe94a6@deep-discount-center.myshopify.com/admin/api/2019-10/products/".$productId.".json",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS => json_encode(array('product' => $product)),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json"
//                "Authorization: Basic NzdmYWY1Y2M2ZDBiZmE0ODM2NDIxODc5NDcwNWJjMGI6NzNlMDFiYjUyYjdmMzhjYzAyOTRmMDRlOGZkZDYxZjI="
            ),
        ));

        $response = curl_exec($curl);
        var_dump($response);exit;
        curl_close($curl);
        $response = json_decode($response, true);
        $productId = $response['product']['id'];
        $variantId = $response['product']['variants'][0]['id'];
        $product->setShopifyProductId($productId);
        $product->setShopifyVariantId($variantId);
        $this->insert($product);
        var_dump($productId);
        var_dump($variantId);
        exit;
    }
}