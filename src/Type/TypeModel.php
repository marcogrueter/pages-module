<?php namespace Anomaly\PagesModule\Type;

use Anomaly\EditorFieldType\EditorFieldType;
use Anomaly\PagesModule\Type\Command\GetTypeStream;
use Anomaly\PagesModule\Type\Contract\TypeInterface;
use Anomaly\Streams\Platform\Model\Pages\PagesTypesEntryModel;
use Anomaly\Streams\Platform\Stream\Contract\StreamInterface;

/**
 * Class TypeModel
 *
 * @link          http://anomaly.is/streams-platform
 * @author        AnomalyLabs, Inc. <hello@anomaly.is>
 * @author        Ryan Thompson <ryan@anomaly.is>
 * @package       Anomaly\PagesModule\Type
 */
class TypeModel extends PagesTypesEntryModel implements TypeInterface
{

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        self::observe(app(substr(__CLASS__, 0, -5) . 'Observer'));

        parent::boot();
    }

    /**
     * Get the name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the slug.
     *
     * @return string
     */
    public function getSlug()
    {
        return str_slug($this->getName(), '_');
    }

    /**
     * Get the description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get the CSS path.
     *
     * @return string
     */
    public function getCssPath()
    {
        /* @var EditorFieldType $css */
        $css = $this->getFieldType('css');

        $css->setEntry($this);

        return $css->getStoragePath();
    }

    /**
     * Get the JS path.
     *
     * @return string
     */
    public function getJsPath()
    {
        /* @var EditorFieldType $js */
        $js = $this->getFieldType('js');

        $js->setEntry($this);

        return $js->getStoragePath();
    }

    /**
     * Get the related entry stream.
     *
     * @return StreamInterface
     */
    public function getEntryStream()
    {
        return $this->dispatch(new GetTypeStream($this));
    }

    /**
     * Get the related entry model name.
     *
     * @return string
     */
    public function getEntryModelName()
    {
        $stream = $this->getEntryStream();

        return $stream->getEntryModelName();
    }
}
