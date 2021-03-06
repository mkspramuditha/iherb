<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use http\Params;

/**
 * Product
 *
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductRepository")
 */
class Product
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=500, unique=true)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="product_id", type="integer", unique=true)
     */
    private $productId;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="sku", type="string", length=255, nullable=true)
     */
    private $sku;

    /**
     * @var string
     *
     * @ORM\Column(name="upc", type="string", length=255, nullable=true)
     */
    private $upc;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="string",length=255, nullable=true)
     */
    private $price;

    /**
     * @var string
     *
     * @ORM\Column(name="stock", type="integer", nullable=true)
     */
    private $stock;

    /**
     * @var string
     *
     * @ORM\Column(name="weight", type="string", length=255, nullable=true)
     */
    private $weight;

    /**
     * @var string
     *
     * @ORM\Column(name="brand", type="string", length=255, nullable=true)
     */
    private $brand;

    /**
     * @var string
     *
     * @ORM\Column(name="category", type="string", length=255, nullable=true)
     */
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(name="images", type="text", nullable=true)
     */
    private $images;

    /**
     * @var string
     *
     * @ORM\Column(name="shopify_product_id", type="string", length=255, nullable=true)
     */
    private $shopifyProductId;

    /**
     * @var string
     *
     * @ORM\Column(name="shopify_variant_id", type="string", length=255, nullable=true)
     */
    private $shopifyVariantId;

    /**
     * @var string
     *
     * @ORM\Column(name="last_updated", type="datetime", nullable=true)
     */
    private $lastUpdated;




    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Product
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set productId
     *
     * @param integer $productId
     *
     * @return Product
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;

        return $this;
    }

    /**
     * Get productId
     *
     * @return integer
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Product
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set sku
     *
     * @param string $sku
     *
     * @return Product
     */
    public function setSku($sku)
    {
        $this->sku = $sku;

        return $this;
    }

    /**
     * Get sku
     *
     * @return string
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * Set upc
     *
     * @param string $upc
     *
     * @return Product
     */
    public function setUpc($upc)
    {
        $this->upc = $upc;

        return $this;
    }

    /**
     * Get upc
     *
     * @return string
     */
    public function getUpc()
    {
        return $this->upc;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Product
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set price
     *
     * @param float $price
     *
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set stock
     *
     * @param integer $stock
     *
     * @return Product
     */
    public function setStock($stock)
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * Get stock
     *
     * @return integer
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * Set weight
     *
     * @param string $weight
     *
     * @return Product
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight
     *
     * @return string
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set images
     *
     * @param array $images
     *
     * @return Product
     */
    public function setImages($images)
    {
        $this->images = join(",",$images);
        return $this;
    }

    /**
     * Get images
     *
     * @return array
     */
    public function getImages()
    {
        if($this->images == null){
            return [];
        }
        return explode(",",$this->images);
    }



    /**
     * Set brand
     *
     * @param string $brand
     *
     * @return Product
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * Get brand
     *
     * @return string
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * Set category
     *
     * @param string $category
     *
     * @return Product
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set shopifyProductId
     *
     * @param string $shopifyProductId
     *
     * @return Product
     */
    public function setShopifyProductId($shopifyProductId)
    {
        $this->shopifyProductId = $shopifyProductId;

        return $this;
    }

    /**
     * Get shopifyProductId
     *
     * @return string
     */
    public function getShopifyProductId()
    {
        return $this->shopifyProductId;
    }

    /**
     * Set shopifyVariantId
     *
     * @param string $shopifyVariantId
     *
     * @return Product
     */
    public function setShopifyVariantId($shopifyVariantId)
    {
        $this->shopifyVariantId = $shopifyVariantId;

        return $this;
    }

    /**
     * Get shopifyVariantId
     *
     * @return string
     */
    public function getShopifyVariantId()
    {
        return $this->shopifyVariantId;
    }

    /**
     * Set lastUpdated
     *
     * @param \DateTime $lastUpdated
     *
     * @return Product
     */
    public function setLastUpdated($lastUpdated)
    {
        $this->lastUpdated = $lastUpdated;

        return $this;
    }

    /**
     * Get lastUpdated
     *
     * @return \DateTime
     */
    public function getLastUpdated()
    {
        return $this->lastUpdated;
    }
}
