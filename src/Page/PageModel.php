<?php namespace Anomaly\PagesModule\Page;

use Anomaly\EditorFieldType\EditorFieldType;
use Anomaly\PagesModule\Page\Contract\PageInterface;
use Anomaly\PagesModule\Type\Contract\TypeInterface;
use Anomaly\Streams\Platform\Entry\Contract\EntryInterface;
use Anomaly\Streams\Platform\Model\EloquentCollection;
use Anomaly\Streams\Platform\Model\Pages\PagesPagesEntryModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;

/**
 * Class PageModel
 *
 * @method        Builder ordered()
 * @link          http://anomaly.is/streams-platform
 * @author        AnomalyLabs, Inc. <hello@anomaly.is>
 * @author        Ryan Thompson <ryan@anomaly.is>
 * @package       Anomaly\PagesModule\Page
 */
class PageModel extends PagesPagesEntryModel implements PageInterface
{

    /**
     * The cache minutes.
     *
     * @var int
     */
    protected $cacheMinutes = 99999;

    /**
     * Always eager load these.
     *
     * @var array
     */
    protected $with = [
        'type'
    ];

    /**
     * The page's content.
     *
     * @var null|string
     */
    protected $content = null;

    /**
     * The page's response.
     *
     * @var null|Response
     */
    protected $response = null;

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        self::observe(app(substr(__CLASS__, 0, -5) . 'Observer'));

        parent::boot();
    }

    /**
     * Return the static prefix.
     *
     * @return string
     */
    public function staticPrefix()
    {
        $path = $this->getSlug();

        if ($parent = $this->getParent()) {
            $path = $parent->staticPrefix() . '/' . $path;
        } elseif ($this->isHome()) {
            return '/';
        }

        return $path;
    }

    /**
     * Return the combined meta title.
     *
     * @return string
     */
    public function metaTitle()
    {
        $metaTitle = $this->getMetaTitle();

        if (!$metaTitle && $type = $this->getType()) {
            $metaTitle = $type->getMetaTitle();
        }

        if (!$metaTitle) {
            $metaTitle = $this->getTitle();
        }

        return $metaTitle;
    }

    /**
     * Return the combined meta keywords.
     *
     * @return string
     */
    public function metaKeywords()
    {
        $metaKeywords = $this->getMetaKeywords();

        if (!$metaKeywords && $type = $this->getType()) {
            $metaKeywords = $type->getMetaKeywords();
        }

        return $metaKeywords;
    }

    /**
     * Return the combined meta description.
     *
     * @return string
     */
    public function metaDescription()
    {
        $metaDescription = $this->getMetaDescription();

        if (!$metaDescription && $type = $this->getType()) {
            $metaDescription = $type->getMetaDescription();
        }

        return $metaDescription;
    }

    /**
     * Get the string ID.
     *
     * @return string
     */
    public function getStrId()
    {
        return $this->str_id;
    }

    /**
     * Get the TTL.
     *
     * @return null|int
     */
    public function getTtl()
    {
        return $this->ttl;
    }

    /**
     * Get the slug.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Get the meta title.
     *
     * @return string
     */
    public function getMetaTitle()
    {
        return $this->meta_title;
    }

    /**
     * Get the meta keywords.
     *
     * @return array
     */
    public function getMetaKeywords()
    {
        return $this->meta_keywords;
    }

    /**
     * Get the meta description.
     *
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->meta_description;
    }

    /**
     * Get the path to the page's type layout.
     *
     * @return string
     */
    public function getLayoutViewPath()
    {
        $type = $this->getType();

        /* @var EditorFieldType $layout */
        $layout = $type->getFieldType('layout');

        return $layout->getViewPath();
    }

    /**
     * Set the live flag.
     *
     * @param $live
     * @return $this
     */
    public function setLive($live)
    {
        $this->live = $live;

        return $this;
    }

    /**
     * Get the live flag.
     *
     * @return bool
     */
    public function isLive()
    {
        return $this->live;
    }

    /**
     * Get the home flag.
     *
     * @return bool
     */
    public function isHome()
    {
        return $this->home;
    }

    /**
     * Get the related parent page.
     *
     * @return null|PageInterface
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Get the related children pages.
     *
     * @return PageCollection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Get the related roles allowed.
     *
     * @return EloquentCollection
     */
    public function getAllowedRoles()
    {
        return $this->allowed_roles;
    }

    /**
     * Get the route suffix.
     *
     * @param null $prefix
     * @return null|string
     */
    public function getRouteSuffix($prefix = null)
    {
        return $this->route_suffix ? $prefix . $this->route_suffix : null;
    }

    /**
     * Get the route constraints.
     *
     * @return null|string
     */
    public function getRouteConstraints()
    {
        return $this->route_constraints;
    }

    /**
     * Get the additional parameters.
     *
     * @return null|string
     */
    public function getAdditionalParameters()
    {
        return $this->additional_parameters;
    }

    /**
     * Get the page type.
     *
     * @return null|TypeInterface
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get the page handler.
     *
     * @return PageHandlerExtension
     */
    public function getPageHandler()
    {
        $type = $this->getType();

        return $type->getPageHandler();
    }

    /**
     * Get the related entry.
     *
     * @return null|EntryInterface
     */
    public function getEntry()
    {
        return $this->entry;
    }

    /**
     * Get the related entry ID.
     *
     * @return null|int
     */
    public function getEntryId()
    {
        return $this->entry_id;
    }

    /**
     * Get the content.
     *
     * @return null|string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set the content.
     *
     * @param $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get the response.
     *
     * @return Response|null
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Set the response.
     *
     * @param $response
     * @return $this
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Return the children pages relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany('Anomaly\PagesModule\Page\PageModel', 'parent_id', 'id');
    }
}
