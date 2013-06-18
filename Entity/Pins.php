<?php

namespace Zeldanet\PinterestBundle\Entity;

use Symfony\Component\DomCrawler\Crawler;

/**
 * Pins
 */
class Pins
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $uri;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $date;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $image;

    /**
     * @var string
     */
    private $author;

    /**
     * @var string
     */
    private $board;

    /**
     * @var string
     */
    private $source;

    /**
     * @var integer
     */
    private $actions;

    /**
     * @var integer
     */
    private $repins;

    /**
     * @var integer
     */
    private $likes;

    /**
     * @var string
     */
    private $updated;

    /**
     * Set id
     *
     * @param  integer $id
     * @return Pins
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set url
     *
     * @param  string $url
     * @return Pins
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
     * Set uri
     *
     * @param  string $uri
     * @return Pins
     */
    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * Get uri
     *
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Set title
     *
     * @param  string $title
     * @return Pins
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
     * Set date
     *
     * @param  string $date
     * @return Pins
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set description
     *
     * @param  string $description
     * @return Pins
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
     * Set image
     *
     * @param  string $image
     * @return Pins
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set author
     *
     * @param  string $author
     * @return Pins
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set board
     *
     * @param  string $board
     * @return Pins
     */
    public function setBoard($board)
    {
        $this->board = $board;

        return $this;
    }

    /**
     * Get board
     *
     * @return string
     */
    public function getBoard()
    {
        return $this->board;
    }

    /**
     * Set source
     *
     * @param  string $source
     * @return Pins
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set actions
     *
     * @param  integer $actions
     * @return Pins
     */
    public function setActions($actions)
    {
        $this->actions = $actions;

        return $this;
    }

    /**
     * Get actions
     *
     * @return integer
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * Set repins
     *
     * @param  integer $repins
     * @return Pins
     */
    public function setRepins($repins)
    {
        $this->repins = $repins;

        return $this;
    }

    /**
     * Get repins
     *
     * @return integer
     */
    public function getRepins()
    {
        return $this->repins;
    }

    /**
     * Set likes
     *
     * @param  integer $likes
     * @return Pins
     */
    public function setLikes($likes)
    {
        $this->likes = $likes;

        return $this;
    }

    /**
     * Get likes
     *
     * @return integer
     */
    public function getLikes()
    {
        return $this->likes;
    }

    /**
     * Set updated
     *
     * @param  string $updated
     * @return Pins
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return string
     */
    public function getUpdated()
    {
        return $this->updated;
        }

    /**
     * hydrateWithCrawler
     *
     * @void
     */
    public function hydrateWithCrawler( $crawler, $options = NULL )
    {

        // v.2013-05-23;
        $tmp_title = $crawler->filter('head > title')->extract('_text');
        $title = mb_convert_encoding((string) array_shift($tmp_title), "auto", "UTF-8" );

        $tmp_img = $crawler->filter('head > link[rel="image_src"]')->extract('href');
        $image = (string) array_shift($tmp_img);

        $tmp_desc = $crawler->filter('meta[property="og:description"]')->extract('content');
        $description = mb_convert_encoding((string) array_shift($tmp_desc), "auto", "UTF-8" );

        $tmp_repins = $crawler->filter('meta[property="pinterestapp:repins"]')->extract('content');
        $repins = (int) array_shift($tmp_repins);

        $tmp_likes = $crawler->filter('meta[property="pinterestapp:likes"]')->extract('content');
        $likes = (int) array_shift($tmp_likes);

        /*
        * Unused
        $tmp_comments = $crawler->filter('meta[property="pinterestapp:comments"]')->extract('content');
        $comments = (int) array_shift($tmp_comments);
        */

        $tmp_actions = $crawler->filter('meta[property="pinterestapp:actions"]')->extract('content');
        $actions = (int) array_shift($tmp_actions);

        // Hydrate Pin
        $this->setId($options['id']);
        $this->setUrl($options['url']);
        $this->setUri($options['uri']);

        if (isset( $options['user'] )) { $this->setAuthor($options['user']); }
        if (isset( $options['board'] )) { $this->setBoard($options['board']); }
        if (isset( $options['date'] )) { $this->setDate( $options['date'] ); }

        $this->setTitle($title);
        $this->setActions($actions);
        $this->setRepins($repins);
        $this->setLikes($likes);
        $this->setUpdated( date("Y-m-d H:i:s") );

        if( $image != "" ) 			$this->setImage($image);
        if( $description != "" ) 	$this->setDescription($description);

        if ( $tmp_source = $crawler->filter('meta[property="pinterestapp:source"]')->extract('content') ) {
            $source = (string) array_shift($tmp_source);
            $this->setSource($source);
        }

    }
}
